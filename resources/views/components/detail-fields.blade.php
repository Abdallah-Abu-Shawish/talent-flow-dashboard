@props(['fields'])
<dl class="detail-fields">
    @foreach($fields as $label => $value)
        <div><dt>{{ $label }}</dt><dd>@if($value === null || $value === '')<span class="muted">Not recorded</span>@elseif(is_bool($value)){{ $value ? 'Yes' : 'No' }}@elseif(is_scalar($value)){{ $value }}@else<span class="muted">Not available</span>@endif</dd></div>
    @endforeach
</dl>
