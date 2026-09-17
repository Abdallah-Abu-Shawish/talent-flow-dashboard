@props(['value'])

@php
    $label = is_scalar($value)
        ? (string) $value
        : '';

    $tone = match (strtolower($label)) {

        'completed',
        'success',
        'succeeded',
        'allowed',
        'configured',
        'published',
        'active',
        'approved',
        'enabled'
            => 'success',

        'failed',
        'failure',
        'denied',
        'error',
        'rejected',
        'critical',
        'high',
        'disabled'
            => 'danger',

        'in_progress',
        'pending',
        'warning',
        'medium',
        'scheduled',
        'not configured'
            => 'warning',

        'enterprise',
        'pro',
        'super_admin'
            => 'accent',

        default
            => 'neutral',
    };
@endphp

<span class="badge badge-{{ $tone }}">
    {{
        $label === ''
            ? '—'
            : str_replace('_', ' ', $label)
    }}
</span>