@props(['table', 'heading' => true, 'filters' => true])
<section class="card table-card" aria-label="{{ $table['title'] }}">
    @if($heading)
        <div class="card-heading"><div><h2>{{ $table['title'] }}</h2>@if(!empty($table['description']))<p>{{ $table['description'] }}</p>@endif</div>@if(isset($table['paginator']))<span class="record-count">{{ number_format($table['paginator']->total()) }} records</span>@endif</div>
    @endif
    @if($filters)<x-filters :fields="$table['filterFields'] ?? []" :values="$table['filters'] ?? []" />@endif
    @if(!empty($table['note']))<div class="table-note"><x-icon name="info" /><span>{{ $table['note'] }}</span></div>@endif
    @if(count($table['rows'] ?? []))
        @php($hasDetail = collect($table['rows'])->contains(fn ($row) => !empty($row['_url'])))
        <div class="table-scroll" role="region" aria-label="{{ $table['title'] }} records" tabindex="0">
            <table>
                <caption class="sr-only">{{ $table['title'] }}</caption>
                <thead><tr>@foreach($table['columns'] as $column)<th scope="col" @class(['numeric' => ($column['type'] ?? '') === 'number'])>{{ $column['label'] }}</th>@endforeach @if($hasDetail)<th scope="col"><span class="sr-only">Details</span></th>@endif</tr></thead>
                <tbody>
                    @foreach($table['rows'] as $row)
                        <tr>
                            @foreach($table['columns'] as $column)
                                @php($cell = $row[$column['key']] ?? null)
                                <td @class(['numeric' => ($column['type'] ?? '') === 'number', 'monospace' => ($column['type'] ?? '') === 'mono'])>
                                    @if(!empty($row['_links'][$column['key']]))<a class="cell-link" href="{{ $row['_links'][$column['key']] }}">@endif
                                    @if(($column['type'] ?? '') === 'badge')<x-badge :value="$cell" />
                                    @elseif($cell === null || $cell === '')<span class="muted">—</span>
                                    @elseif(is_bool($cell)){{ $cell ? 'Yes' : 'No' }}
                                    @elseif(($column['type'] ?? '') === 'number' && is_numeric($cell)){{ number_format((float) $cell) }}
                                    @elseif(is_scalar($cell)){{ $cell }}
                                    @else<span class="muted">—</span>@endif
                                    @if(!empty($row['_links'][$column['key']]))</a>@endif
                                </td>
                            @endforeach
                            @if($hasDetail)<td class="row-action">@if(!empty($row['_url']))<a class="detail-link" href="{{ $row['_url'] }}">View<x-icon name="arrow" /><span class="sr-only"> record {{ $row[$table['columns'][0]['key']] ?? '' }}</span></a>@endif</td>@endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else<x-empty-state />@endif
    @if(isset($table['paginator']))<x-pagination :paginator="$table['paginator']" />@endif
</section>
