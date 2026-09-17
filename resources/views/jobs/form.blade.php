@extends('layouts.app')

@section(
    'title',
    $job
        ? 'Edit Job'
        : 'Create Job'
)

@section('content')

@php
    $isEditing =
        isset($job)
        && is_array($job);

    $selectedCategory = old(
        'category_id',
        $job['category_id']
        ?? ''
    );

    $isActive =
        (bool) old(
            'is_active',
            $job['is_active']
            ?? true
        );
@endphp


<x-page-heading
    :title="$isEditing
        ? 'Edit Job'
        : 'Create Job'"
    description="Create and manage jobs in the global TalentFlow job catalog."
>
    <a
        class="button button-secondary"
        href="{{ route('jobs.index') }}"
    >
        <x-icon name="back" />
        Back to jobs
    </a>
</x-page-heading>


<div class="form-layout">

    {{-- =====================================================
         FORM
    ====================================================== --}}

    <form
        method="POST"
        action="{{ $isEditing
            ? route(
                'jobs.update',
                $job['id']
            )
            : route('jobs.store')
        }}"
        class="card organization-form"
        data-loading-form
    >

        @csrf


        @if($isEditing)

            @method('PATCH')

            <input
                type="hidden"
                name="expected_updated_at"
                value="{{ old(
                    'expected_updated_at',
                    $job['updated_at']
                    ?? ''
                ) }}"
            >

        @endif


        {{-- GENERAL ERROR --}}
        @error('job')

            <div class="form-fields">

                <p class="field-error">
                    {{ $message }}
                </p>

            </div>

        @enderror


        {{-- =================================================
             HEADER
        ================================================== --}}

        <div class="card-heading">

            <div>

                <h2>
                    Job information
                </h2>

                <p>
                    Global job catalog
                </p>

            </div>

        </div>


        <div class="form-fields">


            {{-- =================================================
                 JOB TITLE
            ================================================== --}}

            <div class="form-field">

                <label for="title">
                    Job title
                </label>

                <input
                    id="title"
                    name="title"
                    type="text"
                    maxlength="200"

                    value="{{ old(
                        'title',
                        $job['title']
                        ?? ''
                    ) }}"

                    placeholder="e.g. Flutter Developer"

                    required
                    autocomplete="off"

                    @error('title')
                        aria-invalid="true"
                        aria-describedby="title-error"
                    @enderror
                >

                <p class="field-hint">
                    Enter the global job role name.
                </p>


                @error('title')

                    <p
                        id="title-error"
                        class="field-error"
                    >
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- =================================================
                 CATEGORY
            ================================================== --}}

            <div class="form-field">

                <label for="category_id">
                    Job category
                </label>

                <select
                    id="category_id"
                    name="category_id"
                    required

                    @error('category_id')
                        aria-invalid="true"
                        aria-describedby="category-error"
                    @enderror
                >

                    <option value="">
                        Select category
                    </option>


                    @foreach($categories as $category)

                        @php
                            $categoryIsActive =
                                (bool) (
                                    $category['is_active']
                                    ?? false
                                );

                            $isCurrentCategory =
                                $selectedCategory
                                ===
                                $category['id'];
                        @endphp


                        <option
                            value="{{ $category['id'] }}"

                            @selected(
                                $isCurrentCategory
                            )

                            @disabled(
                                ! $categoryIsActive
                                && ! $isCurrentCategory
                            )
                        >

                            {{ $category['name'] }}

                            @if(! $categoryIsActive)
                                — Disabled
                            @endif

                        </option>

                    @endforeach

                </select>


                <p class="field-hint">
                    Select the category this job belongs to.
                    Disabled categories cannot be assigned to new jobs.
                </p>


                @error('category_id')

                    <p
                        id="category-error"
                        class="field-error"
                    >
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- =================================================
                 DESCRIPTION
            ================================================== --}}

            <div class="form-field">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    maxlength="5000"

                    placeholder="Brief description of this job role..."

                    @error('description')
                        aria-invalid="true"
                        aria-describedby="description-error"
                    @enderror
                >{{ old(
                    'description',
                    $job['description']
                    ?? ''
                ) }}</textarea>


                <p class="field-hint">
                    Optional description of the global job role.
                    This is separate from the company-specific job
                    description used during an AI interview.
                </p>


                @error('description')

                    <p
                        id="description-error"
                        class="field-error"
                    >
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- =================================================
                 AVAILABILITY
            ================================================== --}}

            <div class="form-field">

                <span class="category-availability-label">
                    Job availability
                </span>


                <input
                    type="hidden"
                    name="is_active"
                    value="0"
                >


                <label class="category-availability">

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        @checked($isActive)
                    >


                    <span class="category-availability-copy">

                        <strong>
                            Enabled
                        </strong>

                        <small>
                            When disabled, this job remains stored
                            in the catalog but will not be available
                            for new selections.
                        </small>

                    </span>

                </label>


                @error('is_active')

                    <p class="field-error">
                        {{ $message }}
                    </p>

                @enderror

            </div>

        </div>


        {{-- =====================================================
             FORM ACTIONS
        ====================================================== --}}

        <div class="form-footer">

            <a
                class="button button-quiet"
                href="{{ route('jobs.index') }}"
            >
                Cancel
            </a>


            <button
                type="submit"
                class="button button-primary"
            >

                {{
                    $isEditing
                        ? 'Save changes'
                        : 'Create job'
                }}

                <x-icon name="arrow" />

            </button>

        </div>

    </form>


    {{-- =====================================================
         RIGHT SIDE INFORMATION
    ====================================================== --}}

    <aside class="form-aside">

        <span class="aside-icon">
            <x-icon name="briefcase" />
        </span>


        <h2>
            Global job catalog
        </h2>

        <p>
            Jobs created here are global roles managed by
            TalentFlow and are not owned by a specific company.
        </p>


        <hr>


        <h3>
            Automatic slug
        </h3>

        <p>
            The internal job slug is generated automatically
            from the job title. You do not need to enter it.
        </p>


        <h3>
            Job category
        </h3>

        <p>
            Each job belongs to a category managed from
            the Job Categories section.
        </p>


        <h3>
            Availability
        </h3>

        <p>
            Jobs can be enabled or disabled individually.
            If their category is disabled, they are also
            unavailable for new selections.
        </p>


        <h3>
            Interview job description
        </h3>

        <p>
            Company-specific job descriptions remain separate
            and are stored in job postings for use by the
            AI interview system.
        </p>


        <a href="{{ route('jobs.index') }}">

            View all jobs

            <x-icon name="arrow" />

        </a>

    </aside>

</div>

@endsection