@extends('layouts.app')

@section('title', 'Edit user')

@section('content')

<x-page-heading
    title="Edit user"
    description="Manage profile information, account access and organization roles."
>
    <a
        class="button button-secondary"
        href="{{ route('users.show', $profile['id']) }}"
    >
        Back to user
    </a>
</x-page-heading>


<div class="user-admin-layout">

    {{-- =====================================================
         PROFILE
    ====================================================== --}}

    <section class="card">

        <div class="card-heading">
            <div>
                <h2>Profile information</h2>

                <p>
                    Update the user's account information, global role and company request permission.
                </p>
            </div>

            <x-icon name="users" />
        </div>


        <form
            method="POST"
            action="{{ route('users.update', $profile['id']) }}"
        >
            @csrf
            @method('PATCH')

            <div class="form-fields">

                {{-- FULL NAME --}}
                <div class="form-field">
                    <label for="full_name">
                        Full name
                    </label>

                    <input
                        id="full_name"
                        name="full_name"
                        type="text"
                        value="{{ old('full_name', $profile['full_name'] ?? '') }}"
                        maxlength="120"
                        required
                    >
                </div>


                {{-- EMAIL --}}
                <div class="form-field">
                    <label for="email">
                        Email address
                    </label>

                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $profile['email'] ?? '') }}"
                        maxlength="254"
                        required
                    >
                </div>


                {{-- PHONE --}}
                <div class="form-field">
                    <label for="phone_number">
                        Phone number
                    </label>

                    <input
                        id="phone_number"
                        name="phone_number"
                        type="text"
                        value="{{ old('phone_number', $profile['phone_number'] ?? '') }}"
                        placeholder="+393521234567"
                        maxlength="30"
                    >
                </div>


                {{-- GLOBAL ROLE --}}
                <div class="form-field">
                    <label for="role">
                        Global role
                    </label>

                    <select
                        id="role"
                        name="role"
                        required
                    >
                        <option
                            value="candidate"
                            @selected(
                                old(
                                    'role',
                                    $profile['role'] ?? ''
                                ) === 'candidate'
                            )
                        >
                            Candidate
                        </option>

                        <option
                            value="super_admin"
                            @selected(
                                old(
                                    'role',
                                    $profile['role'] ?? ''
                                ) === 'super_admin'
                            )
                        >
                            Super Admin
                        </option>
                    </select>

                    <p class="field-hint">
                        Company Admin and Interviewer access are managed separately below.
                    </p>
                </div>


                {{-- COMPANY CREATION REQUEST PERMISSION --}}
                <div class="form-field">
                    <label for="company_request_enabled">
                        Company creation request
                    </label>

                    <select
                        id="company_request_enabled"
                        name="company_request_enabled"
                        required
                    >
                        <option
                            value="0"
                            @selected(
                                (string) old(
                                    'company_request_enabled',
                                    !empty(
                                        $profile[
                                            'company_request_enabled'
                                        ]
                                    )
                                        ? '1'
                                        : '0'
                                ) === '0'
                            )
                        >
                            Not allowed
                        </option>

                        <option
                            value="1"
                            @selected(
                                (string) old(
                                    'company_request_enabled',
                                    !empty(
                                        $profile[
                                            'company_request_enabled'
                                        ]
                                    )
                                        ? '1'
                                        : '0'
                                ) === '1'
                            )
                        >
                            Allowed
                        </option>
                    </select>

                    <p class="field-hint">
                        Allows this user to submit one company creation request for Super Admin review.
                    </p>
                </div>

            </div>


            <div class="form-footer">

                <button
                    type="submit"
                    class="button button-primary"
                >
                    Save changes
                </button>

            </div>

        </form>

    </section>


    {{-- =====================================================
         COMPANY ACCESS
    ====================================================== --}}

    <section class="card">

        <div class="card-heading">

            <div>

                <h2>
                    Company access
                </h2>

                <p>
                    Assign this user to a company as an admin or interviewer.
                </p>

            </div>

            <x-icon name="building" />

        </div>


        <form
            method="POST"
            action="{{ route('users.membership.store', $profile['id']) }}"
        >
            @csrf

            <div class="form-fields">

                {{-- COMPANY --}}
                <div class="form-field">

                    <label for="company_id">
                        Company
                    </label>

                    <select
                        id="company_id"
                        name="company_id"
                        required
                    >
                        <option value="">
                            Select company
                        </option>

                        @foreach($companies as $company)

                            <option
                                value="{{ $company['id'] }}"
                            >
                                {{ $company['name'] }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- COMPANY ROLE --}}
                <div class="form-field">

                    <label for="company_role">
                        Company role
                    </label>

                    <select
                        id="company_role"
                        name="company_role"
                        required
                    >
                        <option
                            value=""
                            selected
                            disabled
                        >
                            Select role
                        </option>

                        <option value="company_admin">
                            Company Admin
                        </option>

                        <option value="interviewer">
                            Interviewer
                        </option>

                    </select>

                    <p class="field-hint">
                        Choose the access level this user should have in the selected company.
                    </p>

                </div>

            </div>


            <div class="form-footer">

                <button
                    type="submit"
                    class="button button-primary"
                >
                    Save company access
                </button>

            </div>

        </form>


        @if(!empty($memberships))

            <div class="user-memberships">

                @foreach($memberships as $membership)

                    <div class="user-membership-row">

                        <div>

                            <strong>
                                {{
                                    $membership[
                                        'company'
                                    ][
                                        'name'
                                    ]
                                    ?? 'Unknown company'
                                }}
                            </strong>

                            <small>
                                {{
                                    match(
                                        $membership[
                                            'role'
                                        ]
                                        ?? ''
                                    ) {
                                        'company_admin'
                                            => 'Company Admin',

                                        'interviewer'
                                            => 'Interviewer',

                                        default
                                            => $membership[
                                                'role'
                                            ]
                                            ?? 'Unknown'
                                    }
                                }}
                            </small>

                        </div>


                        <form
                            method="POST"
                            action="{{
                                route(
                                    'users.membership.destroy',
                                    [
                                        $profile['id'],
                                        $membership['id']
                                    ]
                                )
                            }}"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="button button-secondary"
                                onclick="return confirm(
                                    'Remove this company access?'
                                )"
                            >
                                Remove
                            </button>

                        </form>

                    </div>

                @endforeach

            </div>

        @endif

    </section>


    {{-- =====================================================
         PASSWORD
    ====================================================== --}}

    <section class="card">

        <div class="card-heading">

            <div>

                <h2>
                    Password & security
                </h2>

                <p>
                    Send a reset email or assign a new password.
                </p>

            </div>

            <x-icon name="shield" />

        </div>


        <div class="form-fields">

            {{-- PASSWORD RESET EMAIL --}}
            <form
                method="POST"
                action="{{ route('users.password-reset', $profile['id']) }}"
            >
                @csrf

                <button
                    type="submit"
                    class="button button-secondary"
                >
                    Send password reset email
                </button>

            </form>


            {{-- MANUAL PASSWORD --}}
            <form
                method="POST"
                action="{{ route('users.password', $profile['id']) }}"
            >
                @csrf

                <div class="form-field">

                    <label for="password">
                        New password
                    </label>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        minlength="8"
                        maxlength="128"
                        autocomplete="new-password"
                    >

                </div>


                <div class="form-field">

                    <label for="password_confirmation">
                        Confirm password
                    </label>

                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        minlength="8"
                        maxlength="128"
                        autocomplete="new-password"
                    >

                </div>


                <button
                    type="submit"
                    class="button button-primary"
                >
                    Set new password
                </button>

            </form>

        </div>

    </section>


    {{-- =====================================================
         DANGER ZONE
    ====================================================== --}}

    <section class="card user-danger-card">

        <div class="card-heading">

            <div>

                <h2>
                    Danger zone
                </h2>

                <p>
                    Permanently delete this user account.
                </p>

            </div>

        </div>


        <div class="user-danger-body">

            <div>

                <strong>
                    Delete user
                </strong>

                <p>
                    This action cannot be undone.
                </p>

            </div>


            <form
                method="POST"
                action="{{ route('users.destroy', $profile['id']) }}"
            >
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="button user-delete-button"
                    onclick="return confirm(
                        'Are you sure you want to permanently delete this user?'
                    )"
                >
                    Delete user
                </button>

            </form>

        </div>

    </section>

</div>

@endsection