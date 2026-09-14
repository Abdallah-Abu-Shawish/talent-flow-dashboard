<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AdminAuthService;
use App\Services\Supabase\Client;
use Illuminate\Http\Request;

final class AuthController extends Controller
{
    public function create(Client $client): mixed
    {
        return view('auth.login', ['configured' => $client->configured()]);
    }

    public function store(LoginRequest $request, AdminAuthService $auth): mixed
    {
        $auth->login($request, $request->safe()->only(['email', 'password']));
        return redirect()->route('overview');
    }

    public function destroy(Request $request, AdminAuthService $auth): mixed
    {
        $auth->logout($request);
        return redirect()->route('login')->with('status', 'You have been signed out.');
    }
}
