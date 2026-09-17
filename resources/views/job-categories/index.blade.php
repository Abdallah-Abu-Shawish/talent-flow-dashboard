@extends('layouts.app')

@section('title', 'Job Categories')

@section('content')

<x-page-heading
    title="Job Categories"
    description="Manage the categories used to organize jobs across the TalentFlow platform."
>
    <a
        class="button button-primary"
        href="{{ route('job-categories.create') }}"
    >
        <x-icon name="plus" />
        Add Category
    </a>
</x-page-heading>


<div class="card">

    <div class="card-heading">

        <div>
            <h2>Categories</h2>

            <p>
                Global categories available when creating jobs.
            </p>
        </div>

        <span class="record-count">
            {{ count($categories) }}
            {{ count($categories) === 1 ? 'category' : 'categories' }}
        </span>

    </div>


    @if(empty($categories))

        <div class="empty-state">

            <span class="empty-icon">
                <x-icon name="briefcase" />
            </span>

            <h3>No job categories yet</h3>

            <p>
                Create your first category to start organizing
                the global job catalog.
            </p>

            <a
                class="button button-primary"
                href="{{ route('job-categories.create') }}"
            >
                <x-icon name="plus" />
                Add Category
            </a>

        </div>

    @else

        <div class="table-scroll">

            <table>

                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Availability</th>
                        <th>Updated</th>
                        <th class="row-action"></th>
                    </tr>
                </thead>


                <tbody>

                    @foreach($categories as $category)

                        @php
                            $isActive =
                                (bool) (
                                    $category['is_active']
                                    ?? false
                                );
                        @endphp

                        <tr>

                            {{-- CATEGORY NAME --}}
                            <td>

                                <a
                                    class="cell-link"
                                    href="{{ route(
                                        'job-categories.edit',
                                        $category['id']
                                    ) }}"
                                >
                                    {{ $category['name'] }}
                                </a>

                            </td>


                            {{-- DESCRIPTION --}}
                            <td>

                                @if(!empty($category['description']))

                                    {{ \Illuminate\Support\Str::limit(
                                        $category['description'],
                                        90
                                    ) }}

                                @else

                                    <span class="muted">
                                        No description
                                    </span>

                                @endif

                            </td>


                            {{-- STATUS --}}
                            <td>

                                @if($isActive)

                                    <span class="badge badge-success">
                                        Enabled
                                    </span>

                                @else

                                    <span class="badge badge-neutral">
                                        Disabled
                                    </span>

                                @endif

                            </td>


                            {{-- UPDATED --}}
                            <td class="muted">

                                @if(!empty($category['updated_at']))

                                    {{
                                        \Illuminate\Support\Carbon::parse(
                                            $category['updated_at']
                                        )->format('M d, Y')
                                    }}

                                @else
                                    —
                                @endif

                            </td>


                            {{-- EDIT --}}
                            <td class="row-action">

                                <a
                                    class="detail-link"
                                    href="{{ route(
                                        'job-categories.edit',
                                        $category['id']
                                    ) }}"
                                >
                                    Edit

                                    <x-icon name="arrow" />
                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @endif

</div>

@endsection