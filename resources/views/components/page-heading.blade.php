@props(['title', 'description' => null])
<div class="page-heading">
    <div><h1>{{ $title }}</h1>@if($description)<p>{{ $description }}</p>@endif</div>
    @if($slot->isNotEmpty())<div class="page-actions">{{ $slot }}</div>@endif
</div>
