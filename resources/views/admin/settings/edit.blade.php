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
    <button class="primary" type="submit">Save Settings</button>
</form>
@endsection
