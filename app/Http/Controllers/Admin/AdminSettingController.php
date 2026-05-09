<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', ['settings' => Setting::query()->pluck('value', 'key')]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:160'],
            'store_phone' => ['required', 'string', 'max:40'],
            'whatsapp_number' => ['required', 'string', 'max:40'],
            'admin_email' => ['required', 'email', 'max:160'],
            'site_url' => ['required', 'url', 'max:200'],
            'currency' => ['required', 'string', 'max:10'],
        ]);

        foreach ($data as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('status', 'Settings updated.');
    }
}
