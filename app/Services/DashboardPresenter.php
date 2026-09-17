<?php

namespace App\Services;

use App\Repositories\DashboardRepository;
use App\Support\DashboardModules;
use App\Support\SafeDisplay;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class DashboardPresenter
{
    public function __construct(
        private DashboardRepository $repository
    ) {}


    // =========================================================
    // TABLE
    // =========================================================

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

        $rawRows = $paginator->items();

        $rows = array_map(
            fn ($row) => $this->row(
                $module,
                $row
            ),
            $rawRows
        );


        // =====================================================
        // ACTIVE USER SESSIONS
        // =====================================================

        if ($module === 'users') {
            $userIds = array_values(
                array_filter(
                    array_map(
                        fn ($row) =>
                            $row['id']
                            ?? null,
                        $rawRows
                    ),
                    fn ($id) =>
                        is_string($id)
                        && $id !== ''
                )
            );

            $sessionCounts =
                $this->repository
                    ->userSessionCounts(
                        $userIds,
                        $token
                    );

            foreach ($rows as &$row) {
                $userId =
                    $row['id']
                    ?? null;

                $row['active_sessions'] =
                    isset(
                        $sessionCounts[$userId]
                    )
                        ? (string)
                            $sessionCounts[$userId]
                        : '0';
            }

            unset($row);
        }


        return [
            'title' =>
                $definition['title'],

            'description' =>
                $definition['description'],

            'columns' =>
                DashboardModules::columns(
                    $definition
                ),

            'rows' =>
                $rows,

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
                $this->tabs(
                    $module
                ),
        ];
    }


    // =========================================================
    // NORMALIZE ROW
    // =========================================================

    private function row(
        string $module,
        array $row
    ): array {

        // Organization relation.
        $row['company_name'] =
            $row['company']['name']
            ?? (
                $module === 'interviews'
                && empty(
                    $row['company_id']
                )
                    ? 'Practice interview'
                    : null
            );


        // Interview -> company-specific job posting.
        $row['job_title'] =
            $row['job']['title']
            ?? null;


        // Global job role -> category.
        $row['category_name'] =
            $row['category']['name']
            ?? null;


        // Candidate relation.
        $row['candidate_name'] =
            $row['candidate']['full_name']
            ?? null;


        // Member/profile relation.
        $row['full_name'] ??=
            $row['profile']['full_name']
            ?? null;


        $row['company_id'] ??=
            $row['interview']['company_id']
            ?? null;


        $safe = [];


        foreach ($row as $key => $value) {

            // ================================================
            // BOOLEAN AVAILABILITY
            // ================================================

            if ($key === 'is_active') {
                $safe[$key] =
                    filter_var(
                        $value,
                        FILTER_VALIDATE_BOOLEAN
                    )
                        ? 'Enabled'
                        : 'Disabled';

                continue;
            }


            // ================================================
            // DATE / TIME
            // ================================================

            if (
                is_string($key)
                && str_ends_with(
                    $key,
                    '_at'
                )
                && $value !== null
                && $value !== ''
            ) {
                $safe[$key] =
                    $this->formatDateTime(
                        $value
                    );

                continue;
            }


            // ================================================
            // SAFE SCALAR OUTPUT
            // ================================================

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


        // =====================================================
        // LINKS
        // =====================================================

        $links = [];


        /*
         * IMPORTANT:
         *
         * interview.job_id references job_postings,
         * NOT global job_roles.
         *
         * Therefore job_id must NOT link to jobs.show.
         */

        foreach (
            [
                'company_id' =>
                    'organizations.show',

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
                ! empty(
                    $safe[$key]
                )
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

                'candidate_name' =>
                    'candidate_id',

                'full_name' =>
                    'user_id',
            ]
            as $label => $id
        ) {
            if (
                isset(
                    $links[$id]
                )
            ) {
                $links[$label] =
                    $links[$id];
            }
        }


        // =====================================================
        // DETAIL URL
        // =====================================================

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
            '_links' =>
                $links,
        ];
    }


    // =========================================================
    // FILTER FIELDS
    // =========================================================

    private function filterFields(
        string $module,
        array $definition
    ): array {
        $fields = [];


        // =====================================================
        // SEARCH
        // =====================================================

        if (
            isset(
                $definition['search']
            )
        ) {
            $searchFields =
                (array)
                    $definition['search'];

            $fields['q'] = [
                'label' =>
                    $definition[
                        'searchLabel'
                    ]
                    ?? (
                        'Search '
                        .str_replace(
                            '_',
                            ' ',
                            $searchFields[0]
                        )
                    ),

                'type' =>
                    'text',
            ];
        }


        // =====================================================
        // STATUS / ROLE / AVAILABILITY
        // =====================================================

        if (
            isset(
                $definition[
                    'statusField'
                ]
            )
        ) {
            $statusField =
                $definition[
                    'statusField'
                ];


            $label =
                match (true) {

                    $module === 'users' =>
                        'Role',

                    $module === 'jobs'
                    && $statusField ===
                        'is_active' =>
                        'Availability',

                    default =>
                        ucfirst(
                            $statusField
                        ),
                };


            $options =
                $module === 'jobs'
                && $statusField ===
                    'is_active'
                    ? [
                        'true' =>
                            'Enabled',

                        'false' =>
                            'Disabled',
                    ]
                    : array_combine(
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
                    );


            $fields[
                $statusField
            ] = [
                'label' =>
                    $label,

                'type' =>
                    'select',

                'options' =>
                    $options,
            ];
        }


        // =====================================================
        // USERS: KEEP FILTERS SIMPLE
        // =====================================================

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


        // =====================================================
        // ORGANIZATION FILTER
        // =====================================================

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


        // =====================================================
        // SERVICE FILTER
        // =====================================================

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


        // =====================================================
        // MODULE-SPECIFIC FILTERS
        // =====================================================

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


            default =>
                [],
        };


        foreach (
            $extra
            as $key => $label
        ) {
            $fields[$key] = [
                'label' =>
                    $label,

                'type' =>
                    'text',
            ];
        }


        // =====================================================
        // COMMON FILTERS
        // =====================================================

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


    // =========================================================
    // TABS
    // =========================================================

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


        foreach (
            $groups
            as $group
        ) {
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
                            route(
                                $route
                            ),

                        'active' =>
                            $key === $module,
                    ];
                }


                return $tabs;
            }
        }


        return [];
    }


    // =========================================================
    // DETAIL
    // =========================================================

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


        // =====================================================
        // ORGANIZATIONS
        // =====================================================

        if (
            $module ===
            'organizations'
        ) {
            $fields['Members'] =
                $this->repository->count(
                    'members',
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


            /*
             * Do NOT use global "jobs" here.
             *
             * The jobs module now represents job_roles.
             * Company job descriptions remain in job_postings.
             */


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


        // =====================================================
        // USERS
        // =====================================================

        elseif (
            $module === 'users'
        ) {
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


            // =================================================
            // SUPABASE AUTH
            // =================================================

            $authUser =
                $this->repository
                    ->authUser(
                        $id
                    );


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


            // Fallback to identities.
            if (
                empty(
                    $providers
                )
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


            // Final fallback.
            if (
                empty(
                    $providers
                )
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
                ! empty(
                    $authUser[
                        'last_sign_in_at'
                    ]
                )
                    ? $this->formatDateTime(
                        $authUser[
                            'last_sign_in_at'
                        ]
                    )
                    : 'Never';


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


            // Related company memberships.
            $sections[] =
                $this->related(
                    'members',
                    [
                        'user_id' =>
                            $id,
                    ],
                    $token
                );


            // Related interviews.
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


        // =====================================================
        // GLOBAL JOB ROLES
        // =====================================================

        elseif (
            $module === 'jobs'
        ) {

            /*
             * This module represents public.job_roles.
             *
             * It does NOT represent company job_postings.
             */

            $fields = [

                'Job' =>
                    $row['title']
                    ?? '—',

                'Category' =>
                    $row[
                        'category_name'
                    ]
                    ?? 'Not recorded',

                'Availability' =>
                    $row[
                        'is_active'
                    ]
                    ?? 'Disabled',

                'Slug' =>
                    $row['slug']
                    ?? '—',

                'Created' =>
                    $row[
                        'created_at'
                    ]
                    ?? '—',

                'Updated' =>
                    $row[
                        'updated_at'
                    ]
                    ?? '—',

                'Record ID' =>
                    $id,
            ];


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
        }


        // =====================================================
        // INTERVIEWS
        // =====================================================

        elseif (
            $module ===
            'interviews'
        ) {
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
                    'Connection heartbeats, provider/model metadata, latency, cost and failure traces are not recorded by the current contract. '
                    .'Transcript and media access require a separate authorized access workflow.',
            ];
        }


        // =====================================================
        // AUDIT
        // =====================================================

        elseif (
            $module === 'audit'
        ) {
            $fields[
                'Outcome'
            ] =
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
                (
                    $raw[
                        'table_name'
                    ]
                    ?? null
                )
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


        // =====================================================
        // EDIT URL
        // =====================================================

        $editUrl =
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

                'jobs' =>
                    route(
                        'jobs.edit',
                        $id
                    ),

                default =>
                    null,
            };


        $editLabel =
            match ($module) {

                'organizations' =>
                    'Edit Organization',

                'users' =>
                    'Edit User',

                'jobs' =>
                    'Edit Job',

                default =>
                    'Edit',
            };


        // =====================================================
        // RETURN DETAIL
        // =====================================================

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
                $editUrl,

            'editLabel' =>
                $editLabel,
        ];
    }


    // =========================================================
    // RELATED TABLE
    // =========================================================

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


        $table[
            'filterFields'
        ] = [];


        $table[
            'createUrl'
        ] = null;


        $table[
            'tabs'
        ] = [];


        $table[
            'paginator'
        ] =
            new LengthAwarePaginator(
                $table[
                    'rows'
                ],
                count(
                    $table[
                        'rows'
                    ]
                ),
                5,
                1
            );


        $table[
            'collectionUrl'
        ] =
            route(
                $module.'.index',
                $scope
            );


        $table[
            'note'
        ] =
            'Showing up to 5 records. '
            .$table[
                'description'
            ];


        return [
            'title' =>
                $table[
                    'title'
                ],

            'table' =>
                $table,
        ];
    }


    // =========================================================
    // FORMAT DATETIME
    // =========================================================

    private function formatDateTime(
        mixed $value
    ): string {
        if (
            $value === null
            || $value === ''
            || $value === '—'
        ) {
            return '—';
        }


        try {
            return Carbon::parse(
                (string) $value
            )
                ->utc()
                ->format(
                    'd M Y · H:i'
                )
                .' UTC';

        } catch (\Throwable) {
            return (string) $value;
        }
    }
}