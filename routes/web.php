<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\UserAdminController;

use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Controllers\JobCategoryController;

use Illuminate\Support\Facades\Route;


// =========================================================
// SECURED APPLICATION ROUTES
// =========================================================

Route::middleware([
    SecurityHeaders::class,
])->group(function () {

    // =====================================================
    // AUTHENTICATION
    // =====================================================

    Route::get(
        '/login',
        [
            AuthController::class,
            'create',
        ],
    )->name('login');


    Route::post(
        '/login',
        [
            AuthController::class,
            'store',
        ],
    )
        ->middleware('throttle:10,1')
        ->name('login.store');


    // =====================================================
    // SUPER ADMIN ONLY
    // =====================================================

    Route::middleware([
        EnsureSuperAdmin::class,
    ])->group(function () {

        // =================================================
        // LOGOUT
        // =================================================

        Route::post(
            '/logout',
            [
                AuthController::class,
                'destroy',
            ],
        )->name('logout');


        // =================================================
        // DASHBOARD
        // =================================================

        Route::get(
            '/',
            [
                DashboardController::class,
                'overview',
            ],
        )->name('overview');


        Route::get(
            '/usage',
            [
                DashboardController::class,
                'usage',
            ],
        )->name('usage.index');


        Route::get(
            '/health',
            [
                DashboardController::class,
                'health',
            ],
        )->name('health');


        Route::get(
            '/settings',
            [
                DashboardController::class,
                'settings',
            ],
        )->name('settings');


        // =================================================
        // COMPANY CREATION REQUESTS
        // =================================================

        Route::get(
            '/company-requests',
            [
                CompanyRequestController::class,
                'index',
            ],
        )->name('company-requests.index');


        Route::get(
            '/company-requests/{id}',
            [
                CompanyRequestController::class,
                'show',
            ],
        )->name('company-requests.show');


        Route::post(
            '/company-requests/{id}/approve',
            [
                CompanyRequestController::class,
                'approve',
            ],
        )->name('company-requests.approve');


        Route::post(
            '/company-requests/{id}/reject',
            [
                CompanyRequestController::class,
                'reject',
            ],
        )->name('company-requests.reject');


        // =================================================
        // ORGANIZATIONS
        // Custom routes must precede /organizations/{id}
        // =================================================

        Route::get(
            '/organizations/create',
            [
                OrganizationController::class,
                'create',
            ],
        )->name('organizations.create');


        Route::post(
            '/organizations',
            [
                OrganizationController::class,
                'store',
            ],
        )->name('organizations.store');


        Route::get(
            '/organizations/{id}/edit',
            [
                OrganizationController::class,
                'edit',
            ],
        )->name('organizations.edit');


        Route::patch(
            '/organizations/{id}',
            [
                OrganizationController::class,
                'update',
            ],
        )->name('organizations.update');


        // =================================================
        // JOBS
        // Custom routes must precede /jobs/{id}
        // =================================================

        Route::get(
            '/jobs/create',
            [
                JobController::class,
                'create',
            ],
        )->name('jobs.create');


        Route::post(
            '/jobs',
            [
                JobController::class,
                'store',
            ],
        )->name('jobs.store');


        Route::get(
            '/jobs/{id}/edit',
            [
                JobController::class,
                'edit',
            ],
        )->name('jobs.edit');


        Route::patch(
            '/jobs/{id}',
            [
                JobController::class,
                'update',
            ],
        )->name('jobs.update');

        // =========================================================
        // JOB CATEGORIES
        // =========================================================

        Route::get(
            '/job-categories',
            [
                JobCategoryController::class,
                'index',
            ],
        )->name('job-categories.index');


        Route::get(
            '/job-categories/create',
            [
                JobCategoryController::class,
                'create',
            ],
        )->name('job-categories.create');


        Route::post(
            '/job-categories',
            [
                JobCategoryController::class,
                'store',
            ],
        )->name('job-categories.store');


        Route::get(
            '/job-categories/{id}/edit',
            [
                JobCategoryController::class,
                'edit',
            ],
        )->name('job-categories.edit');


        Route::patch(
            '/job-categories/{id}',
            [
                JobCategoryController::class,
                'update',
            ],
        )->name('job-categories.update');

        // =================================================
        // MODULE INDEX ROUTES
        // =================================================

        $modules = [
            'organizations' =>
                'organizations',

            'users' =>
                'users',

            'jobs' =>
                'jobs',

            'interviews' =>
                'interviews',

            'credits' =>
                'credits',

            'audit' =>
                'audit',

            'commands' =>
                'commands',

            'security' =>
                'security',

            'auth-events' =>
                'auth-events',

            'members' =>
                'members',
        ];


        foreach (
            $modules
            as $module => $path
        ) {
            Route::get(
                "/{$path}",
                [
                    DashboardController::class,
                    'index',
                ],
            )
                ->defaults(
                    'module',
                    $module,
                )
                ->name(
                    "{$path}.index",
                );
        }


        // =================================================
        // USER ADMINISTRATION
        // =================================================

        Route::get(
            '/users/{id}/edit',
            [
                UserAdminController::class,
                'edit',
            ],
        )->name('users.edit');


        Route::patch(
            '/users/{id}',
            [
                UserAdminController::class,
                'update',
            ],
        )->name('users.update');


        Route::post(
            '/users/{id}/password',
            [
                UserAdminController::class,
                'setPassword',
            ],
        )->name('users.password');


        Route::post(
            '/users/{id}/password-reset',
            [
                UserAdminController::class,
                'sendPasswordReset',
            ],
        )->name('users.password-reset');


        Route::post(
            '/users/{id}/membership',
            [
                UserAdminController::class,
                'saveMembership',
            ],
        )->name('users.membership.store');


        Route::delete(
            '/users/{id}/membership/{membershipId}',
            [
                UserAdminController::class,
                'removeMembership',
            ],
        )->name(
            'users.membership.destroy',
        );


        Route::delete(
            '/users/{id}',
            [
                UserAdminController::class,
                'destroy',
            ],
        )->name('users.destroy');


        // =================================================
        // DETAIL / SHOW ROUTES
        // Keep these after custom create/edit routes
        // =================================================

        Route::get(
            '/organizations/{id}',
            [
                DashboardController::class,
                'show',
            ],
        )
            ->defaults(
                'module',
                'organizations',
            )
            ->name(
                'organizations.show',
            );


        Route::get(
            '/users/{id}',
            [
                DashboardController::class,
                'show',
            ],
        )
            ->defaults(
                'module',
                'users',
            )
            ->name(
                'users.show',
            );


        Route::get(
            '/jobs/{id}',
            [
                DashboardController::class,
                'show',
            ],
        )
            ->defaults(
                'module',
                'jobs',
            )
            ->name(
                'jobs.show',
            );


        Route::get(
            '/interviews/{id}',
            [
                DashboardController::class,
                'show',
            ],
        )
            ->defaults(
                'module',
                'interviews',
            )
            ->name(
                'interviews.show',
            );


        Route::get(
            '/audit/{id}',
            [
                DashboardController::class,
                'show',
            ],
        )
            ->defaults(
                'module',
                'audit',
            )
            ->name(
                'audit.show',
            );
    });
});


// =========================================================
// SPEED TEST
// =========================================================

Route::get(
    '/speed-test',
    function () {
        return response(
            'OK',
            200,
        );
    },
);