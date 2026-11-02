<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\RegistrationSetting;
use App\Models\Rombel;
use App\Models\User;
use App\Support\TemporaryPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * M08: kelola akun guru & siswa (FR-03, FR-04, PB-03, PB-04).
 */
class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with('rombel')
            ->whereIn('role', [Role::Guru->value, Role::Siswa->value])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->q.'%';
                $query->where(fn ($w) => $w->where('name', 'like', $q)->orWhere('username', 'like', $q));
            })
            ->when(in_array($request->peran, ['guru', 'siswa'], true), fn ($query) => $query->where('role', $request->peran))
            ->when($request->filled('rombel'), fn ($query) => $query->where('rombel_id', $request->rombel))
            ->when($request->status === 'nonaktif', fn ($query) => $query->where('is_active', false))
            ->when($request->status === 'aktif', fn ($query) => $query->where('is_active', true))
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.akun.index', [
            'users' => $users,
            'rombels' => Rombel::orderBy('name')->get(),
            'registrationOpen' => RegistrationSetting::isOpen(),
        ]);
    }

    public function create(): View
    {
        return view('admin.akun.form', [
            'user' => new User(['role' => Role::Siswa]),
            'rombels' => Rombel::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $password = TemporaryPassword::generate();

        $user = User::create([
            ...$data,
            'is_active' => true,
            'must_change_password' => true,
            'password' => $password,
        ]);

        // M08c: tampilkan kata sandi awal sekali saja
        return redirect()->route('admin.akun.index')->with('temporary_password', [
            'title' => 'Akun berhasil dibuat',
            'name' => $user->name,
            'username' => $user->username,
            'password' => $password,
        ]);
    }

    public function edit(User $user): View
    {
        $this->ensureManageable($user);

        return view('admin.akun.form', [
            'user' => $user,
            'rombels' => Rombel::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureManageable($user);

        $user->update($this->validated($request, $user));

        return redirect()->route('admin.akun.index')->with('status', "Akun {$user->name} berhasil diubah.");
    }

    /** M08d: nonaktifkan. Data lama tetap tersimpan (PB-03). */
    public function deactivate(User $user): RedirectResponse
    {
        $this->ensureManageable($user);

        $user->update(['is_active' => false]);

        // Keluarkan dari semua perangkat yang sedang login
        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
        }

        return back()->with('status', "Akun {$user->name} dinonaktifkan.");
    }

    public function activate(User $user): RedirectResponse
    {
        $this->ensureManageable($user);

        $user->update(['is_active' => true]);

        return back()->with('status', "Akun {$user->name} diaktifkan lagi.");
    }

    /** PB-04: reset kata sandi, pengguna wajib menggantinya saat login berikutnya. */
    public function resetPassword(User $user): RedirectResponse
    {
        $this->ensureManageable($user);

        $password = TemporaryPassword::generate();

        $user->update([
            'password' => $password,
            'must_change_password' => true,
        ]);

        return back()->with('temporary_password', [
            'title' => 'Kata sandi berhasil direset',
            'name' => $user->name,
            'username' => $user->username,
            'password' => $password,
        ]);
    }

    /** Admin hanya mengelola akun guru dan siswa. */
    private function ensureManageable(User $user): void
    {
        abort_if($user->isAdmin(), 404);
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?User $user = null): array
    {
        $isSiswa = $request->input('role') === Role::Siswa->value;

        $data = $request->validate([
            'role' => ['required', Rule::in([Role::Guru->value, Role::Siswa->value])],
            'name' => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'string',
                $isSiswa ? 'regex:/^\d{10}$/' : 'regex:/^[A-Za-z0-9._-]{3,50}$/',
                Rule::unique('users', 'username')->ignore($user?->id),
            ],
            'rombel' => [$isSiswa ? 'required' : 'nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => $isSiswa ? 'NISN wajib diisi.' : 'NIP atau username wajib diisi.',
            'username.regex' => $isSiswa
                ? 'NISN harus 10 digit angka.'
                : 'Username hanya boleh huruf, angka, titik, strip, atau garis bawah (3–50 karakter).',
            'username.unique' => $isSiswa ? 'NISN ini sudah terdaftar.' : 'NIP/username ini sudah terdaftar.',
            'rombel.required' => 'Rombel wajib diisi untuk siswa.',
        ]);

        $rombel = $isSiswa ? Rombel::findByName($data['rombel']) : null;
        if ($isSiswa && $rombel === null) {
            throw ValidationException::withMessages(['rombel' => 'Rombel tidak ada di daftar. Tambahkan dulu di menu Rombel.']);
        }

        return [
            'role' => $data['role'],
            'name' => $data['name'],
            'username' => $data['username'],
            'rombel_id' => $rombel?->id,
        ];
    }
}
