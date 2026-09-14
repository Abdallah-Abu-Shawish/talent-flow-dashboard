@props(['fields' => [], 'values' => [], 'action' => null])
@if(count($fields))
    @php($filterId = 'filter-'.\Illuminate\Support\Str::random(8).'-')
    <form class="filter-form" action="{{ $action ?: url()->current() }}" method="GET" data-loading-form aria-label="Filter records">
        @foreach($fields as $name => $field)
            <div @class(['filter-field', 'filter-field-search' => ($field['type'] ?? 'text') === 'text'])>
                <label for="{{ $filterId }}{{ $name }}">{{ $field['label'] }}</label>
                @if(($field['type'] ?? 'text') === 'select')
                    <select id="{{ $filterId }}{{ $name }}" name="{{ $name }}">
                        @foreach($field['options'] ?? [] as $value => $label)<option value="{{ $value }}" @selected((string) ($values[$name] ?? '') === (string) $value)>{{ $label }}</option>@endforeach
                    </select>
                @else
                    <input id="{{ $filterId }}{{ $name }}" type="{{ ($field['type'] ?? 'text') === 'date' ? 'date' : 'text' }}" name="{{ $name }}" value="{{ $values[$name] ?? '' }}" @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif @if(($field['type'] ?? 'text') !== 'date') maxlength="200" @endif>
                @endif
            </div>
        @endforeach
        <div class="filter-actions"><button type="submit" class="button button-primary"><x-icon name="search" />Apply filters</button><a href="{{ $action ?: url()->current() }}" class="button button-quiet">Reset</a></div>
    </form>
@endif
