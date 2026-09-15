<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizationController;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Support\Facades\Route;

Route::middleware([SecurityHeaders::class])->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:10,1')->name('login.store');

    Route::middleware([EnsureSuperAdmin::class])->group(function () {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

        Route::get('/', [DashboardController::class, 'overview'])->name('overview');
        Route::get('/usage', [DashboardController::class, 'usage'])->name('usage.index');
        Route::get('/health', [DashboardController::class, 'health'])->name('health');
        Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');

        // Organizations custom routes (must precede resource show route)
        Route::get('/organizations/create', [OrganizationController::class, 'create'])->name('organizations.create');
        Route::post('/organizations', [OrganizationController::class, 'store'])->name('organizations.store');
        Route::get('/organizations/{id}/edit', [OrganizationController::class, 'edit'])->name('organizations.edit');
        Route::patch('/organizations/{id}', [OrganizationController::class, 'update'])->name('organizations.update');

        // Module index routes
        $modules = [
            'organizations' => 'organizations',
            'users' => 'users',
            'jobs' => 'jobs',
            'interviews' => 'interviews',
            'credits' => 'credits',
            'audit' => 'audit',
            'commands' => 'commands',
            'security' => 'security',
            'auth-events' => 'auth-events',
            'members' => 'members',
        ];

        foreach ($modules as $module => $path) {
            Route::get("/{$path}", [DashboardController::class, 'index'])
                ->defaults('module', $module)
                ->name("{$path}.index");
        }

        Route::get('/organizations/{id}', [DashboardController::class, 'show'])
            ->defaults('module', 'organizations')
            ->name('organizations.show');

        Route::get('/users/{id}', [DashboardController::class, 'show'])
            ->defaults('module', 'users')
            ->name('users.show');

        Route::get('/jobs/{id}', [DashboardController::class, 'show'])
            ->defaults('module', 'jobs')
            ->name('jobs.show');

        Route::get('/interviews/{id}', [DashboardController::class, 'show'])
            ->defaults('module', 'interviews')
            ->name('interviews.show');

        Route::get('/audit/{id}', [DashboardController::class, 'show'])
            ->defaults('module', 'audit')
            ->name('audit.show');
    });
});

Route::get('/speed-test', function () {
    return response('OK', 200);
});
