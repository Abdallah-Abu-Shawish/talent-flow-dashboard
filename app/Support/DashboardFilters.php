<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class DashboardFilters
{
    public static function fromRequest(Request $request, array $definition, bool $dateDefaults = false): array
    {
        $rules = [
            'q' => ['nullable', 'string', 'max:120'],
            'page' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'per_page' => ['nullable', Rule::in([15, 25, 50])],
            'sort' => ['nullable', Rule::in(array_keys($definition['sorts']))],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
            'company_id' => ['nullable', 'uuid'],
            'record_id' => ['nullable', 'uuid'],
            'actor_id' => ['nullable', 'uuid'],
            'interview_id' => ['nullable', 'uuid'],
            'job_id' => ['nullable', 'uuid'],
            'candidate_id' => ['nullable', 'uuid'],
            'user_id' => ['nullable', 'uuid'],
            'correlation_id' => ['nullable', 'uuid'],
            'service' => ['nullable', 'string', 'max:100'],
        ];
        if (isset($definition['statuses'])) {
            $rules[$definition['statusField']] = ['nullable', Rule::in($definition['statuses'])];
        }
        $filters = array_filter(Validator::make($request->query(), $rules)->validate(), fn ($v) => $v !== null && $v !== '');
        if ($dateDefaults) {
            $filters['from'] ??= now('UTC')->subDays(29)->format('Y-m-d');
            $filters['to'] ??= now('UTC')->format('Y-m-d');
        }
        if (isset($filters['from'], $filters['to'])) {
            $from = Carbon::parse($filters['from'], 'UTC');
            $to = Carbon::parse($filters['to'], 'UTC');
            if ($to->lt($from) || $from->diffInDays($to) > 365) {
                throw ValidationException::withMessages(['to' => 'Choose an end date on or after the start, within 366 days.']);
            }
        }
        return $filters + ['page' => 1, 'per_page' => 25, 'sort' => 'created_at', 'direction' => 'desc'];
    }
}
