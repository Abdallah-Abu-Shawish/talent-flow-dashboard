<?php

namespace App\Support;

final class DashboardModules
{
    public static function get(string $module): array
    {
        $definitions = [

            // =====================================================
            // ORGANIZATIONS
            // =====================================================

            'organizations' => [
                'table' => 'companies',

                'title' => 'Enterprise Accounts',

                'description' =>
                    'Organization identity, plan and recorded token balance across the platform.',

                'select' =>
                    'id,name,slug,plan,token_balance,created_at,updated_at',

                'search' => 'name',

                'statuses' => [
                    'free',
                    'starter',
                    'pro',
                    'enterprise',
                ],

                'statusField' => 'plan',

                'columns' => [
                    'name' => 'Organization',
                    'slug' => 'Slug',
                    'plan' => 'Plan',
                    'token_balance' => 'Token balance',
                    'created_at' => 'Created',
                ],

                'sorts' => [
                    'created_at' => 'Newest',
                    'name' => 'Name',
                    'token_balance' => 'Token balance',
                ],
            ],


            // =====================================================
            // USERS
            // =====================================================

            'users' => [
                'table' => 'profiles',

                'title' => 'Users & Access',

                'description' =>
                    'Manage users, profile information and platform access.',

                'select' =>
                    'id,email,full_name,phone_number,role,company_request_enabled,created_at,updated_at',

                'search' => [
                    'full_name',
                    'email',
                    'phone_number',
                ],

                'searchLabel' =>
                    'Search name, email or phone',

                'statuses' => [
                    'candidate',
                    'super_admin',
                ],

                'statusField' => 'role',

                'columns' => [
                    'full_name' => 'User',
                    'email' => 'Email',
                    'phone_number' => 'Phone',
                    'role' => 'Role',
                    'active_sessions' => 'Active sessions',
                    'created_at' => 'Created',
                ],

                'sorts' => [
                    'created_at' => 'Newest',
                    'full_name' => 'Name',
                    'email' => 'Email',
                ],
            ],


            // =====================================================
            // GLOBAL JOB CATALOG
            // job_roles = Jobs managed by Super Admin
            // =====================================================

            'jobs' => [
                'table' => 'job_roles',

                'title' => 'Jobs',

                'description' =>
                    'Manage the global job catalog used across the TalentFlow platform.',

                'select' =>
                    'id,'
                    .'category_id,'
                    .'title,'
                    .'slug,'
                    .'description,'
                    .'is_active,'
                    .'created_by,'
                    .'created_at,'
                    .'updated_at,'
                    .'category:job_categories!job_roles_category_id_fkey(name,is_active)',

                'search' => [
                    'title',
                    'slug',
                ],

                'searchLabel' =>
                    'Search job title or slug',

                'statuses' => [
                    'true',
                    'false',
                ],

                'statusField' =>
                    'is_active',

                'columns' => [
                    'title' =>
                        'Job',

                    'category_name' =>
                        'Category',

                    'slug' =>
                        'Slug',

                    'is_active' =>
                        'Availability',

                    'created_at' =>
                        'Created',
                ],

                'sorts' => [
                    'created_at' =>
                        'Newest',

                    'title' =>
                        'Title',

                    'slug' =>
                        'Slug',
                ],
            ],


            // =====================================================
            // AI INTERVIEWS
            //
            // IMPORTANT:
            // Interviews continue to use job_postings.
            // job_postings contains the company-specific job
            // description/context used by the AI interview.
            // =====================================================

            'interviews' => [
                'table' => 'interviews',

                'title' => 'AI Interviews',

                'description' =>
                    'Recorded interview lifecycle. In progress does not confirm a live connection.',

                'select' =>
                    'id,company_id,job_id,candidate_id,status,scheduled_at,started_at,ended_at,overall_score,created_at,'
                    .'company:companies!interviews_company_id_fkey(name),'
                    .'job:job_postings!interview_job_tenant_fk(title),'
                    .'candidate:profiles!interviews_candidate_id_fkey(full_name)',

                'statuses' => [
                    'scheduled',
                    'in_progress',
                    'completed',
                    'failed',
                    'flagged',
                ],

                'statusField' => 'status',

                'companyField' => 'company_id',

                'columns' => [
                    'candidate_name' => 'Candidate',
                    'job_title' => 'Job',
                    'company_name' => 'Organization',
                    'status' => 'Status',
                    'scheduled_at' => 'Scheduled',
                    'overall_score' => 'Recorded score',
                ],

                'sorts' => [
                    'created_at' => 'Newest',
                    'scheduled_at' => 'Scheduled time',
                ],
            ],


            // =====================================================
            // USAGE
            // =====================================================

            'usage' => [
                'table' => 'token_usage_logs',

                'title' => 'Usage & Tokens',

                'description' =>
                    'Immutable token debit ledger. Ledger entries are not provider request counts or monetary costs.',

                'select' =>
                    'id,company_id,interview_id,service_name,tokens_used,balance_after,created_at,company:companies(name)',

                'companyField' => 'company_id',

                'serviceField' => 'service_name',

                'columns' => [
                    'company_name' => 'Organization',
                    'service_name' => 'Service',
                    'tokens_used' => 'Charged tokens',
                    'balance_after' => 'Balance after',
                    'created_at' => 'Recorded at',
                ],

                'sorts' => [
                    'created_at' => 'Newest',
                    'tokens_used' => 'Tokens used',
                ],
            ],


            // =====================================================
            // CREDITS
            // =====================================================

            'credits' => [
                'table' => 'token_credit_logs',

                'title' => 'Token Credits',

                'description' =>
                    'Recorded token credits. Credits do not represent subscription or invoice payments.',

                'select' =>
                    'id,company_id,tokens_added,balance_after,created_at,company:companies(name)',

                'companyField' => 'company_id',

                'columns' => [
                    'company_name' => 'Organization',
                    'tokens_added' => 'Tokens added',
                    'balance_after' => 'Balance after',
                    'created_at' => 'Recorded at',
                ],

                'sorts' => [
                    'created_at' => 'Newest',
                ],
            ],


            // =====================================================
            // AUDIT
            // =====================================================

            'audit' => [
                'table' => 'audit_logs',

                'title' => 'Audit Logs',

                'description' =>
                    'Immutable database row changes. Open an event for a safe before/after summary.',

                'select' =>
                    'id,event_sequence,table_name,record_id,company_id,action,performed_by,database_role,created_at',

                'search' => 'table_name',

                'statuses' => [
                    'INSERT',
                    'UPDATE',
                    'DELETE',
                ],

                'statusField' => 'action',

                'companyField' => 'company_id',

                'columns' => [
                    'table_name' => 'Resource',
                    'action' => 'Action',
                    'record_id' => 'Record',
                    'performed_by' => 'Actor',
                    'database_role' => 'Database role',
                    'created_at' => 'Recorded at',
                ],

                'sorts' => [
                    'created_at' => 'Newest',
                ],
            ],


            // =====================================================
            // DASHBOARD COMMANDS
            // =====================================================

            'commands' => [
                'table' => 'dashboard_admin_commands',

                'title' => 'Dashboard Actions',

                'description' =>
                    'Committed organization commands with actor attribution and the administrator’s reason.',

                'select' =>
                    'id,actor_id,company_id,action,reason,created_at',

                'statuses' => [
                    'create',
                    'update',
                ],

                'statusField' => 'action',

                'companyField' => 'company_id',

                'columns' => [
                    'action' => 'Committed action',
                    'company_id' => 'Organization',
                    'actor_id' => 'Actor',
                    'reason' => 'Reason',
                    'created_at' => 'Recorded at',
                ],

                'sorts' => [
                    'created_at' => 'Newest',
                ],
            ],


            // =====================================================
            // SECURITY
            // =====================================================

            'security' => [
                'table' => 'anti_cheat_logs',

                'title' => 'Security & Integrity',

                'description' =>
                    'Persisted interview integrity observations. Severity is recorded by the producer.',

                'select' =>
                    'id,interview_id,event_type,severity,created_at,interview:interviews!inner(company_id,candidate_id)',

                'search' => 'event_type',

                'statuses' => [
                    'low',
                    'medium',
                    'high',
                    'critical',
                ],

                'statusField' => 'severity',

                'companyField' =>
                    'interview.company_id',

                'columns' => [
                    'event_type' => 'Event',
                    'severity' => 'Severity',
                    'interview_id' => 'Interview',
                    'company_id' => 'Organization',
                    'created_at' => 'Recorded at',
                ],

                'sorts' => [
                    'created_at' => 'Newest',
                ],
            ],


            // =====================================================
            // AUTH EVENTS
            // =====================================================

            'auth-events' => [
                'table' => 'dashboard_auth_events',

                'title' => 'Authentication Events',

                'description' =>
                    'Dashboard sign-ins, access denials, expirations, sign-outs and failed admin actions.',

                'select' =>
                    'id,actor_id,event,outcome,correlation_id,created_at',

                'statuses' => [
                    'success',
                    'denied',
                    'failure',
                ],

                'statusField' => 'outcome',

                'columns' => [
                    'event' => 'Event',
                    'outcome' => 'Outcome',
                    'actor_id' => 'Actor',
                    'correlation_id' => 'Correlation ID',
                    'created_at' => 'Recorded at',
                ],

                'sorts' => [
                    'created_at' => 'Newest',
                ],
            ],


            // =====================================================
            // COMPANY MEMBERS
            // =====================================================

          'members' => [
    'table' =>
        'company_members',

    'title' =>
        'Organization Memberships',

    'description' =>
        'Tenant authority is separate from the global profile role.',

    'select' =>
        'id,company_id,user_id,role,is_active,suspended_at,created_at,company:companies(name),profile:profiles!company_members_user_id_fkey(full_name)',

    'statuses' => [
        'company_admin',
        'interviewer',
    ],

    'statusField' =>
        'role',

    'companyField' =>
        'company_id',

    'columns' => [
        'full_name' =>
            'Member',

        'company_name' =>
            'Organization',

        'role' =>
            'Tenant role',

        'is_active' =>
            'Status',

        'created_at' =>
            'Joined',
    ],

    'sorts' => [
        'created_at' =>
            'Newest',
    ],
],
        ];


        abort_unless(
            isset($definitions[$module]),
            404
        );


        return $definitions[$module];
    }


    // =========================================================
    // COLUMN TYPES
    // =========================================================

    public static function columns(
        array $definition
    ): array {
        $columns = [];


        foreach (
            $definition['columns']
            as $key => $label
        ) {
            $type = match (true) {

                in_array(
                    $key,
                    [
                        'role',
                        'plan',
                        'status',
                        'severity',
                        'action',
                        'outcome',
                        'is_active',
                    ],
                    true
                ) =>
                    'badge',


                str_ends_with(
                    $key,
                    '_id'
                )
                || $key === 'id' =>
                    'mono',


                in_array(
                    $key,
                    [
                        'token_balance',
                        'tokens_used',
                        'tokens_added',
                        'balance_after',
                        'overall_score',
                        'active_sessions',
                    ],
                    true
                ) =>
                    'number',


                default =>
                    'text',
            };


            $columns[] =
                compact(
                    'key',
                    'label',
                    'type'
                );
        }


        return $columns;
    }
}