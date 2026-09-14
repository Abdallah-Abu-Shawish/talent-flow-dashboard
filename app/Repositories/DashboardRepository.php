<?php

namespace App\Repositories;

use App\Exceptions\SupabaseException;
use App\Services\Supabase\Client;
use App\Support\DashboardModules;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

final class DashboardRepository
{
    public function __construct(private Client $client) {}

    public function page(string $module, array $filters, string $token, array $scope = []): LengthAwarePaginator
    {
        $definition = DashboardModules::get($module);
        $size = (int) ($filters['per_page'] ?? 25);
        $page = (int) ($filters['page'] ?? 1);
        $sort = $filters['sort'] ?? 'created_at';
        $direction = $filters['direction'] ?? 'desc';
        $query = [
            'select' => $definition['select'], 'limit' => $size, 'offset' => ($page - 1) * $size,
            'order' => $sort.'.'.$direction.',id.'.$direction,
        ];
        if (! empty($filters['q']) && isset($definition['search'])) {
            // Single-field filter: no PostgREST boolean grammar is concatenated from user input.
            $literal = str_replace(['\\', '%', '_', '*'], ['\\\\', '\\%', '\\_', '\\*'], $filters['q']);
            $query[$definition['search']] = 'ilike.%'.$literal.'%';
        }
        if (isset($definition['statusField'], $filters[$definition['statusField']])) {
            $query[$definition['statusField']] = 'eq.'.$filters[$definition['statusField']];
        }
        if (isset($definition['companyField'], $filters['company_id'])) {
            $query[$definition['companyField']] = 'eq.'.$filters['company_id'];
        }
        if (isset($definition['serviceField'], $filters['service'])) {
            $query[$definition['serviceField']] = 'eq.'.$filters['service'];
        }
        $moduleFilters = match ($module) {
            'interviews' => ['job_id', 'candidate_id'],
            'usage', 'security' => ['interview_id'],
            'members' => ['user_id'],
            'audit' => ['record_id'],
            'auth-events' => ['actor_id', 'correlation_id'],
            'commands' => ['actor_id'],
            default => [],
        };
        foreach ($moduleFilters as $field) {
            if (isset($filters[$field])) {
                $query[$field] = 'eq.'.$filters[$field];
            }
        }
        if ($module === 'audit' && isset($filters['actor_id'])) {
            $query['performed_by'] = 'eq.'.$filters['actor_id'];
        }
        if ($module === 'users' && isset($filters['company_id'])) {
            $query['select'] .= ',membership:company_members!inner(company_id)';
            $query['membership.company_id'] = 'eq.'.$filters['company_id'];
        }
        $dateTerms = [];
        if (isset($filters['from'])) {
            $dateTerms[] = 'created_at.gte.'.$filters['from'].'T00:00:00Z';
        }
        if (isset($filters['to'])) {
            $dateTerms[] = 'created_at.lt.'.Carbon::parse($filters['to'], 'UTC')->addDay()->format('Y-m-d').'T00:00:00Z';
        }
        if ($dateTerms) {
            $query['and'] = '('.implode(',', $dateTerms).')';
        }
        foreach ($scope as $field => $value) {
            $query[$field] = 'eq.'.$value;
        }
        $response = $this->client->select($definition['table'], $query, $token, true);
        $rows = $response->json();
        $range = $response->header('Content-Range');
        if (! is_array($rows) || ! preg_match('/\/(\d+)$/', $range, $matches)) {
            // An absent exact count is an upstream contract failure, never a fabricated total.
            throw new SupabaseException;
        }
        return new LengthAwarePaginator($rows, (int) $matches[1], $size, $page, [
            'path' => request()->url(), 'query' => request()->query(),
        ]);
    }

    public function find(string $module, string $id, string $token, string $extra = ''): array
    {
        $definition = DashboardModules::get($module);
        $rows = $this->client->select($definition['table'], [
            'select' => $definition['select'].$extra, 'id' => 'eq.'.$id, 'limit' => 1,
        ], $token)->json();
        if (! is_array($rows)) {
            throw new SupabaseException;
        }
        abort_unless(isset($rows[0]), 404);
        return $rows[0];
    }

    public function evaluation(string $interviewId, string $token): ?array
    {
        $rows = $this->client->select('interview_evaluations', [
            'select' => 'technical_score,communication_score,problem_solving_score,created_at',
            'interview_id' => 'eq.'.$interviewId, 'limit' => 1,
        ], $token)->json();
        if (! is_array($rows)) {
            throw new SupabaseException;
        }
        return $rows[0] ?? null;
    }

    public function count(string $module, array $scope, string $token): int
    {
        return $this->page($module, ['per_page' => 1], $token, $scope)->total();
    }

    public function summary(array $filters, string $token): array
    {
        return $this->client->rpc('dashboard_summary', [
            'p_from' => $filters['from'], 'p_to' => $filters['to'],
            'p_company_id' => $filters['company_id'] ?? null, 'p_service' => $filters['service'] ?? null,
        ], $token);
    }

    public function saveOrganization(array $data, ?string $id, string $token): array
    {
        return $this->client->rpc('dashboard_save_company', [
            'p_request_id' => $data['request_id'], 'p_company_id' => $id,
            'p_name' => $data['name'], 'p_slug' => $data['slug'], 'p_plan' => $data['plan'],
            'p_expected_updated_at' => $data['expected_updated_at'] ?? null, 'p_reason' => $data['reason'],
        ], $token);
    }
}
