<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Menu Notifikasi (semua peran): tugas baru & nilai untuk siswa, pengumpulan untuk guru,
 * pendaftar guru untuk admin. Membuka notifikasi = tandai dibaca lalu menuju halamannya.
 */
class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('notifikasi.index', [
            'notifications' => $request->user()->notifications()->paginate(30),
        ]);
    }

    public function read(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->to($notification->data['url'] ?? route('notifikasi.index'));
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('status', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
