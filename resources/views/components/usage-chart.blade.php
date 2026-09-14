@props(['daily' => []])
<section class="card chart-card">
    <div class="card-heading"><div><h2>Token consumption</h2><p>Recorded token debits by day · UTC</p></div><span class="chart-legend"><span></span>Tokens</span></div>
    @if(count($daily))
        @php
            $maximum = max(1, (float) collect($daily)->max('tokens'));
            $width = 700;
            $height = 160;
            $points = collect($daily)->values()->map(function ($point, $index) use ($daily, $maximum, $width, $height) {
                $x = 56 + (count($daily) === 1 ? $width / 2 : $index * $width / (count($daily) - 1));
                $y = 20 + $height - max(0, (float) $point['tokens']) / $maximum * $height;
                return ['x' => round($x, 2), 'y' => round($y, 2), 'tokens' => $point['tokens'], 'day' => $point['day']];
            });
            $line = $points->map(fn ($point) => $point['x'].','.$point['y'])->implode(' ');
        @endphp
        <div class="chart-wrapper">
            <svg class="usage-chart" viewBox="0 0 780 220" role="img" aria-label="Daily recorded token consumption. Exact values available in the chart data below.">
                @foreach([0, 0.5, 1] as $fraction)
                    <line x1="56" y1="{{ 20 + 160 * $fraction }}" x2="756" y2="{{ 20 + 160 * $fraction }}" class="chart-grid" />
                    <text x="46" y="{{ 24 + 160 * $fraction }}" text-anchor="end" class="chart-axis">{{ IlluminateSupportNumber::abbreviate($maximum * (1 - $fraction), 1) }}</text>
                @endforeach
                @if(count($daily) > 1)<polygon points="{{ $points->first()['x'] }},180 {{ $line }} {{ $points->last()['x'] }},180" class="chart-area" />@endif
                <polyline points="{{ $line }}" class="chart-line" />
                @foreach($points as $point)<circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="3" class="chart-point"><title>{{ $point['day'] }}: {{ number_format((float) $point['tokens']) }} tokens</title></circle>@endforeach
                <text x="56" y="210" class="chart-axis">{{ $points->first()['day'] }}</text>
                @if(count($daily) > 1)<text x="756" y="210" text-anchor="end" class="chart-axis">{{ $points->last()['day'] }}</text>@endif
            </svg>
        </div>
        <details class="chart-data"><summary>View daily values<span class="muted">{{ count($daily) }} days</span></summary><div class="table-scroll" tabindex="0" role="region" aria-label="Daily token values"><table><thead><tr><th scope="col">Day (UTC)</th><th scope="col" class="numeric">Tokens debited</th></tr></thead><tbody>@foreach($daily as $point)<tr><td>{{ $point['day'] }}</td><td class="numeric">{{ number_format((float) $point['tokens']) }}</td></tr>@endforeach</tbody></table></div></details>
    @else<x-empty-state title="No usage recorded" description="No token consumption was recorded in this date range." icon="activity" />@endif
</section>
