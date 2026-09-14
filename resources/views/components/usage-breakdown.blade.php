@props(['rows' => [], 'title', 'labelKey', 'description' => null, 'organizations' => false])
<section class="card breakdown-card">
    <div class="card-heading"><div><h2>{{ $title }}</h2>@if($description)<p>{{ $description }}</p>@endif</div></div>
    @if(count($rows))
        @php($max = max(1, (float) collect($rows)->max('tokens')))
        <ol class="breakdown-list">
            @foreach($rows as $row)
                <li><div class="breakdown-label">@if($organizations && !empty($row['id']))<a href="{{ route('organizations.show', $row['id']) }}">{{ $row[$labelKey] ?: 'Unnamed organization' }}</a>@else<span>{{ str_replace('_', ' ', $row[$labelKey] ?: 'Not recorded') }}</span>@endif<strong>{{ number_format((float) $row['tokens']) }} <small>tokens</small></strong></div><meter min="0" max="{{ $max }}" value="{{ max(0, (float) $row['tokens']) }}" aria-label="{{ $row[$labelKey] ?: 'Not recorded' }} token usage">{{ $row['tokens'] }}</meter></li>
            @endforeach
        </ol>
    @else<x-empty-state title="No consumption yet" description="Recorded usage will appear here for the selected period." icon="coins" />@endif
</section>
