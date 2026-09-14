@extends('layouts.app')
@section('title', 'Platform overview')
@section('content')
    <x-page-heading title="Platform overview" description="Organizations, interview activity and recorded token consumption.">
        <a class="button button-secondary" href="{{ route('usage.index', $filters) }}">Explore usage<x-icon name="arrow" /></a>
    </x-page-heading>
    <x-date-filters :filters="$filters" :action="route('overview')" />
    <div class="metrics-grid">
        <x-metric label="Organizations" :value="$summary['organizations'] ?? null" icon="building" :url="route('organizations.index')" note="Platform total · all time" />
        <x-metric label="Users" :value="$summary['users'] ?? null" icon="users" :url="route('users.index')" note="Platform total · all time" />
        <x-metric label="Interviews" :value="$summary['interviews'] ?? null" icon="interview" :url="route('interviews.index')" note="Created in selected period" />
        <x-metric label="Tokens consumed" :value="$summary['tokens'] ?? null" icon="coins" :url="route('usage.index', $filters)" note="Persisted token debits" :accent="true" />
    </div>
    <div class="analytics-grid"><x-usage-chart :daily="$summary['daily'] ?? []" /><x-usage-breakdown :rows="$summary['by_service'] ?? []" title="Usage by service" label-key="service" description="Recorded token attribution" /></div>
    <div class="operational-grid">
        <section class="card activity-card"><div class="card-heading"><div><h2>Interview activity</h2><p>Persisted workflow and integrity records</p></div><x-icon name="interview" /></div><dl class="activity-stats"><div><dt>Completed <small>Created in period</small></dt><dd>{{ isset($summary['completed']) ? number_format($summary['completed']) : '—' }}</dd></div><div><dt>In progress <small>Current · all time</small></dt><dd>{{ isset($summary['in_progress']) ? number_format($summary['in_progress']) : '—' }}</dd></div><div><dt>Integrity events <small>Selected period</small></dt><dd>{{ isset($summary['integrity_events']) ? number_format($summary['integrity_events']) : '—' }}</dd></div></dl><div class="card-bottom"><span>Workflow status does not verify a live connection.</span><a href="{{ route('interviews.index') }}">View interviews<x-icon name="arrow" /></a></div></section>
        <section class="card telemetry-card"><span class="telemetry-icon"><x-icon name="activity" /></span><div><h2>Worker telemetry</h2><p>Worker heartbeats and queue metrics are not yet connected.</p><a href="{{ route('health') }}">View system health<x-icon name="arrow" /></a></div></section>
    </div>
    <x-data-table :table="$recentTable" />
@endsection
