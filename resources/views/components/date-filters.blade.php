@props(['filters' => [], 'action', 'extended' => false])
@php
    $fields = ['from' => ['label' => 'From (UTC)', 'type' => 'date'], 'to' => ['label' => 'To (UTC)', 'type' => 'date']];
    if ($extended) {
        $fields['company_id'] = ['label' => 'Organization ID', 'type' => 'text', 'placeholder' => 'All organizations'];
        $fields['service'] = ['label' => 'Service', 'type' => 'text', 'placeholder' => 'All services'];
    }
@endphp
<div class="card date-filter-card"><x-filters :fields="$fields" :values="$filters" :action="$action" /></div>
