@extends('layouts.app')
@section('title', $organization ? 'Edit organization' : 'Create organization')
@section('content')
    <x-page-heading :title="$organization ? 'Edit organization' : 'Create organization'" description="Manage the organization identity and recorded subscription plan.">
        <a class="button button-secondary" href="{{ $organization ? route('organizations.show', $organization['id']) : route('organizations.index') }}"><x-icon name="back" />Back to {{ $organization ? 'organization' : 'organizations' }}</a>
    </x-page-heading>
    <div class="form-layout">
        <form method="POST" action="{{ $organization ? route('organizations.update', $organization['id']) : route('organizations.store') }}" class="card organization-form" data-confirm-form data-loading-form>
            @csrf
            @if($organization)@method('PATCH')@endif
            <input type="hidden" name="request_id" value="{{ old('request_id', $requestId) }}">
            @if($organization)<input type="hidden" name="expected_updated_at" value="{{ old('expected_updated_at', $organization['updated_at']) }}">@endif
            <div class="card-heading"><h2>Organization information</h2><span class="muted">All fields required</span></div>
            <div class="form-fields">
                <div class="form-field"><label for="name">Organization name</label><input id="name" name="name" type="text" maxlength="160" value="{{ old('name', $organization['name'] ?? '') }}" required autocomplete="organization" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>@error('name')<p id="name-error" class="field-error">{{ $message }}</p>@enderror</div>
                <div class="form-field"><label for="slug">Organization slug</label><input id="slug" class="monospace" name="slug" type="text" maxlength="80" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" value="{{ old('slug', $organization['slug'] ?? '') }}" required spellcheck="false" aria-describedby="slug-hint @error('slug') slug-error @enderror" @error('slug') aria-invalid="true" @enderror><p id="slug-hint" class="field-hint">A unique identifier using lowercase letters, numbers and single hyphens.</p>@error('slug')<p id="slug-error" class="field-error">{{ $message }}</p>@enderror</div>
                <div class="form-field"><label for="plan">Subscription plan</label><select name="plan" id="plan" required @error('plan') aria-invalid="true" aria-describedby="plan-error" @enderror>@foreach(['free' => 'Free', 'starter' => 'Starter', 'pro' => 'Pro', 'enterprise' => 'Enterprise'] as $value => $label)<option value="{{ $value }}" @selected(old('plan', $organization['plan'] ?? 'free') === $value)>{{ $label }}</option>@endforeach</select>@error('plan')<p id="plan-error" class="field-error">{{ $message }}</p>@enderror</div>
                <div class="form-field"><label for="reason">Reason for this change</label><textarea id="reason" name="reason" rows="3" minlength="10" maxlength="500" required aria-describedby="reason-hint @error('reason') reason-error @enderror" @error('reason') aria-invalid="true" @enderror>{{ old('reason') }}</textarea><p id="reason-hint" class="field-hint">10–500 characters. This reason is stored in the audit history.</p>@error('reason')<p id="reason-error" class="field-error">{{ $message }}</p>@enderror</div>
                <label class="confirmation-checkbox"><input type="checkbox" name="confirmed" value="1" required @checked(old('confirmed'))><span>I have reviewed these details and confirm this organization change.</span></label>
            </div>
            <div class="form-footer"><a class="button button-quiet" href="{{ $organization ? route('organizations.show', $organization['id']) : route('organizations.index') }}">Cancel</a><button type="submit" class="button button-primary">{{ $organization ? 'Save changes' : 'Create organization' }}<x-icon name="arrow" /></button></div>
        </form>
        <aside class="form-aside"><span class="aside-icon"><x-icon name="shield" /></span><h2>An accountable change</h2><p>Your administrator identity, reason and the result of this operation are recorded in the command history.</p><hr><h3>Subscription plan</h3><p>This updates the plan recorded for the organization. Token balances and subscription payments are managed separately.</p>@if($organization)<h3>Concurrent edits</h3><p>If another administrator changes this record while you are editing, reload the latest organization before retrying.</p>@endif<a href="{{ route('audit.index') }}">View audit history<x-icon name="arrow" /></a></aside>
    </div>
@endsection
