<?php

namespace App\Repositories;

use App\Exceptions\SupabaseException;
use App\Services\Supabase\Client;
use App\Support\DashboardModules;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

final class DashboardRepository
{
    public function __construct(
        private Client $client
    ) {}


    // =========================================================
    // PAGINATION / MODULE TABLE
    // =========================================================

    public function page(
        string $module,
        array $filters,
        string $token,
        array $scope = []
    ): LengthAwarePaginator {
        $definition = DashboardModules::get($module);

        $size = (int) ($filters['per_page'] ?? 25);
        $page = (int) ($filters['page'] ?? 1);

        $sort =
            $filters['sort']
            ?? 'created_at';

        $direction =
            $filters['direction']
            ?? 'desc';


        $query = [
            'select' =>
                $definition['select'],

            'limit' =>
                $size,

            'offset' =>
                ($page - 1) * $size,

            'order' =>
                $sort.'.'.$direction
                .',id.'.$direction,
        ];


        // =====================================================
        // SEARCH
        // =====================================================

        if (
            ! empty($filters['q'])
            && isset($definition['search'])
        ) {
            $searchFields =
                (array) $definition['search'];

            // Keep normal name/email/phone/search characters only.
            $literal = preg_replace(
                '/[^\p{L}\p{N}@._+\-\s]/u',
                '',
                trim(
                    (string) $filters['q']
                )
            );


            if ($literal !== '') {
                if (
                    count($searchFields) === 1
                ) {
                    $query[
                        $searchFields[0]
                    ] =
                        'ilike.*'
                        .$literal
                        .'*';
                } else {
                    $expressions =
                        array_map(
                            fn (string $field) =>
                                $field
                                .'.ilike.*'
                                .$literal
                                .'*',

                            $searchFields
                        );


                    $query['or'] =
                        '('
                        .implode(
                            ',',
                            $expressions
                        )
                        .')';
                }
            }
        }


        // =====================================================
        // STATUS / AVAILABILITY
        // =====================================================

        if (
            isset(
                $definition['statusField'],
                $filters[
                    $definition['statusField']
                ]
            )
        ) {
            $query[
                $definition['statusField']
            ] =
                'eq.'
                .$filters[
                    $definition['statusField']
                ];
        }


        // =====================================================
        // COMPANY FILTER
        // =====================================================

        if (
            isset(
                $definition['companyField'],
                $filters['company_id']
            )
        ) {
            $query[
                $definition['companyField']
            ] =
                'eq.'
                .$filters['company_id'];
        }


        // =====================================================
        // SERVICE FILTER
        // =====================================================

        if (
            isset(
                $definition['serviceField'],
                $filters['service']
            )
        ) {
            $query[
                $definition['serviceField']
            ] =
                'eq.'
                .$filters['service'];
        }


        // =====================================================
        // MODULE-SPECIFIC FILTERS
        // =====================================================

        $moduleFilters =
            match ($module) {
                'interviews' => [
                    'job_id',
                    'candidate_id',
                ],

                'usage',
                'security' => [
                    'interview_id',
                ],

                'members' => [
                    'user_id',
                ],

                'audit' => [
                    'record_id',
                ],

                'auth-events' => [
                    'actor_id',
                    'correlation_id',
                ],

                'commands' => [
                    'actor_id',
                ],

                default => [],
            };


        foreach (
            $moduleFilters
            as $field
        ) {
            if (
                isset($filters[$field])
            ) {
                $query[$field] =
                    'eq.'
                    .$filters[$field];
            }
        }


        // =====================================================
        // AUDIT ACTOR
        // =====================================================

        if (
            $module === 'audit'
            && isset(
                $filters['actor_id']
            )
        ) {
            $query['performed_by'] =
                'eq.'
                .$filters['actor_id'];
        }


        // =====================================================
        // USER COMPANY MEMBERSHIP FILTER
        // =====================================================

        if (
            $module === 'users'
            && isset(
                $filters['company_id']
            )
        ) {
            $query['select'] .=
                ',membership:company_members!inner(company_id)';

            $query[
                'membership.company_id'
            ] =
                'eq.'
                .$filters['company_id'];
        }


        // =====================================================
        // DATE FILTERS
        // =====================================================

        $dateTerms = [];


        if (
            isset($filters['from'])
        ) {
            $dateTerms[] =
                'created_at.gte.'
                .$filters['from']
                .'T00:00:00Z';
        }


        if (
            isset($filters['to'])
        ) {
            $dateTerms[] =
                'created_at.lt.'
                .Carbon::parse(
                    $filters['to'],
                    'UTC'
                )
                    ->addDay()
                    ->format('Y-m-d')
                .'T00:00:00Z';
        }


        if ($dateTerms) {
            $query['and'] =
                '('
                .implode(
                    ',',
                    $dateTerms
                )
                .')';
        }


        // =====================================================
        // ADDITIONAL SCOPE
        // =====================================================

        foreach (
            $scope
            as $field => $value
        ) {
            $query[$field] =
                'eq.'
                .$value;
        }


        // =====================================================
        // QUERY SUPABASE
        // =====================================================

        $response =
            $this->client->select(
                $definition['table'],
                $query,
                $token,
                true
            );


        $rows =
            $response->json();

        $range =
            $response->header(
                'Content-Range'
            );


        if (
            ! is_array($rows)
            || ! preg_match(
                '/\/(\d+)$/',
                (string) $range,
                $matches
            )
        ) {
            // An absent exact count is an upstream
            // contract failure, never a fabricated total.
            throw new SupabaseException;
        }


        return new LengthAwarePaginator(
            $rows,
            (int) $matches[1],
            $size,
            $page,
            [
                'path' =>
                    request()->url(),

                'query' =>
                    request()->query(),
            ]
        );
    }


    // =========================================================
    // FIND MODULE RECORD
    // =========================================================

    public function find(
        string $module,
        string $id,
        string $token,
        string $extra = ''
    ): array {
        $definition =
            DashboardModules::get(
                $module
            );


        $rows =
            $this->client->select(
                $definition['table'],
                [
                    'select' =>
                        $definition['select']
                        .$extra,

                    'id' =>
                        'eq.'.$id,

                    'limit' =>
                        1,
                ],
                $token
            )->json();


        if (! is_array($rows)) {
            throw new SupabaseException;
        }


        abort_unless(
            isset($rows[0]),
            404
        );


        return $rows[0];
    }


    // =========================================================
    // INTERVIEW EVALUATION
    // =========================================================

    public function evaluation(
        string $interviewId,
        string $token
    ): ?array {
        $rows =
            $this->client->select(
                'interview_evaluations',
                [
                    'select' =>
                        'technical_score,'
                        .'communication_score,'
                        .'problem_solving_score,'
                        .'created_at',

                    'interview_id' =>
                        'eq.'
                        .$interviewId,

                    'limit' =>
                        1,
                ],
                $token
            )->json();


        if (! is_array($rows)) {
            throw new SupabaseException;
        }


        return $rows[0] ?? null;
    }


    // =========================================================
    // COUNT MODULE RECORDS
    // =========================================================

    public function count(
        string $module,
        array $scope,
        string $token
    ): int {
        return $this
            ->page(
                $module,
                [
                    'per_page' => 1,
                ],
                $token,
                $scope
            )
            ->total();
    }


    // =========================================================
    // DASHBOARD SUMMARY
    // =========================================================

    public function summary(
        array $filters,
        string $token
    ): array {
        return $this->client->rpc(
            'dashboard_summary',
            [
                'p_from' =>
                    $filters['from'],

                'p_to' =>
                    $filters['to'],

                'p_company_id' =>
                    $filters['company_id']
                    ?? null,

                'p_service' =>
                    $filters['service']
                    ?? null,
            ],
            $token
        );
    }


    // =========================================================
    // SAVE ORGANIZATION
    // =========================================================

    public function saveOrganization(
        array $data,
        ?string $id,
        string $token
    ): array {
        return $this->client->rpc(
            'dashboard_save_company',
            [
                'p_request_id' =>
                    $data['request_id'],

                'p_company_id' =>
                    $id,

                'p_name' =>
                    $data['name'],

                'p_slug' =>
                    $data['slug'],

                'p_plan' =>
                    $data['plan'],

                'p_expected_updated_at' =>
                    $data[
                        'expected_updated_at'
                    ]
                    ?? null,

                'p_reason' =>
                    $data['reason'],
            ],
            $token
        );
    }


    // =========================================================
    // SAVE GLOBAL JOB ROLE
    //
    // IMPORTANT:
    // This manages job_roles.
    // It does NOT modify job_postings.
    // =========================================================

    public function saveJob(
    array $data,
    ?string $id,
    string $token
): array {
    return $this->client->rpc(
        'dashboard_save_job_role',
        [
            'p_job_role_id' => $id,

            'p_category_id' =>
                $data['category_id'],

            'p_title' =>
                $data['title'],

            'p_description' =>
                $data['description']
                ?? null,

            'p_is_active' =>
                (bool) (
                    $data['is_active']
                    ?? true
                ),

            'p_expected_updated_at' =>
                $data['expected_updated_at']
                ?? null,
        ],
        $token
    );
}


    // =========================================================
    // AUTH USER
    // =========================================================

    public function authUser(
        string $id
    ): array {
        return $this->client
            ->adminGetUser(
                $id
            );
    }


    // =========================================================
    // USER SESSION COUNTS
    // =========================================================

    public function userSessionCounts(
        array $userIds,
        string $token
    ): array {
        $userIds =
            array_values(
                array_unique(
                    array_filter(
                        $userIds,
                        fn ($id) =>
                            is_string($id)
                            && $id !== ''
                    )
                )
            );


        if ($userIds === []) {
            return [];
        }


        $rows =
            $this->client->rpc(
                'dashboard_user_session_counts',
                [
                    'p_user_ids' =>
                        $userIds,
                ],
                $token
            );


        $counts =
            array_fill_keys(
                $userIds,
                0
            );


        foreach ($rows as $row) {
            $userId =
                $row['user_id']
                ?? null;


            if (
                is_string($userId)
                && isset(
                    $counts[$userId]
                )
            ) {
                $counts[$userId] =
                    (int) (
                        $row[
                            'active_sessions'
                        ]
                        ?? 0
                    );
            }
        }


        return $counts;
    }
}