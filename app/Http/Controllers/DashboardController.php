<?php

namespace App\Http\Controllers;

use App\Repositories\DashboardRepository;
use App\Services\DashboardPresenter;
use App\Services\Supabase\Client;
use App\Support\DashboardFilters;
use App\Support\DashboardModules;
use Illuminate\Http\Request;

final class DashboardController extends Controller
{
    // =========================================================
    // MODULE INDEX
    // =========================================================

    public function index(
        Request $request,
        DashboardPresenter $presenter,
    ): mixed {
        $module =
            $request->route()->defaults['module'];

        $filters =
            DashboardFilters::fromRequest(
                $request,
                DashboardModules::get($module),
            );

        $token =
            $request
                ->session()
                ->get('admin.access_token');

        $primaryAction =
            match ($module) {
                'jobs' => [
                    'label' =>
                        'Create Job',

                    'url' =>
                        route('jobs.create'),
                ],

                default =>
                    null,
            };

        return view(
            'dashboard.index',
            [
                'module' =>
                    $module,

                'table' =>
                    $presenter->table(
                        $module,
                        $filters,
                        $token,
                    ),

                'primaryAction' =>
                    $primaryAction,
            ],
        );
    }


    // =========================================================
    // MODULE DETAIL
    // =========================================================

    public function show(
        Request $request,
        string $id,
        DashboardPresenter $presenter,
    ): mixed {
        $module =
            $request->route()->defaults['module'];

        $token =
            $request
                ->session()
                ->get('admin.access_token');

        return view(
            'dashboard.show',
            [
                'module' =>
                    $module,

                'detail' =>
                    $presenter->detail(
                        $module,
                        $id,
                        $token,
                    ),
            ],
        );
    }


    // =========================================================
    // OVERVIEW
    // =========================================================

    public function overview(
        Request $request,
        DashboardRepository $repository,
    ): mixed {
        $filters =
            DashboardFilters::fromRequest(
                $request,
                DashboardModules::get('usage'),
                true,
            );

        $token =
            $request
                ->session()
                ->get('admin.access_token');

        return view(
            'dashboard.overview',
            [
                'summary' =>
                    $repository->summary(
                        $filters,
                        $token,
                    ),

                'filters' =>
                    $filters,
            ],
        );
    }


    // =========================================================
    // USAGE
    // =========================================================

    public function usage(
        Request $request,
        DashboardRepository $repository,
        DashboardPresenter $presenter,
    ): mixed {
        $filters =
            DashboardFilters::fromRequest(
                $request,
                DashboardModules::get('usage'),
                true,
            );

        $token =
            $request
                ->session()
                ->get('admin.access_token');

        return view(
            'dashboard.usage',
            [
                'summary' =>
                    $repository->summary(
                        $filters,
                        $token,
                    ),

                'filters' =>
                    $filters,

                'table' =>
                    $presenter->table(
                        'usage',
                        $filters,
                        $token,
                    ),
            ],
        );
    }


    // =========================================================
    // SYSTEM HEALTH
    // =========================================================

    public function health(): mixed
    {
        return view(
            'dashboard.health',
        );
    }


    // =========================================================
    // SETTINGS
    // =========================================================

    public function settings(
        Client $client,
    ): mixed {
        return view(
            'dashboard.settings',
            [
                'integrations' => [

                    [
                        'name' =>
                            'Supabase dashboard access',

                        'configured' =>
                            $client->configured(),

                        'description' =>
                            'Required server configuration is present. This status does not verify schema installation or connectivity.',
                    ],

                    [
                        'name' =>
                            'Python worker pool',

                        'configured' =>
                            false,

                        'description' =>
                            'No deployed worker registry or heartbeat producer is connected.',
                    ],

                    [
                        'name' =>
                            'Gemini',

                        'configured' =>
                            false,

                        'description' =>
                            'Provider adapters and request telemetry belong to the Python runtime. No runtime integration registry is available.',
                    ],

                    [
                        'name' =>
                            'LiveKit / WebRTC',

                        'configured' =>
                            false,

                        'description' =>
                            'Interview records can reference sessions; connection observations and administrative commands are not implemented.',
                    ],

                    [
                        'name' =>
                            'Google Calendar & Gmail',

                        'configured' =>
                            false,

                        'description' =>
                            'Runtime OAuth adapters and action events are not configured. Codex development MCP connections are separate.',
                    ],

                ],
            ],
        );
    }
}