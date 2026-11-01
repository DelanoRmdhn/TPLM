<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegistrationSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * M08e, FR-30, PB-31: buka/tutup pendaftaran mandiri.
 */
class RegistrationSettingController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $open = $request->boolean('is_open');

        RegistrationSetting::current()->update([
            'is_open' => $open,
            'updated_by' => $request->user()->id,
        ]);

        return back()->with('status', $open ? 'Pendaftaran akun dibuka.' : 'Pendaftaran akun ditutup.');
    }
}
