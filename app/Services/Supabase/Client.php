<?php

namespace App\Services\Supabase;

use App\Exceptions\SupabaseException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class Client
{
    public function configured(): bool
    {
        $url = (string) config('supabase.url');

        return (
            str_starts_with($url, 'https://')
            || str_starts_with($url, 'http://')
        )
            && parse_url($url, PHP_URL_HOST)
            && ! parse_url($url, PHP_URL_USER)
            && ! parse_url($url, PHP_URL_QUERY)
            && filled(config('supabase.anon_key'));
    }

    public function request(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        ?string $token = null,
        bool $service = false,
        bool $count = false,
        array $headers = [],
    ): Response {
        if (! $this->configured()) {
            throw new SupabaseException;
        }

        $key = (string) config(
            $service
                ? 'supabase.service_role_key'
                : 'supabase.anon_key'
        );

        if ($service && ! filled($key)) {
            Log::warning(
                'Supabase service key is not configured for administrative request'
            );

            throw new SupabaseException;
        }

        $attempts =
            strtoupper($method) === 'GET'
                ? 2
                : 1;

        for (
            $attempt = 1;
            $attempt <= $attempts;
            $attempt++
        ) {
            try {
                $requestHeaders = [
                    'apikey' => $key,
                ];

                if ($count) {
                    $requestHeaders['Prefer'] =
                        'count=exact';
                }

                $requestHeaders = array_merge(
                    $requestHeaders,
                    $headers
                );

                $request = Http::baseUrl(
                    config('supabase.url')
                )
                    ->acceptJson()
                    ->asJson()
                    ->withHeaders(
                        $requestHeaders
                    )
                    ->connectTimeout(
                        config(
                            'supabase.connect_timeout'
                        )
                    )
                    ->timeout(
                        config(
                            'supabase.timeout'
                        )
                    )
                    ->withOptions([
                        'allow_redirects' => false,
                    ]);

                /*
                 * Normal user requests use the signed-in
                 * user's JWT.
                 *
                 * New Supabase sb_secret_* keys are API keys,
                 * not JWTs, so administrative service
                 * requests intentionally do NOT send them
                 * as Bearer tokens.
                 */
                if (
                    ! $service
                    && filled($token)
                ) {
                    $request =
                        $request->withToken(
                            $token
                        );
                }

                $response = $request->send(
                    strtoupper($method),
                    $path,
                    array_filter(
                        [
                            'query' => $query,
                            'json' => $body,
                        ],
                        fn ($value) =>
                            $value !== null
                    )
                );
            } catch (ConnectionException) {
                if ($attempt < $attempts) {
                    usleep(200000);
                    continue;
                }

                Log::warning(
                    'Supabase connection unavailable',
                    [
                        'operation' =>
                            strtoupper($method),
                    ]
                );

                throw new SupabaseException;
            }

            if ($response->successful()) {
                return $response;
            }

            if (
                $response->serverError()
                && $attempt < $attempts
            ) {
                usleep(200000);
                continue;
            }

            $code =
                $response->json('code')
                ?? $response->json('error_code');

            $status = match (true) {
                $response->status() === 429 =>
                    429,

                $response->status() === 401 =>
                    401,

                $response->status() === 403
                    || $code === '42501' =>
                    403,

                $response->status() === 404 =>
                    404,

                $code === '23505'
                    || $code === 'P0001'
                    || $code === '40001' =>
                    409,

                $code === 'P0002' =>
                    404,

                in_array(
                    $code,
                    [
                        '22023',
                        '23514',
                        '23503',
                        '22P02',
                        'validation_failed',
                    ],
                    true
                ) =>
                    422,

                $path === '/auth/v1/token'
                    && $response->status() === 400 =>
                    401,

                default =>
                    503,
            };

            /*
             * Never log:
             * - response body
             * - secret key
             * - Authorization header
             * - user passwords
             */
            Log::warning(
                'Supabase request failed',
                [
                    'operation' =>
                        strtoupper($method),

                    'path' =>
                        $this->safePathForLog(
                            $path
                        ),

                    'status' =>
                        $response->status(),
                ]
            );

            throw new SupabaseException(
                $status,
                min(
                    120,
                    max(
                        1,
                        (int) (
                            $response->header(
                                'Retry-After'
                            )
                            ?: 30
                        )
                    )
                )
            );
        }

        throw new SupabaseException;
    }

    // =========================================================
    // NORMAL AUTHENTICATED REST
    // =========================================================

    public function select(
        string $table,
        array $query,
        string $token,
        bool $count = false,
    ): Response {
        return $this->request(
            'GET',
            '/rest/v1/'.$table,
            $query,
            token: $token,
            count: $count,
        );
    }

    public function rpc(
        string $name,
        array $parameters,
        string $token,
    ): array {
        $data = $this->request(
            'POST',
            '/rest/v1/rpc/'.$name,
            body: $parameters,
            token: $token,
        )->json();

        if (! is_array($data)) {
            throw new SupabaseException;
        }

        return $data;
    }

    // =========================================================
    // SERVICE REST
    // Server-side only
    // =========================================================

    public function serviceSelect(
        string $table,
        array $query = [],
        bool $count = false,
    ): Response {
        return $this->request(
            'GET',
            '/rest/v1/'.$table,
            $query,
            service: true,
            count: $count,
        );
    }

    public function serviceInsert(
        string $table,
        array $body,
    ): array {
        $data = $this->request(
            'POST',
            '/rest/v1/'.$table,
            body: $body,
            service: true,
            headers: [
                'Prefer' =>
                    'return=representation',
            ],
        )->json();

        if (! is_array($data)) {
            throw new SupabaseException;
        }

        return $data;
    }

    public function serviceUpdate(
        string $table,
        array $query,
        array $body,
    ): array {
        $data = $this->request(
            'PATCH',
            '/rest/v1/'.$table,
            query: $query,
            body: $body,
            service: true,
            headers: [
                'Prefer' =>
                    'return=representation',
            ],
        )->json();

        if (! is_array($data)) {
            throw new SupabaseException;
        }

        return $data;
    }

    public function serviceDelete(
        string $table,
        array $query,
    ): void {
        $this->request(
            'DELETE',
            '/rest/v1/'.$table,
            query: $query,
            service: true,
        );
    }

    // =========================================================
    // SUPABASE AUTH ADMIN
    // Server-side only
    // =========================================================

    public function adminGetUser(
        string $userId,
    ): array {
        $data = $this->request(
            'GET',
            '/auth/v1/admin/users/'
                .rawurlencode($userId),
            service: true,
        )->json();

        if (! is_array($data)) {
            throw new SupabaseException;
        }

        return $data;
    }

    public function adminUpdateUser(
        string $userId,
        array $attributes,
    ): array {
        $data = $this->request(
            'PUT',
            '/auth/v1/admin/users/'
                .rawurlencode($userId),
            body: $attributes,
            service: true,
        )->json();

        if (! is_array($data)) {
            throw new SupabaseException;
        }

        return $data;
    }

    public function adminDeleteUser(
        string $userId,
        bool $softDelete = false,
    ): void {
        $this->request(
            'DELETE',
            '/auth/v1/admin/users/'
                .rawurlencode($userId),
            body: [
                'should_soft_delete' =>
                    $softDelete,
            ],
            service: true,
        );
    }

    // =========================================================
    // PASSWORD
    // =========================================================

    public function adminSetPassword(
        string $userId,
        string $password,
    ): array {
        return $this->adminUpdateUser(
            $userId,
            [
                'password' => $password,
            ]
        );
    }

    public function sendPasswordRecovery(
        string $email,
    ): void {
        $this->request(
            'POST',
            '/auth/v1/recover',
            body: [
                'email' => $email,
            ],
        );
    }

    // =========================================================
    // LOGGING SAFETY
    // =========================================================

    private function safePathForLog(
        string $path,
    ): string {
        if (
            str_starts_with(
                $path,
                '/auth/v1/admin/users/'
            )
        ) {
            return '/auth/v1/admin/users/{id}';
        }

        return $path;
    }
}