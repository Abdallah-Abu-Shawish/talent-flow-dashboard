@extends('layouts.app')
@section('title', 'Usage & tokens')
@section('content')
    <x-page-heading title="Usage & tokens" description="Trace token consumption to the organization and service that recorded it." />
    <x-date-filters :filters="$filters" :action="route('usage.index')" :extended="true" />
    <div class="usage-summary card"><span class="metric-icon"><x-icon name="coins" /></span><div><p>Tokens consumed in selected period</p><strong class="usage-total">{{ isset($summary['tokens']) ? number_format((float) $summary['tokens']) : '—' }}</strong></div><p class="usage-explanation">Totals reflect persisted token debits. Provider prices, request counts and monetary costs are not recorded by this ledger.</p></div>
    <x-usage-chart :daily="$summary['daily'] ?? []" />
    <div class="equal-grid"><x-usage-breakdown :rows="$summary['by_company'] ?? []" title="Top organizations" label-key="name" description="By recorded token consumption" :organizations="true" /><x-usage-breakdown :rows="$summary['by_service'] ?? []" title="Usage by service" label-key="service" description="Consumption by recorded feature" /></div>
    <x-data-table :table="$table" />
@endsection
