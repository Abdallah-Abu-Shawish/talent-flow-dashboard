@extends('layouts.app')

@section(
    'title',
    $category
        ? 'Edit Job Category'
        : 'Add Job Category'
)

@section('content')

@php
    $isEditing =
        isset($category)
        && is_array($category);

    $isActive =
        (bool) old(
            'is_active',
            $category['is_active']
            ?? true
        );
@endphp


<x-page-heading
    :title="$isEditing
        ? 'Edit Job Category'
        : 'Add Job Category'"
    description="Create and manage categories used by the global job catalog."
>
    <a
        class="button button-secondary"
        href="{{ route('job-categories.index') }}"
    >
        <x-icon name="back" />
        Back to categories
    </a>
</x-page-heading>


<div class="form-layout">

    <form
        method="POST"
        action="{{ $isEditing
            ? route(
                'job-categories.update',
                $category['id']
            )
            : route('job-categories.store')
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
                    $category['updated_at']
                    ?? ''
                ) }}"
            >

        @endif


        {{-- GENERAL ERROR --}}
        @error('category')

            <div class="form-fields">

                <p class="field-error">
                    {{ $message }}
                </p>

            </div>

        @enderror


        <div class="card-heading">

            <div>

                <h2>
                    Category information
                </h2>

                <p>
                    Global job classification
                </p>

            </div>

        </div>


        <div class="form-fields">


            {{-- NAME --}}
            <div class="form-field">

                <label for="name">
                    Category name
                </label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    maxlength="160"
                    value="{{ old(
                        'name',
                        $category['name']
                        ?? ''
                    ) }}"
                    placeholder="e.g. Software Development"
                    required
                    autocomplete="off"

                    @error('name')
                        aria-invalid="true"
                        aria-describedby="name-error"
                    @enderror
                >

                <p class="field-hint">
                    Example: Software Development, Design,
                    Marketing or Healthcare.
                </p>

                @error('name')

                    <p
                        id="name-error"
                        class="field-error"
                    >
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- DESCRIPTION --}}
            <div class="form-field">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    maxlength="3000"
                    placeholder="Brief description of this category..."

                    @error('description')
                        aria-invalid="true"
                        aria-describedby="description-error"
                    @enderror
                >{{ old(
                    'description',
                    $category['description']
                    ?? ''
                ) }}</textarea>

                <p class="field-hint">
                    Optional explanation of the types of jobs
                    that belong to this category.
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


            {{-- AVAILABILITY --}}
            <div class="form-field">

                <span class="category-availability-label">
                    Category availability
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
                            When disabled, jobs under this category
                            will not be available for new selections.
                            Existing data remains stored.
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


        <div class="form-footer">

            <a
                class="button button-quiet"
                href="{{ route('job-categories.index') }}"
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
                        : 'Create category'
                }}

                <x-icon name="arrow" />

            </button>

        </div>

    </form>


    {{-- SIDE INFO --}}
    <aside class="form-aside">

        <span class="aside-icon">
            <x-icon name="briefcase" />
        </span>


        <h2>
            Job categories
        </h2>

        <p>
            Categories organize the global TalentFlow job catalog
            into logical groups.
        </p>


        <hr>


        <h3>
            Automatic slug
        </h3>

        <p>
            You only enter the category name.
            The internal slug is generated automatically
            by the system.
        </p>


        <h3>
            Disabling a category
        </h3>

        <p>
            Disabling a category does not delete its jobs.
            It simply makes the category unavailable
            for new selections.
        </p>


        <h3>
            Individual jobs
        </h3>

        <p>
            Each job can also be enabled or disabled
            independently from its category.
        </p>


        <a href="{{ route('job-categories.index') }}">
            View all categories
            <x-icon name="arrow" />
        </a>

    </aside>

</div>

@endsection