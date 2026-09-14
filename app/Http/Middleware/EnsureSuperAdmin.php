<?php

namespace App\Http\Middleware;

use App\Exceptions\SupabaseException;
use App\Services\AdminAuthService;
use App\Services\AuthEventRecorder;
use Closure;
use Illuminate\Http\Request;

final class EnsureSuperAdmin
{
    public function __construct(private AdminAuthService $auth, private AuthEventRecorder $events) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $token = $request->session()->get('admin.access_token');
        if (! is_string($token) || $token === '') {
            return redirect()->route('login');
        }
        if ((int) $request->session()->get('admin.expires_at', 0) <= time()) {
            return $this->expire($request);
        }
        try {
            $profile = $this->auth->currentProfile($token);
        } catch (SupabaseException $e) {
            if ($e->status === 401) {
                return $this->expire($request);
            }
            throw $e;
        }
        if (($profile['role'] ?? null) !== 'super_admin') {
            $actor = $profile['id'] ?? null;
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            try {
                $this->events->record('access_denied', 'denied', $actor);
            } catch (SupabaseException) {}
            abort(403);
        }
        $request->session()->put('admin.profile', $profile);
        $request->attributes->set('admin_profile', $profile);
        return $next($request);
    }

    private function expire(Request $request): mixed
    {
        $actor = $request->session()->get('admin.profile.id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        try {
            $this->events->record('session_expired', 'denied', $actor);
        } catch (SupabaseException) {}
        return redirect()->route('login')->with('status', 'Your session expired. Please sign in again.');
    }
}
