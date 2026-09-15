<?php

namespace App\Http\Controllers;

use App\Services\Supabase\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exceptions\SupabaseException;

final class UserAdminController extends Controller
{
    public function __construct(
        private Client $client,
    ) {}

    // =========================================================
    // EDIT USER
    // =========================================================

    public function edit(
        Request $request,
        string $id,
    ): View {
        $profileRows = $this->client
            ->serviceSelect(
                'profiles',
                [
                    'select' =>
                        'id,email,full_name,phone_number,role,created_at,updated_at',
                    'id' => 'eq.'.$id,
                    'limit' => 1,
                ]
            )
            ->json();

        abort_unless(
            is_array($profileRows)
            && isset($profileRows[0]),
            404
        );

        $profile = $profileRows[0];

        $authUser =
            $this->client->adminGetUser($id);

        $companies =
            $this->client
                ->serviceSelect(
                    'companies',
                    [
                        'select' =>
                            'id,name,slug',
                        'order' =>
                            'name.asc',
                    ]
                )
                ->json();

        $memberships =
            $this->client
                ->serviceSelect(
                    'company_members',
                    [
                        'select' =>
                            'id,company_id,user_id,role,created_at,company:companies(name)',
                        'user_id' =>
                            'eq.'.$id,
                        'order' =>
                            'created_at.desc',
                    ]
                )
                ->json();

        return view(
            'dashboard.users.edit',
            [
                'profile' =>
                    $profile,

                'authUser' =>
                    $authUser,

                'companies' =>
                    is_array($companies)
                        ? $companies
                        : [],

                'memberships' =>
                    is_array($memberships)
                        ? $memberships
                        : [],
            ]
        );
    }

    // =========================================================
    // UPDATE USER PROFILE
    // =========================================================

    public function update(
        Request $request,
        string $id,
    ): RedirectResponse {
        $validated =
            $request->validate([
                'full_name' => [
                    'required',
                    'string',
                    'max:120',
                ],

                'email' => [
                    'required',
                    'email',
                    'max:254',
                ],

                'phone_number' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'role' => [
                    'required',
                    'in:candidate,super_admin',
                ],
            ]);

        $currentAdminId =
            (string) $request
                ->session()
                ->get('admin.profile.id');

        /*
         * Prevent accidentally removing your own
         * super-admin access.
         */
        if (
            $id === $currentAdminId
            && $validated['role']
                !== 'super_admin'
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'You cannot remove your own super-admin role.'
                );
        }

        $email =
            mb_strtolower(
                trim(
                    $validated['email']
                )
            );

        $fullName =
            trim(
                $validated['full_name']
            );

        $phone =
            isset(
                $validated['phone_number']
            )
                ? trim(
                    $validated['phone_number']
                )
                : '';

        /*
         * Update Supabase Auth first.
         */
        $this->client
            ->adminUpdateUser(
                $id,
                [
                    'email' =>
                        $email,

                    'email_confirm' =>
                        true,

                    'user_metadata' => [
                        'full_name' =>
                            $fullName,
                    ],
                ]
            );

        /*
         * Then synchronize public.profiles.
         */
        $this->client
            ->serviceUpdate(
                'profiles',
                [
                    'id' =>
                        'eq.'.$id,
                ],
                [
                    'email' =>
                        $email,

                    'full_name' =>
                        $fullName,

                    'phone_number' =>
                        $phone !== ''
                            ? $phone
                            : null,

                    'role' =>
                        $validated['role'],

                    'updated_at' =>
                        now()
                            ->utc()
                            ->toIso8601String(),
                ]
            );

        return redirect()
            ->route(
                'users.show',
                $id
            )
            ->with(
                'success',
                'User updated successfully.'
            );
    }

    // =========================================================
    // SET NEW PASSWORD
    // =========================================================

    public function setPassword(
        Request $request,
        string $id,
    ): RedirectResponse {
        $validated =
            $request->validate([
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:128',
                    'confirmed',
                ],
            ]);

        $this->client
            ->adminSetPassword(
                $id,
                $validated['password']
            );

        return back()
            ->with(
                'success',
                'Password updated successfully.'
            );
    }

    // =========================================================
    // SEND PASSWORD RESET
    // =========================================================

 public function sendPasswordReset(
    string $id,
): RedirectResponse {
    $authUser =
        $this->client
            ->adminGetUser($id);

    $email = trim(
        (string) (
            $authUser['email']
            ?? ''
        )
    );

    abort_if(
        $email === '',
        404
    );

    try {
        $this->client
            ->sendPasswordRecovery(
                $email
            );
    } catch (SupabaseException $exception) {

        return back()->with(
            'error',
            'Could not send the password reset email. '
            .'If a reset was recently requested, wait about 60 seconds and try again.'
        );
    }

    return back()->with(
        'success',
        'Password reset email sent to '.$email.'.'
    );
}

    // =========================================================
    // ADD / UPDATE COMPANY ACCESS
    // =========================================================

    public function saveMembership(
        Request $request,
        string $id,
    ): RedirectResponse {
        $validated =
            $request->validate([
                'company_id' => [
                    'required',
                    'uuid',
                ],

                'company_role' => [
                    'required',
                    'in:company_admin,interviewer',
                ],
            ]);

        $existing =
            $this->client
                ->serviceSelect(
                    'company_members',
                    [
                        'select' =>
                            'id',

                        'user_id' =>
                            'eq.'.$id,

                        'company_id' =>
                            'eq.'.$validated[
                                'company_id'
                            ],

                        'limit' =>
                            1,
                    ]
                )
                ->json();

        if (
            is_array($existing)
            && isset($existing[0]['id'])
        ) {
            $this->client
                ->serviceUpdate(
                    'company_members',
                    [
                        'id' =>
                            'eq.'
                            .$existing[0]['id'],
                    ],
                    [
                        'role' =>
                            $validated[
                                'company_role'
                            ],
                    ]
                );
        } else {
            $this->client
                ->serviceInsert(
                    'company_members',
                    [
                        'user_id' =>
                            $id,

                        'company_id' =>
                            $validated[
                                'company_id'
                            ],

                        'role' =>
                            $validated[
                                'company_role'
                            ],
                    ]
                );
        }

        return back()
            ->with(
                'success',
                'Company access updated successfully.'
            );
    }

    // =========================================================
    // REMOVE COMPANY ACCESS
    // =========================================================

    public function removeMembership(
        Request $request,
        string $id,
        string $membershipId,
    ): RedirectResponse {
        /*
         * user_id is included so one user's membership
         * cannot be removed through another user's URL.
         */
        $this->client
            ->serviceDelete(
                'company_members',
                [
                    'id' =>
                        'eq.'.$membershipId,

                    'user_id' =>
                        'eq.'.$id,
                ]
            );

        return back()
            ->with(
                'success',
                'Company access removed.'
            );
    }

    // =========================================================
    // DELETE USER
    // =========================================================

    public function destroy(
        Request $request,
        string $id,
    ): RedirectResponse {
        $currentAdminId =
            (string) $request
                ->session()
                ->get('admin.profile.id');

        if ($id === $currentAdminId) {
            return back()
                ->with(
                    'error',
                    'You cannot delete your own account.'
                );
        }

        /*
         * Delete Auth user.
         *
         * Database foreign-key behavior should handle
         * dependent records according to the schema.
         */
        $this->client
            ->adminDeleteUser(
                $id,
                false
            );

        return redirect()
            ->route('users.index')
            ->with(
                'success',
                'User deleted successfully.'
            );
    }
}