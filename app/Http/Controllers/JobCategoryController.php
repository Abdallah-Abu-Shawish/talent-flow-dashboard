<?php

namespace App\Http\Controllers;

use App\Exceptions\SupabaseException;
use App\Http\Requests\JobCategoryRequest;
use App\Services\Supabase\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class JobCategoryController extends Controller
{
    public function __construct(
        private Client $client,
    ) {}


    // =========================================================
    // INDEX
    // =========================================================

    public function index(
        Request $request,
    ): mixed {
        $token = $request
            ->session()
            ->get('admin.access_token');

        $rows = $this->client->select(
            'job_categories',
            [
                'select' =>
                    'id,name,slug,description,is_active,created_at,updated_at',

                'order' =>
                    'name.asc',
            ],
            $token,
        )->json();

        if (! is_array($rows)) {
            throw new SupabaseException;
        }

        return view(
            'job-categories.index',
            [
                'categories' => $rows,
            ],
        );
    }


    // =========================================================
    // CREATE
    // =========================================================

    public function create(): mixed
    {
        return view(
            'job-categories.form',
            [
                'category' => null,
            ],
        );
    }


    // =========================================================
    // STORE
    // =========================================================

    public function store(
        JobCategoryRequest $request,
    ): mixed {
        return $this->save(
            $request,
            null,
        );
    }


    // =========================================================
    // EDIT
    // =========================================================

    public function edit(
        Request $request,
        string $id,
    ): mixed {
        if (! Str::isUuid($id)) {
            abort(404);
        }

        $token = $request
            ->session()
            ->get('admin.access_token');

        $rows = $this->client->select(
            'job_categories',
            [
                'select' =>
                    'id,name,slug,description,is_active,created_at,updated_at',

                'id' =>
                    'eq.'.$id,

                'limit' =>
                    1,
            ],
            $token,
        )->json();

        if (
            ! is_array($rows)
            || ! isset($rows[0])
        ) {
            abort(404);
        }

        return view(
            'job-categories.form',
            [
                'category' =>
                    $rows[0],
            ],
        );
    }


    // =========================================================
    // UPDATE
    // =========================================================

    public function update(
        JobCategoryRequest $request,
        string $id,
    ): mixed {
        if (! Str::isUuid($id)) {
            abort(404);
        }

        return $this->save(
            $request,
            $id,
        );
    }


    // =========================================================
    // SAVE
    // =========================================================

    private function save(
        JobCategoryRequest $request,
        ?string $id,
    ): mixed {
        try {
            $data =
                $request->validated();

            $category =
                $this->client->rpc(
                    'dashboard_save_job_category',
                    [
                        'p_category_id' =>
                            $id,

                        'p_name' =>
                            $data['name'],

                        'p_description' =>
                            $data['description']
                            ?? null,

                        'p_is_active' =>
                            (bool) (
                                $data['is_active']
                                ?? true
                            ),

                        'p_expected_updated_at' =>
                            $data[
                                'expected_updated_at'
                            ]
                            ?? null,
                    ],
                    $request
                        ->session()
                        ->get(
                            'admin.access_token'
                        ),
                );

        } catch (SupabaseException $e) {

            if (
                in_array(
                    $e->status,
                    [
                        400,
                        409,
                        422,
                    ],
                    true,
                )
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'category' =>
                            $e->getMessage(),
                    ]);
            }

            throw $e;
        }


        if (
            ! Str::isUuid(
                $category['id'] ?? ''
            )
        ) {
            throw new SupabaseException;
        }


        return redirect()
            ->route(
                'job-categories.index',
            )
            ->with(
                'status',
                $id === null
                    ? 'Category created successfully.'
                    : 'Category updated successfully.',
            );
    }
}