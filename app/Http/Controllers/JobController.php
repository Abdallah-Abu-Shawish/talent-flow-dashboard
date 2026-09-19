<?php

namespace App\Http\Controllers;

use App\Exceptions\SupabaseException;
use App\Http\Requests\JobRequest;
use App\Repositories\DashboardRepository;
use App\Services\Supabase\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class JobController extends Controller
{
    public function __construct(
        private Client $client,
    ) {}


    // =========================================================
    // CREATE
    // =========================================================

    public function create(
        Request $request
    ): mixed {
        $token =
            $request
                ->session()
                ->get('admin.access_token');

        return view(
            'jobs.form',
            [
                'job' =>
                    null,

                'categories' =>
                    $this->loadCategories(
                        $token
                    ),

                'companies' =>
                    $this->loadCompanies(
                        $token
                    ),
            ]
        );
    }


    // =========================================================
    // STORE
    // =========================================================

    public function store(
        JobRequest $request,
        DashboardRepository $repository
    ): mixed {
        return $this->save(
            $request,
            $repository,
            null
        );
    }


    // =========================================================
    // EDIT
    // =========================================================

    public function edit(
        Request $request,
        string $id
    ): mixed {
        if (! Str::isUuid($id)) {
            abort(404);
        }


        $token =
            $request
                ->session()
                ->get('admin.access_token');


        $rows =
            $this->client->select(
                'job_roles',
                [
                    'select' =>
                        'id,category_id,company_id,title,slug,description,is_active,created_by,created_at,updated_at,'
                        .'creator:profiles!job_roles_created_by_fkey(full_name,email)',

                    'id' =>
                        'eq.'.$id,

                    'limit' =>
                        1,
                ],
                $token
            )->json();


        if (
            ! is_array($rows)
            || ! isset($rows[0])
        ) {
            abort(404);
        }


        return view(
            'jobs.form',
            [
                'job' =>
                    $rows[0],

                'categories' =>
                    $this->loadCategories(
                        $token
                    ),

                'companies' =>
                    $this->loadCompanies(
                        $token
                    ),
            ]
        );
    }


    // =========================================================
    // UPDATE
    // =========================================================

    public function update(
        JobRequest $request,
        string $id,
        DashboardRepository $repository
    ): mixed {
        if (! Str::isUuid($id)) {
            abort(404);
        }


        return $this->save(
            $request,
            $repository,
            $id
        );
    }


    // =========================================================
    // SAVE
    // =========================================================

    private function save(
        JobRequest $request,
        DashboardRepository $repository,
        ?string $id
    ): mixed {
        try {

            $data =
                $request->validated();


            $job =
                $repository->saveJob(
                    $data,
                    $id,
                    $request
                        ->session()
                        ->get(
                            'admin.access_token'
                        )
                );

        } catch (
            SupabaseException $e
        ) {

            if (
                in_array(
                    $e->status,
                    [
                        400,
                        409,
                        422,
                    ],
                    true
                )
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'job' =>
                            $e->getMessage(),
                    ]);
            }


            throw $e;
        }


        if (
            ! Str::isUuid(
                $job['id']
                ?? ''
            )
        ) {
            throw new SupabaseException;
        }


        return redirect()
            ->route(
                'jobs.show',
                $job['id']
            )
            ->with(
                'status',
                $id === null
                    ? 'Job created successfully.'
                    : 'Job updated successfully.'
            );
    }


    // =========================================================
    // LOAD CATEGORIES
    // =========================================================

    private function loadCategories(
        string $token
    ): array {
        $rows =
            $this->client->select(
                'job_categories',
                [
                    'select' =>
                        'id,name,slug,is_active',

                    'order' =>
                        'name.asc',
                ],
                $token
            )->json();


        if (! is_array($rows)) {
            throw new SupabaseException;
        }


        return $rows;
    }


    // =========================================================
    // LOAD COMPANIES
    // =========================================================

    private function loadCompanies(
        string $token
    ): array {
        $rows =
            $this->client->select(
                'companies',
                [
                    'select' =>
                        'id,name,slug',

                    'order' =>
                        'name.asc',
                ],
                $token
            )->json();


        if (! is_array($rows)) {
            throw new SupabaseException;
        }


        return $rows;
    }
}
