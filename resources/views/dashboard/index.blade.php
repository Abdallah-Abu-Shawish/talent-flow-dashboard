@extends('layouts.app')

@section('title', $table['title'])

@section('content')

    <x-page-heading
        :title="$table['title']"
        :description="$table['description'] ?? null"
    >

        {{-- Jobs: Create Job --}}
        @if(!empty($primaryAction))
            <a
                class="button button-primary"
                href="{{ $primaryAction['url'] }}"
            >
                <x-icon name="plus" />

                {{ $primaryAction['label'] }}
            </a>

        {{-- Organizations: existing create action --}}
        @elseif(!empty($table['createUrl']))
            <a
                class="button button-primary"
                href="{{ $table['createUrl'] }}"
            >
                <x-icon name="plus" />

                Create organization
            </a>
        @endif

    </x-page-heading>


    @if(!empty($table['tabs']))

        <nav
            class="tabs"
            aria-label="{{ $table['title'] }} sections"
        >

            @foreach($table['tabs'] as $tab)

                <a
                    href="{{ $tab['url'] }}"
                    @class([
                        'tab',
                        'active' => $tab['active'],
                    ])
                    @if($tab['active'])
                        aria-current="page"
                    @endif
                >
                    {{ $tab['label'] }}
                </a>

            @endforeach

        </nav>

    @endif


    <x-data-table
        :table="$table"
        :heading="false"
    />

@endsection