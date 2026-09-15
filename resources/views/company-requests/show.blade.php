@extends('layouts.app')

@section('title', 'Company Request')

@section('content')

    @php
        $status = ucfirst(
            $companyRequest['status'] ?? 'pending'
        );

        $submittedAt = ! empty($companyRequest['created_at'])
            ? \Illuminate\Support\Carbon::parse(
                $companyRequest['created_at']
            )->format('Y-m-d H:i')
            : '—';

        $updatedAt = ! empty($companyRequest['updated_at'])
            ? \Illuminate\Support\Carbon::parse(
                $companyRequest['updated_at']
            )->format('Y-m-d H:i')
            : '—';

        $reviewedAt = ! empty($companyRequest['reviewed_at'])
            ? \Illuminate\Support\Carbon::parse(
                $companyRequest['reviewed_at']
            )->format('Y-m-d H:i')
            : '—';
    @endphp


    {{-- ========================================================= --}}
    {{-- PAGE HEADING --}}
    {{-- ========================================================= --}}

    <x-page-heading
        title="{{ $companyRequest['company_name'] ?? 'Company Request' }}"
        description="Review the company information and requester details before approving or rejecting this request."
    >
        <a
            class="button button-secondary"
            href="{{ route('company-requests.index') }}"
        >
            ← Back to requests
        </a>
    </x-page-heading>


    {{-- ========================================================= --}}
    {{-- STATUS --}}
    {{-- ========================================================= --}}

    <section class="card table-card">

        <div class="card-heading">

            <div>
                <h2>Request Status</h2>

                <p>
                    Current state of this company creation request.
                </p>
            </div>

            <x-badge :value="$status" />

        </div>

        <div
            class="table-scroll"
            role="region"
            aria-label="Request status"
            tabindex="0"
        >

            <table>

                <tbody>

                    <tr>

                        <th scope="row">
                            Request ID
                        </th>

                        <td class="monospace">
                            {{ $companyRequest['id'] ?? '—' }}
                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            Submitted
                        </th>

                        <td>
                            {{ $submittedAt }} UTC
                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            Last Updated
                        </th>

                        <td>
                            {{ $updatedAt }} UTC
                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            Reviewed At
                        </th>

                        <td>
                            {{ $reviewedAt }}
                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            Review Note
                        </th>

                        <td>
                            {{ $companyRequest['review_note'] ?? '—' }}
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- COMPANY INFORMATION --}}
    {{-- ========================================================= --}}

    <section class="card table-card">

        <div class="card-heading">

            <div>

                <h2>
                    Company Information
                </h2>

                <p>
                    Information provided by the requester.
                </p>

            </div>

        </div>

        <div
            class="table-scroll"
            role="region"
            aria-label="Company information"
            tabindex="0"
        >

            <table>

                <tbody>

                    <tr>

                        <th scope="row">
                            Company Name
                        </th>

                        <td>
                            {{ $companyRequest['company_name'] ?? '—' }}
                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            Requested Slug
                        </th>

                        <td class="monospace">
                            {{ $companyRequest['requested_slug'] ?? '—' }}
                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            Business Email
                        </th>

                        <td>
                            {{ $companyRequest['business_email'] ?? '—' }}
                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            Phone Number
                        </th>

                        <td>
                            {{ $companyRequest['phone_number'] ?? '—' }}
                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            Website
                        </th>

                        <td>

                            @if(!empty($companyRequest['website']))

                                <a
                                    class="cell-link"
                                    href="{{ $companyRequest['website'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    {{ $companyRequest['website'] }}
                                </a>

                            @else

                                <span class="muted">
                                    —
                                </span>

                            @endif

                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            Industry
                        </th>

                        <td>
                            {{ $companyRequest['industry'] ?? '—' }}
                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            Company Size
                        </th>

                        <td>
                            {{ $companyRequest['company_size'] ?? '—' }}
                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            Country
                        </th>

                        <td>
                            {{ $companyRequest['country'] ?? '—' }}
                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            City
                        </th>

                        <td>
                            {{ $companyRequest['city'] ?? '—' }}
                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            Description
                        </th>

                        <td>
                            {{ $companyRequest['description'] ?? '—' }}
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- REQUESTER INFORMATION --}}
    {{-- ========================================================= --}}

    <section class="card table-card">

        <div class="card-heading">

            <div>

                <h2>
                    Requester Information
                </h2>

                <p>
                    TalentFlow account that submitted this request.
                </p>

            </div>

        </div>

        <div
            class="table-scroll"
            role="region"
            aria-label="Requester information"
            tabindex="0"
        >

            <table>

                <tbody>

                    <tr>

                        <th scope="row">
                            Full Name
                        </th>

                        <td>
                            {{ $requester['full_name'] ?? '—' }}
                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            Email
                        </th>

                        <td>
                            {{ $requester['email'] ?? '—' }}
                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            Phone Number
                        </th>

                        <td>
                            {{ $requester['phone_number'] ?? '—' }}
                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            User ID
                        </th>

                        <td class="monospace">

                            {{
                                $requester['id']
                                ?? $companyRequest['requested_by']
                                ?? '—'
                            }}

                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            Account Role
                        </th>

                        <td>

                            <x-badge
                                :value="$requester['role'] ?? 'candidate'"
                            />

                        </td>

                    </tr>


                    <tr>

                        <th scope="row">
                            Company Request Permission
                        </th>

                        <td>

                            @if(
                                ($requester['company_request_enabled'] ?? false)
                                === true
                            )

                                <x-badge value="Allowed" />

                            @else

                                <x-badge value="Not Allowed" />

                            @endif

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- RESULT / CREATED COMPANY --}}
    {{-- ========================================================= --}}

    @if(!empty($companyRequest['company_id']))

        <section class="card table-card">

            <div class="card-heading">

                <div>

                    <h2>
                        Created Company
                    </h2>

                    <p>
                        Company created from this request.
                    </p>

                </div>

            </div>

            <div
                class="table-scroll"
                role="region"
                aria-label="Created company"
                tabindex="0"
            >

                <table>

                    <tbody>

                        <tr>

                            <th scope="row">
                                Company ID
                            </th>

                            <td class="monospace">
                                {{ $companyRequest['company_id'] }}
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </section>

    @endif


    {{-- ========================================================= --}}
    {{-- ADMIN ACTIONS --}}
    {{-- ========================================================= --}}

    @if(($companyRequest['status'] ?? null) === 'pending')

        <section class="card">

            <div class="card-heading">

                <div>

                    <h2>
                        Review Request
                    </h2>

                    <p>
                        Approving will create the company and assign
                        the requester as Company Admin.
                    </p>

                </div>


                <div
                    style="
                        display: flex;
                        gap: 10px;
                        flex-wrap: wrap;
                        align-items: center;
                    "
                >

                    {{-- Reject will be connected next --}}
                  <form
    method="POST"
    action="{{ route(
        'company-requests.reject',
        $companyRequest['id']
    ) }}"
    style="display: inline;"
    onsubmit="
        const reason = prompt(
            'Please enter the reason for rejecting this company request:'
        );

        if (reason === null) {
            return false;
        }

        if (reason.trim().length < 3) {
            alert('Please enter a valid rejection reason.');
            return false;
        }

        this.querySelector(
            'input[name=review_note]'
        ).value = reason.trim();

        return confirm(
            'Reject this company request?\n\n'
            + 'The requester will be informed of the rejection.'
        );
    "
>
    @csrf

            <input
                type="hidden"
                name="review_note"
                value=""
            >

            <button
                type="submit"
                class="button button-secondary"
            >
                Reject
            </button>
        </form>


                    {{-- Approve company --}}
                    <form
                        method="POST"
                        action="{{ route(
                            'company-requests.approve',
                            $companyRequest['id']
                        ) }}"
                        style="display: inline;"
                        onsubmit="
                            return confirm(
                                'Approve this company request?\n\n'
                                + 'Company: {{ addslashes($companyRequest['company_name'] ?? '') }}\n\n'
                                + 'This will create the company and assign the requester as Company Admin.'
                            );
                        "
                    >

                        @csrf

                        <button
                            type="submit"
                            class="button button-primary"
                        >
                            Approve Company
                        </button>

                    </form>

                </div>

            </div>

        </section>

    @endif


    {{-- ========================================================= --}}
    {{-- ALREADY REVIEWED --}}
    {{-- ========================================================= --}}

    @if(
        in_array(
            $companyRequest['status'] ?? null,
            ['approved', 'rejected'],
            true
        )
    )

        <section class="card">

            <div class="card-heading">

                <div>

                    <h2>
                        Request Reviewed
                    </h2>

                    <p>

                        This request has already been

                        <strong>
                            {{ $status }}
                        </strong>.

                        No further review action is available.

                    </p>

                </div>

                <x-badge :value="$status" />

            </div>

        </section>

    @endif

@endsection