@props(['title' => 'No records found', 'description' => 'There are no records matching this view. Try adjusting your filters.', 'icon' => 'search'])
<div class="empty-state"><span class="empty-icon"><x-icon :name="$icon" /></span><h3>{{ $title }}</h3><p>{{ $description }}</p>{{ $slot }}</div>
