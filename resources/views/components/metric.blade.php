@props(['label', 'value' => null, 'icon' => 'grid', 'note' => null, 'url' => null, 'accent' => false])
<article @class(['card metric-card', 'metric-accent' => $accent])>
    <div class="metric-top"><span class="metric-icon"><x-icon :name="$icon" /></span>@if($url)<a href="{{ $url }}" class="metric-link" aria-label="View {{ strtolower($label) }}"><x-icon name="arrow" /></a>@endif</div>
    <div class="metric-value">{{ is_numeric($value) ? number_format((float) $value) : '—' }}</div>
    <h2>{{ $label }}</h2>@if($note)<p>{{ $note }}</p>@endif
</article>
