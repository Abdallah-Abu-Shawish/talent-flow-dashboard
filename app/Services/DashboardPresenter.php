<?php

namespace App\Services;

use App\Repositories\DashboardRepository;
use App\Support\DashboardModules;
use App\Support\SafeDisplay;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

final class DashboardPresenter
{
    public function __construct(
        private DashboardRepository $repository
    ) {}

    public function table(
        string $module,
        array $filters,
        string $token,
        array $scope = []
    ): array {
        $definition = DashboardModules::get($module);

        $paginator = $this->repository->page(
            $module,
            $filters,
            $token,
            $scope
        );

        return [
            'title' => $definition['title'],

            'description' =>
                $definition['description'],

            'columns' =>
                DashboardModules::columns(
                    $definition
                ),

            'rows' =>
                array_map(
                    fn ($row) =>
                        $this->row(
                            $module,
                            $row
                        ),
                    $paginator->items()
                ),

            'paginator' =>
                $paginator,

            'filters' =>
                $filters,

            'filterFields' =>
                $this->filterFields(
                    $module,
                    $definition
                ),

            'createUrl' =>
                $module === 'organizations'
                    ? route(
                        'organizations.create'
                    )
                    : null,

            'tabs' =>
                $this->tabs($module),
        ];
    }

    private function row(
        string $module,
        array $row
    ): array {
        $row['company_name'] =
            $row['company']['name']
            ?? (
                $module === 'interviews'
                && empty($row['company_id'])
                    ? 'Practice interview'
                    : null
            );

        $row['job_title'] =
            $row['job']['title']
            ?? null;

        $row['candidate_name'] =
            $row['candidate']['full_name']
            ?? null;

        $row['full_name'] ??=
            $row['profile']['full_name']
            ?? null;

        $row['company_id'] ??=
            $row['interview']['company_id']
            ?? null;

        $safe = [];

        foreach ($row as $key => $value) {
            if (
                is_scalar($value)
                || $value === null
            ) {
                $safe[$key] =
                    SafeDisplay::text(
                        $value
                    );
            }
        }

        $links = [];

        foreach (
            [
                'company_id' =>
                    'organizations.show',

                'job_id' =>
                    'jobs.show',

                'candidate_id' =>
                    'users.show',

                'user_id' =>
                    'users.show',

                'actor_id' =>
                    'users.show',

                'performed_by' =>
                    'users.show',

                'interview_id' =>
                    'interviews.show',
            ]
            as $key => $route
        ) {
            if (
                ! empty($safe[$key])
                && Str::isUuid(
                    $safe[$key]
                )
            ) {
                $links[$key] =
                    route(
                        $route,
                        $safe[$key]
                    );
            }
        }

        foreach (
            [
                'company_name' =>
                    'company_id',

                'job_title' =>
                    'job_id',

                'candidate_name' =>
                    'candidate_id',

                'full_name' =>
                    'user_id',
            ]
            as $label => $id
        ) {
            if (isset($links[$id])) {
                $links[$label] =
                    $links[$id];
            }
        }

        if (
            in_array(
                $module,
                [
                    'organizations',
                    'users',
                    'jobs',
                    'interviews',
                    'audit',
                ],
                true
            )
            && Str::isUuid(
                $safe['id']
                ?? ''
            )
        ) {
            $safe['_url'] =
                route(
                    $module.'.show',
                    $safe['id']
                );
        }

        return $safe + [
            '_links' => $links,
        ];
    }

    private function filterFields(
        string $module,
        array $definition
    ): array {
        $fields = [];

        if (isset($definition['search'])) {
            $searchFields =
                (array)
                    $definition['search'];

            $fields['q'] = [
                'label' =>
                    $definition[
                        'searchLabel'
                    ]
                    ?? 'Search '
                    .str_replace(
                        '_',
                        ' ',
                        $searchFields[0]
                    ),

                'type' =>
                    'text',
            ];
        }

        if (
            isset(
                $definition[
                    'statusField'
                ]
            )
        ) {
            $fields[
                $definition[
                    'statusField'
                ]
            ] = [
                'label' =>
                    $module === 'users'
                        ? 'Role'
                        : ucfirst(
                            $definition[
                                'statusField'
                            ]
                        ),

                'type' =>
                    'select',

                'options' =>
                    array_combine(
                        $definition[
                            'statuses'
                        ],
                        array_map(
                            fn ($value) =>
                                Str::headline(
                                    $value
                                ),
                            $definition[
                                'statuses'
                            ]
                        )
                    ),
            ];
        }

        /*
         * Keep Users & Access simple.
         */
        if ($module === 'users') {
            return $fields + [
                'sort' => [
                    'label' =>
                        'Sort by',

                    'type' =>
                        'select',

                    'options' =>
                        $definition[
                            'sorts'
                        ],
                ],

                'direction' => [
                    'label' =>
                        'Order',

                    'type' =>
                        'select',

                    'options' => [
                        'desc' =>
                            'Descending',

                        'asc' =>
                            'Ascending',
                    ],
                ],

                'per_page' => [
                    'label' =>
                        'Rows per page',

                    'type' =>
                        'select',

                    'options' => [
                        15 => '15',
                        25 => '25',
                        50 => '50',
                    ],
                ],
            ];
        }

        if (
            isset(
                $definition[
                    'companyField'
                ]
            )
        ) {
            $fields['company_id'] = [
                'label' =>
                    'Organization ID',

                'type' =>
                    'text',
            ];
        }

        if (
            isset(
                $definition[
                    'serviceField'
                ]
            )
        ) {
            $fields['service'] = [
                'label' =>
                    'Exact service name',

                'type' =>
                    'text',
            ];
        }

        $extra = match ($module) {
            'interviews' => [
                'job_id' =>
                    'Job ID',

                'candidate_id' =>
                    'Candidate ID',
            ],

            'usage',
            'security' => [
                'interview_id' =>
                    'Interview ID',
            ],

            'audit' => [
                'record_id' =>
                    'Record ID',

                'actor_id' =>
                    'Actor ID',
            ],

            'commands' => [
                'actor_id' =>
                    'Actor ID',
            ],

            'auth-events' => [
                'actor_id' =>
                    'Actor ID',

                'correlation_id' =>
                    'Correlation ID',
            ],

            'members' => [
                'user_id' =>
                    'User ID',
            ],

            default => [],
        };

        foreach (
            $extra
            as $key => $label
        ) {
            $fields[$key] = [
                'label' => $label,
                'type' => 'text',
            ];
        }

        return $fields + [
            'from' => [
                'label' =>
                    'Created from (UTC)',

                'type' =>
                    'date',
            ],

            'to' => [
                'label' =>
                    'Created through (UTC)',

                'type' =>
                    'date',
            ],

            'sort' => [
                'label' =>
                    'Sort by',

                'type' =>
                    'select',

                'options' =>
                    $definition[
                        'sorts'
                    ],
            ],

            'direction' => [
                'label' =>
                    'Order',

                'type' =>
                    'select',

                'options' => [
                    'desc' =>
                        'Descending',

                    'asc' =>
                        'Ascending',
                ],
            ],

            'per_page' => [
                'label' =>
                    'Rows per page',

                'type' =>
                    'select',

                'options' => [
                    15 => '15',
                    25 => '25',
                    50 => '50',
                ],
            ],
        ];
    }

    private function tabs(
        string $module
    ): array {
        $groups = [
            [
                'audit' => [
                    'Row changes',
                    'audit.index',
                ],

                'commands' => [
                    'Dashboard actions',
                    'commands.index',
                ],
            ],

            [
                'security' => [
                    'Interview integrity',
                    'security.index',
                ],

                'auth-events' => [
                    'Authentication',
                    'auth-events.index',
                ],
            ],

            [
                'usage' => [
                    'Token debits',
                    'usage.index',
                ],

                'credits' => [
                    'Token credits',
                    'credits.index',
                ],
            ],
        ];

        foreach ($groups as $group) {
            if (
                isset(
                    $group[$module]
                )
            ) {
                $tabs = [];

                foreach (
                    $group
                    as $key => [
                        $label,
                        $route,
                    ]
                ) {
                    $tabs[] = [
                        'label' =>
                            $label,

                        'url' =>
                            route($route),

                        'active' =>
                            $key === $module,
                    ];
                }

                return $tabs;
            }
        }

        return [];
    }

    public function detail(
        string $module,
        string $id,
        string $token
    ): array {
        $extra = match ($module) {
            'jobs' =>
                ',description,updated_at',

            'interviews' =>
                ',results_released_at,updated_at',

            'audit' =>
                ',old_data,new_data',

            default =>
                '',
        };

        $raw =
            $this->repository->find(
                $module,
                $id,
                $token,
                $extra
            );

        $row =
            $this->row(
                $module,
                $raw
            );

        $fields = [];

        foreach (
            DashboardModules::get(
                $module
            )['columns']
            as $key => $label
        ) {
            $fields[$label] =
                $row[$key]
                ?? null;
        }

        $fields['Record ID'] =
            $id;

        $sections = [];

        /*
         * =====================================================
         * ORGANIZATIONS
         * =====================================================
         */

        if ($module === 'organizations') {
            $fields['Members'] =
                $this->repository->count(
                    'members',
                    [
                        'company_id' =>
                            $id,
                    ],
                    $token
                );

            $fields['Jobs'] =
                $this->repository->count(
                    'jobs',
                    [
                        'company_id' =>
                            $id,
                    ],
                    $token
                );

            $fields['Interviews'] =
                $this->repository->count(
                    'interviews',
                    [
                        'company_id' =>
                            $id,
                    ],
                    $token
                );

            $sections[] =
                $this->related(
                    'members',
                    [
                        'company_id' =>
                            $id,
                    ],
                    $token
                );

            $sections[] =
                $this->related(
                    'jobs',
                    [
                        'company_id' =>
                            $id,
                    ],
                    $token
                );

            $sections[] =
                $this->related(
                    'usage',
                    [
                        'company_id' =>
                            $id,
                    ],
                    $token
                );
        }

        /*
         * =====================================================
         * USERS
         * =====================================================
         */
        elseif ($module === 'users') {
            /*
             * Public profile information.
             */
            $fields = [
                'Full name' =>
                    $row[
                        'full_name'
                    ]
                    ?? '—',

                'Email' =>
                    $row[
                        'email'
                    ]
                    ?? '—',

                'Phone' =>
                    $row[
                        'phone_number'
                    ]
                    ?? '—',

                'Global role' =>
                    $row[
                        'role'
                    ]
                    ?? '—',
            ];

            /*
             * Supabase Auth information.
             */
            $authUser =
                $this->repository
                    ->authUser($id);

            $appMetadata =
                is_array(
                    $authUser[
                        'app_metadata'
                    ]
                    ?? null
                )
                    ? $authUser[
                        'app_metadata'
                    ]
                    : [];

            /*
             * Get all linked sign-in providers.
             */
            $providers =
                $appMetadata[
                    'providers'
                ]
                ?? [];

            if (
                ! is_array(
                    $providers
                )
            ) {
                $providers = [];
            }

            /*
             * Fallback to identities when
             * providers is unavailable.
             */
            if (
                empty($providers)
                && is_array(
                    $authUser[
                        'identities'
                    ]
                    ?? null
                )
            ) {
                foreach (
                    $authUser[
                        'identities'
                    ]
                    as $identity
                ) {
                    $provider =
                        $identity[
                            'provider'
                        ]
                        ?? null;

                    if (
                        is_string(
                            $provider
                        )
                        && $provider !== ''
                    ) {
                        $providers[] =
                            $provider;
                    }
                }
            }

            /*
             * Final fallback to primary provider.
             */
            if (
                empty($providers)
                && ! empty(
                    $appMetadata[
                        'provider'
                    ]
                )
            ) {
                $providers[] =
                    $appMetadata[
                        'provider'
                    ];
            }

            $providers =
                array_values(
                    array_unique(
                        array_filter(
                            $providers,
                            'is_string'
                        )
                    )
                );

            $providerLabel =
                static function (
                    string $provider
                ): string {
                    return match (
                        strtolower(
                            $provider
                        )
                    ) {
                        'google' =>
                            'Google',

                        'email' =>
                            'Email & Password',

                        'apple' =>
                            'Apple',

                        'github' =>
                            'GitHub',

                        'facebook' =>
                            'Facebook',

                        'azure' =>
                            'Microsoft',

                        'linkedin',
                        'linkedin_oidc' =>
                            'LinkedIn',

                        'phone' =>
                            'Phone',

                        default =>
                            Str::headline(
                                $provider
                            ),
                    };
                };

            $providerLabels =
                array_map(
                    $providerLabel,
                    $providers
                );

            $primaryProvider =
                $appMetadata[
                    'provider'
                ]
                ?? (
                    $providers[0]
                    ?? null
                );

            $fields[
                'Sign-in methods'
            ] =
                ! empty(
                    $providerLabels
                )
                    ? implode(
                        ', ',
                        $providerLabels
                    )
                    : 'Unknown';

            $fields[
                'Primary sign-in'
            ] =
                is_string(
                    $primaryProvider
                )
                && $primaryProvider !== ''
                    ? $providerLabel(
                        $primaryProvider
                    )
                    : 'Unknown';

            $fields[
                'Email confirmed'
            ] =
                ! empty(
                    $authUser[
                        'email_confirmed_at'
                    ]
                )
                    ? 'Yes'
                    : 'No';

            $fields[
                'Last sign in'
            ] =
                $authUser[
                    'last_sign_in_at'
                ]
                ?? 'Never';

            $fields[
                'User ID'
            ] =
                $row['id']
                ?? $id;

            $fields[
                'Created'
            ] =
                $row[
                    'created_at'
                ]
                ?? '—';

            $fields[
                'Updated'
            ] =
                $row[
                    'updated_at'
                ]
                ?? '—';

            /*
             * Related company access.
             */
            $sections[] =
                $this->related(
                    'members',
                    [
                        'user_id' =>
                            $id,
                    ],
                    $token
                );

            /*
             * Related interviews.
             */
            $sections[] =
                $this->related(
                    'interviews',
                    [
                        'candidate_id' =>
                            $id,
                    ],
                    $token
                );

            $sections[] = [
                'title' =>
                    'Access management',

                'body' =>
                    'Global platform access and company memberships are managed separately. '
                    .'Company Admin and Interviewer permissions belong to organization memberships.',
            ];
        }

        /*
         * =====================================================
         * JOBS
         * =====================================================
         */
        elseif ($module === 'jobs') {
            $fields[
                'Organization ID'
            ] =
                $row[
                    'company_id'
                ];

            $fields[
                'Created by'
            ] =
                $row[
                    'created_by'
                ]
                ?? 'Not recorded';

            $fields[
                'Interview count'
            ] =
                $this->repository->count(
                    'interviews',
                    [
                        'job_id' =>
                            $id,
                    ],
                    $token
                );

            $sections[] = [
                'title' =>
                    'Job description',

                'body' =>
                    SafeDisplay::text(
                        $raw[
                            'description'
                        ]
                        ?? ''
                    ),
            ];

            $sections[] =
                $this->related(
                    'interviews',
                    [
                        'job_id' =>
                            $id,
                    ],
                    $token
                );
        }

        /*
         * =====================================================
         * INTERVIEWS
         * =====================================================
         */
        elseif ($module === 'interviews') {
            $fields += [
                'Started (UTC)' =>
                    $row[
                        'started_at'
                    ],

                'Ended (UTC)' =>
                    $row[
                        'ended_at'
                    ],

                'Results released (UTC)' =>
                    $row[
                        'results_released_at'
                    ]
                    ?? null,
            ];

            $evaluation =
                $this->repository
                    ->evaluation(
                        $id,
                        $token
                    );

            $sections[] =
                $evaluation
                    ? [
                        'title' =>
                            'Recorded evaluation',

                        'fields' => [
                            'Technical score' =>
                                $evaluation[
                                    'technical_score'
                                ],

                            'Communication score' =>
                                $evaluation[
                                    'communication_score'
                                ],

                            'Problem-solving score' =>
                                $evaluation[
                                    'problem_solving_score'
                                ],

                            'Recorded at' =>
                                $evaluation[
                                    'created_at'
                                ],
                        ],
                    ]
                    : [
                        'title' =>
                            'Recorded evaluation',

                        'body' =>
                            'No evaluation has been recorded for this interview.',
                    ];

            $sections[] =
                $this->related(
                    'security',
                    [
                        'interview_id' =>
                            $id,
                    ],
                    $token
                );

            $sections[] =
                $this->related(
                    'usage',
                    [
                        'interview_id' =>
                            $id,
                    ],
                    $token
                );

            $sections[] = [
                'title' =>
                    'Session observations',

                'body' =>
                    'Connection heartbeats, provider/model metadata, latency, cost and failure traces are not recorded by the current contract. Transcript and media access require a separate authorized access workflow.',
            ];
        }

        /*
         * =====================================================
         * AUDIT
         * =====================================================
         */
        elseif ($module === 'audit') {
            $fields['Outcome'] =
                'Committed row change';

            $fields[
                'Actor attribution'
            ] =
                $row[
                    'performed_by'
                ]
                ?? 'No user actor recorded (database or backend operation)';

            $sections[] = [
                'title' =>
                    'Before · safe fields',

                'fields' =>
                    SafeDisplay::auditSnapshot(
                        $raw[
                            'old_data'
                        ]
                        ?? null
                    ),
            ];

            $sections[] = [
                'title' =>
                    'After · safe fields',

                'fields' =>
                    SafeDisplay::auditSnapshot(
                        $raw[
                            'new_data'
                        ]
                        ?? null
                    ),
            ];

            if (
                $raw[
                    'table_name'
                ]
                === 'companies'
            ) {
                $sections[] =
                    $this->related(
                        'commands',
                        [
                            'company_id' =>
                                $raw[
                                    'record_id'
                                ],
                        ],
                        $token
                    );
            }
        }

        return [
            'title' =>
                $row['name']
                ?? $row['full_name']
                ?? $row['title']
                ?? (
                    $module ===
                    'interviews'
                        ? 'Interview details'
                        : 'Audit event'
                ),

            'subtitle' =>
                DashboardModules::get(
                    $module
                )['description'],

            'badge' =>
                $row['status']
                ?? $row['plan']
                ?? $row['role']
                ?? $row['action']
                ?? null,

            'fields' =>
                $fields,

            'sections' =>
                $sections,

            'editUrl' =>
                match ($module) {
                    'organizations' =>
                        route(
                            'organizations.edit',
                            $id
                        ),

                    'users' =>
                        route(
                            'users.edit',
                            $id
                        ),

                    default =>
                        null,
                },
        ];
    }

    private function related(
        string $module,
        array $scope,
        string $token
    ): array {
        $table =
            $this->table(
                $module,
                [
                    'page' => 1,
                    'per_page' => 5,
                ] + $scope,
                $token,
                $scope
            );

        $table['filterFields'] =
            [];

        $table['createUrl'] =
            null;

        $table['tabs'] =
            [];

        $table['paginator'] =
            new LengthAwarePaginator(
                $table['rows'],
                count(
                    $table['rows']
                ),
                5,
                1
            );

        $table['collectionUrl'] =
            route(
                $module.'.index',
                $scope
            );

        $table['note'] =
            'Showing up to 5 records. '
            .$table['description'];

        return [
            'title' =>
                $table['title'],

            'table' =>
                $table,
        ];
    }
}