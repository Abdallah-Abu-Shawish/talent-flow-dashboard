@extends('layouts.app')

@section('title', $detail['title'])

@section('content')

@php

    $backUrl = match ($module ?? null) {

        'jobs' =>
            route('jobs.index'),

        'organizations' =>
            route('organizations.index'),

        'users' =>
            route('users.index'),

        'interviews' =>
            route('interviews.index'),

        'audit' =>
            route('audit.index'),

        default =>
            null,
    };


    $backLabel = match ($module ?? null) {

        'jobs' =>
            'Back to jobs',

        'organizations' =>
            'Back to organizations',

        'users' =>
            'Back to users',

        'interviews' =>
            'Back to interviews',

        'audit' =>
            'Back to audit logs',

        default =>
            'Back',
    };

@endphp


<x-page-heading
    :title="$detail['title']"
    :description="$detail['subtitle'] ?? null"
>

    {{-- =====================================================
         BACK BUTTON
    ====================================================== --}}

    @if($backUrl)

        <a
            class="button button-secondary"
            href="{{ $backUrl }}"
        >
            <x-icon name="back" />

            {{ $backLabel }}
        </a>

    @endif


    {{-- =====================================================
         BADGE
    ====================================================== --}}

    @if(!empty($detail['badge']))

        <x-badge
            :value="$detail['badge']"
        />

    @endif


    {{-- =====================================================
         EDIT BUTTON
    ====================================================== --}}

    @if(!empty($detail['editUrl']))

        <a
            class="button button-primary"
            href="{{ $detail['editUrl'] }}"
        >
            {{ $detail['editLabel'] ?? 'Edit' }}

            <x-icon name="arrow" />
        </a>

    @endif

</x-page-heading>


{{-- =========================================================
     RECORD DETAILS
========================================================= --}}

<section class="card">

    <div class="card-heading">

        <h2>
            Record details
        </h2>

        <span class="overline">
            PERSISTED RECORD
        </span>

    </div>


    <x-detail-fields
        :fields="$detail['fields'] ?? []"
    />


    @if(!empty($detail['body']))

        <div class="prose record-body">
            {{ $detail['body'] }}
        </div>

    @endif

</section>


{{-- =========================================================
     RELATED SECTIONS
========================================================= --}}

@foreach(
    $detail['sections'] ?? []
    as $section
)

    @if(isset($section['table']))

        <x-data-table
            :table="$section['table']"
        />

    @else

        <section class="card">

            <div class="card-heading">

                <h2>
                    {{ $section['title'] }}
                </h2>

            </div>


            @if(isset($section['fields']))

                <x-detail-fields
                    :fields="$section['fields']"
                />

            @endif


            @if(!empty($section['body']))

                <div class="prose record-body">
                    {{ $section['body'] }}
                </div>

            @endif

        </section>

    @endif

@endforeach


@endsection