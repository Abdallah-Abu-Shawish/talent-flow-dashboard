<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DashboardAuthTest extends TestCase
{
    public function test_login_screen_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sign in');
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $routes = [
            '/',
            '/organizations',
            '/users',
            '/jobs',
            '/interviews',
            '/usage',
            '/health',
            '/audit',
            '/security',
            '/settings',
            '/organizations/create',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    public function test_expired_session_is_redirected_to_login(): void
    {
        $response = $this->withSession([
            'admin' => [
                'access_token' => 'dummy_token',
                'expires_at' => time() - 3600,
                'profile' => ['id' => '00000000-0000-0000-0000-000000000000', 'role' => 'super_admin'],
            ],
        ])->get('/');

        $response->assertRedirect('/login');
        $response->assertSessionHas('status', 'Your session expired. Please sign in again.');
    }

    public function test_valid_super_admin_login_succeeds(): void
    {
        Http::fake([
            '*/auth/v1/token*' => Http::response([
                'access_token' => 'mock_super_admin_token',
                'expires_in' => 3600,
                'user' => ['id' => '11111111-1111-1111-1111-111111111111'],
            ], 200),
            '*/rest/v1/profiles*' => Http::response([
                [
                    'id' => '11111111-1111-1111-1111-111111111111',
                    'full_name' => 'Super Admin',
                    'email' => 'admin@talentflow.ai',
                    'role' => 'super_admin',
                ],
            ], 200),
            '*/rest/v1/dashboard_auth_events*' => Http::response([], 201),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@talentflow.ai',
            'password' => 'SecurePassword123!',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('admin.profile.role', 'super_admin');
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        Http::fake([
            '*/auth/v1/token*' => Http::response([
                'error' => 'invalid_grant',
                'error_description' => 'Invalid login credentials',
            ], 400),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@talentflow.ai',
            'password' => 'WrongPassword!',
        ]);

        $response->assertSessionHasErrors(['email' => 'Unable to sign in with these credentials.']);
    }

    public function test_valid_non_super_admin_user_is_rejected(): void
    {
        Http::fake([
            '*/auth/v1/token*' => Http::response([
                'access_token' => 'mock_candidate_token',
                'expires_in' => 3600,
                'user' => ['id' => '22222222-2222-2222-2222-222222222222'],
            ], 200),
            '*/rest/v1/profiles*' => Http::response([
                [
                    'id' => '22222222-2222-2222-2222-222222222222',
                    'full_name' => 'John Candidate',
                    'email' => 'candidate@example.com',
                    'role' => 'candidate',
                ],
            ], 200),
            '*/auth/v1/logout*' => Http::response([], 204),
        ]);

        $response = $this->post('/login', [
            'email' => 'candidate@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertSessionHasErrors(['email' => 'Unable to sign in with these credentials.']);
        $this->assertNull(session('admin'));
    }

    public function test_supabase_service_unavailable_handled_cleanly(): void
    {
        Http::fake([
            '*/auth/v1/token*' => Http::response([], 503),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@talentflow.ai',
            'password' => 'Password123!',
        ]);

        $response->assertSessionHasErrors(['email' => 'Authentication service is currently unavailable. Please try again later.']);
    }

    public function test_logout_invalidates_session_and_redirects(): void
    {
        Http::fake([
            '*/auth/v1/user*' => Http::response([
                'id' => '11111111-1111-1111-1111-111111111111',
            ], 200),
            '*/rest/v1/profiles*' => Http::response([
                [
                    'id' => '11111111-1111-1111-1111-111111111111',
                    'full_name' => 'Super Admin',
                    'email' => 'admin@talentflow.ai',
                    'role' => 'super_admin',
                ],
            ], 200),
            '*/auth/v1/logout*' => Http::response([], 204),
            '*/rest/v1/dashboard_auth_events*' => Http::response([], 201),
        ]);

        $response = $this->withSession([
            'admin' => [
                'access_token' => 'valid_token',
                'expires_at' => time() + 3600,
                'profile' => ['id' => '11111111-1111-1111-1111-111111111111', 'role' => 'super_admin'],
            ],
        ])->post('/logout');

        $response->assertRedirect('/login');
        $response->assertSessionHas('status', 'You have been signed out.');
        $this->assertNull(session('admin'));
    }

    public function test_non_super_admin_authenticated_user_gets_403_access_denied(): void
    {
        Http::fake([
            '*/auth/v1/user*' => Http::response([
                'id' => '22222222-2222-2222-2222-222222222222',
            ], 200),
            '*/rest/v1/profiles*' => Http::response([
                [
                    'id' => '22222222-2222-2222-2222-222222222222',
                    'full_name' => 'Non Admin User',
                    'email' => 'user@example.com',
                    'role' => 'candidate',
                ],
            ], 200),
        ]);

        $response = $this->withSession([
            'admin' => [
                'access_token' => 'valid_token_for_non_admin',
                'expires_at' => time() + 3600,
                'profile' => ['id' => '22222222-2222-2222-2222-222222222222', 'role' => 'candidate'],
            ],
        ])->get('/');

        $response->assertStatus(403);
    }
}
