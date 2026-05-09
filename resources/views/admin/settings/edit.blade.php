@extends('layouts.admin')

@section('content')
<h1>Settings</h1>
<form method="post" action="{{ route('admin.settings.update') }}">
    @csrf @method('PATCH')
    <div class="form-grid">
        @foreach (['store_name'=>'Store name','store_phone'=>'Store phone','whatsapp_number'=>'WhatsApp number','admin_email'=>'Admin email','site_url'=>'Site URL','currency'=>'Currency'] as $key => $label)
            <div class="field"><label>{{ $label }}</label><input name="{{ $key }}" value="{{ old($key, $settings[$key] ?? '') }}" required></div>
        @endforeach
    </div>
    <h2>Admin Password</h2>
    <p class="muted">Leave blank to keep the current password.</p>
    <div class="form-grid">
        <div class="field"><label>New password</label><input name="new_admin_password" type="password" autocomplete="new-password"></div>
        <div class="field"><label>Confirm new password</label><input name="new_admin_password_confirmation" type="password" autocomplete="new-password"></div>
    </div>
    <button class="primary" type="submit">Save Settings</button>
</form>
@endsection
