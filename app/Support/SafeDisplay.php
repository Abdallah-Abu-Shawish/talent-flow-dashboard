<?php

namespace App\Support;

final class SafeDisplay
{
    public static function text(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $text = mb_substr((string) $value, 0, 12000);
        // Defense in depth for free text; arbitrary payload objects never enter a view.
        $text = preg_replace('/\bBearer\s+\S+|\beyJ[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+/i', '[redacted]', $text);
        $text = preg_replace('/\b(?:sk-[A-Za-z0-9_-]{12,}|sb_secret_[A-Za-z0-9_-]+|AIza[A-Za-z0-9_-]{20,})\b/', '[redacted]', $text);
        $text = preg_replace('/\b(?:password|secret|api[_ -]?key|access[_ -]?token|refresh[_ -]?token|authorization)\s*[:=]\s*[^\s,;]+/i', '[redacted]', $text);
        return $text;
    }

    public static function auditSnapshot(?array $snapshot): array
    {
        if ($snapshot === null) {
            return ['State' => 'No row'];
        }
        $safe = [];
        foreach (['id', 'company_id', 'job_id', 'interview_id', 'candidate_id', 'user_id', 'role', 'plan', 'status', 'severity', 'tokens_used', 'tokens_added', 'balance_after', 'token_balance', 'created_at', 'updated_at'] as $key) {
            if (array_key_exists($key, $snapshot) && is_scalar($snapshot[$key])) {
                $safe[str_replace('_', ' ', ucfirst($key))] = self::text($snapshot[$key]);
            }
        }
        return $safe ?: ['Summary' => 'Sensitive fields omitted.'];
    }
}
