@if (session('success'))
    <div class="alert alert-success" role="status"><x-icon name="check" /><span>{{ session('success') }}</span></div>
@endif
@if (session('status'))
    <div class="alert alert-info" role="status"><x-icon name="info" /><span>{{ session('status') }}</span></div>
@endif
@if (session('error'))
    <div class="alert alert-error" role="alert"><x-icon name="info" /><span>{{ session('error') }}</span></div>
@endif
@if ($errors->any())
    <div class="alert alert-error" role="alert">
        <x-icon name="info" />
        <div><strong>Please review the following</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    </div>
@endif
