<?php

namespace App\Services;

use App\Exceptions\SupabaseException;
use App\Services\Supabase\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class AuthEventRecorder
{
    public function __construct(private Client $client) {}

    public function record(string $event, string $outcome, ?string $actor = null): void
    {
        $payload = [
            'id' => (string) Str::uuid(), 'actor_id' => $actor, 'event' => $event,
            'outcome' => $outcome, 'correlation_id' => (string) Str::uuid(),
        ];
        // The same event ID makes an ambiguous transport retry safe. Duplicate = already recorded.
        for ($attempt = 0; $attempt < 2; $attempt++) {
            try {
                $this->client->request('POST', '/rest/v1/dashboard_auth_events', body: $payload, service: true);
                return;
            } catch (SupabaseException $e) {
                if ($e->status === 409) {
                    return;
                }
                if ($attempt === 0 && $e->status === 503) {
                    continue;
                }
                Log::error('Dashboard security event persistence unavailable', ['event' => $event, 'event_id' => $payload['id']]);
                throw $e;
            }
        }
    }
}
