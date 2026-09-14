<?php

namespace App\Services;

use App\Exceptions\SupabaseException;
use App\Services\Supabase\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class AdminAuthService
{
    public function __construct(private Client $client, private AuthEventRecorder $events) {}

    public function login(Request $request, array $credentials): void
    {
        try {
            $data = $this->client->request('POST', '/auth/v1/token', ['grant_type' => 'password'], $credentials)->json();
        } catch (SupabaseException $e) {
            if ($e->status === 401) {
                try {
                    $this->events->record('login_failed', 'denied');
                } catch (SupabaseException) {}
                throw ValidationException::withMessages(['email' => 'Unable to sign in with these credentials.']);
            }
            throw ValidationException::withMessages(['email' => 'Authentication service is currently unavailable. Please try again later.']);
        }

        $token = $data['access_token'] ?? null;
        $id = $data['user']['id'] ?? null;
        if (! is_string($token) || ! is_string($id) || ! Str::isUuid($id)) {
            throw ValidationException::withMessages(['email' => 'Unable to sign in with these credentials.']);
        }
        $profile = $this->profile($id, $token);
        if (($profile['role'] ?? null) !== 'super_admin') {
            try {
                $this->events->record('access_denied', 'denied', $id);
            } catch (SupabaseException) {} finally {
                $this->revoke($token);
            }
            throw ValidationException::withMessages(['email' => 'Unable to sign in with these credentials.']);
        }

        try {
            $this->events->record('login_succeeded', 'success', $id);
        } catch (SupabaseException) {}

        $request->session()->regenerate(true);
        $request->session()->put('admin', [
            'access_token' => $token, 'profile' => $profile,
            'expires_at' => time() + min(3600, max(1, (int) ($data['expires_in'] ?? 0))),
        ]);
    }

    public function currentProfile(string $token): array
    {
        $id = $this->client->request('GET', '/auth/v1/user', token: $token)->json('id');
        if (! is_string($id) || ! Str::isUuid($id)) {
            throw new SupabaseException(401);
        }
        return $this->profile($id, $token);
    }

    private function profile(string $id, string $token): array
    {
        $rows = $this->client->select('profiles', [
            'select' => 'id,full_name,email,role', 'id' => 'eq.'.$id, 'limit' => 1,
        ], $token)->json();
        return is_array($rows) ? ($rows[0] ?? []) : [];
    }

    public function logout(Request $request): void
    {
        $token = $request->session()->get('admin.access_token');
        $actor = $request->session()->get('admin.profile.id');
        try {
            if ($token) {
                try {
                    $this->events->record('logout', 'success', $actor);
                } catch (SupabaseException) {}
            }
        } finally {
            // Always destroy the local session, including during an upstream outage.
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            if ($token) {
                $this->revoke($token);
            }
        }
    }

    private function revoke(string $token): void
    {
        try {
            $this->client->request('POST', '/auth/v1/logout', ['scope' => 'local'], token: $token);
        } catch (SupabaseException) {
            // Local session is already invalid; access JWT also has a bounded lifetime.
        }
    }
}
