@extends('layouts.app')
@section('title', $table['title'])
@section('content')
    <x-page-heading :title="$table['title']" :description="$table['description'] ?? null">
        @if(!empty($table['createUrl']))<a class="button button-primary" href="{{ $table['createUrl'] }}"><x-icon name="plus" />Create organization</a>@endif
    </x-page-heading>
    @if(!empty($table['tabs']))
        <nav class="tabs" aria-label="{{ $table['title'] }} sections">@foreach($table['tabs'] as $tab)<a href="{{ $tab['url'] }}" @class(['tab', 'active' => $tab['active']]) @if($tab['active']) aria-current="page" @endif>{{ $tab['label'] }}</a>@endforeach</nav>
    @endif
    <x-data-table :table="$table" :heading="false" />
@endsection
