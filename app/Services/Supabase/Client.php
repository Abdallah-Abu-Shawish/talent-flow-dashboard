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
        return (str_starts_with($url, 'https://') || str_starts_with($url, 'http://'))
            && parse_url($url, PHP_URL_HOST)
            && ! parse_url($url, PHP_URL_USER)
            && ! parse_url($url, PHP_URL_QUERY)
            && filled(config('supabase.anon_key'));
    }

    public function request(string $method, string $path, array $query = [], ?array $body = null, ?string $token = null, bool $service = false, bool $count = false): Response
    {
        if (! $this->configured()) {
            throw new SupabaseException;
        }

        $key = (string) config($service ? 'supabase.service_role_key' : 'supabase.anon_key');
        if ($service && ! filled($key)) {
            Log::warning('Supabase service role key is not configured for administrative request');
            throw new SupabaseException;
        }
        $attempts = $method === 'GET' ? 2 : 1;
        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $request = Http::baseUrl(config('supabase.url'))
                    ->acceptJson()->asJson()->withHeaders(['apikey' => $key])
                    ->withToken($service ? $key : ($token ?? $key))
                    ->connectTimeout(config('supabase.connect_timeout'))
                    ->timeout(config('supabase.timeout'))
                    ->withOptions(['allow_redirects' => false]);
                if ($count) {
                    $request = $request->withHeaders(['Prefer' => 'count=exact']);
                }
                $response = $request->send($method, $path, array_filter(['query' => $query, 'json' => $body], fn ($v) => $v !== null));
            } catch (ConnectionException) {
                if ($attempt < $attempts) {
                    usleep(200000);
                    continue;
                }
                Log::warning('Supabase connection unavailable', ['operation' => $method]);
                throw new SupabaseException;
            }

            if ($response->successful()) {
                return $response;
            }
            if ($response->serverError() && $attempt < $attempts) {
                usleep(200000);
                continue;
            }
            $code = $response->json('code');
            $status = match (true) {
                $response->status() === 429 => 429,
                $response->status() === 401 => 401,
                $response->status() === 403 || $code === '42501' => 403,
                $code === '23505' || $code === 'P0001' || $code === '40001' => 409,
                $code === 'P0002' => 404,
                in_array($code, ['22023', '23514', '23503', '22P02'], true) => 422,
                $path === '/auth/v1/token' && $response->status() === 400 => 401,
                default => 503,
            };
            // Never log response bodies, request headers, credentials, or arbitrary provider messages.
            Log::warning('Supabase request failed', ['operation' => $method, 'status' => $response->status()]);
            throw new SupabaseException($status, min(120, max(1, (int) ($response->header('Retry-After') ?: 30))));
        }

        throw new SupabaseException;
    }

    public function select(string $table, array $query, string $token, bool $count = false): Response
    {
        return $this->request('GET', '/rest/v1/'.$table, $query, token: $token, count: $count);
    }

    public function rpc(string $name, array $parameters, string $token): array
    {
        $data = $this->request('POST', '/rest/v1/rpc/'.$name, body: $parameters, token: $token)->json();
        if (! is_array($data)) {
            throw new SupabaseException;
        }
        return $data;
    }
}
