    @extends('layouts.app')
    @section('title', $detail['title'])
    @section('content')
        <x-page-heading :title="$detail['title']" :description="$detail['subtitle'] ?? null">
            @if(!empty($detail['badge']))<x-badge :value="$detail['badge']" />@endif
            @if(!empty($detail['editUrl']))
    <a
        class="button button-primary"
        href="{{ $detail['editUrl'] }}"
    >
        Edit
        <x-icon name="arrow" />
    </a>
@endif
        </x-page-heading>
        <section class="card"><div class="card-heading"><h2>Record details</h2><span class="overline">PERSISTED RECORD</span></div><x-detail-fields :fields="$detail['fields'] ?? []" />@if(!empty($detail['body']))<div class="prose record-body">{{ $detail['body'] }}</div>@endif</section>
        @foreach($detail['sections'] ?? [] as $section)
            @if(isset($section['table']))<x-data-table :table="$section['table']" />
            @else<section class="card"><div class="card-heading"><h2>{{ $section['title'] }}</h2></div>@if(isset($section['fields']))<x-detail-fields :fields="$section['fields']" />@endif @if(!empty($section['body']))<div class="prose record-body">{{ $section['body'] }}</div>@endif</section>@endif
        @endforeach
    @endsection
