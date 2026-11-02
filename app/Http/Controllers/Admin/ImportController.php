<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AccountImporter;
use App\Support\SimpleXlsx;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

/**
 * M08, M08b: impor akun dari Excel (FR-05, PB-02): unggah → hasil cek → Simpan → unduh kata sandi awal.
 */
class ImportController extends Controller
{
    private const XLSX = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    public function index(): View
    {
        return view('admin.impor.index');
    }

    /** Unduh template Excel kosong (hanya judul kolom). */
    public function template(string $type): Response
    {
        abort_unless(isset(AccountImporter::HEADERS[$type]), 404);

        return response(SimpleXlsx::write([AccountImporter::HEADERS[$type]], 'Data '.ucfirst($type)), 200, [
            'Content-Type' => self::XLSX,
            'Content-Disposition' => "attachment; filename=\"template-impor-{$type}.xlsx\"",
        ]);
    }

    /** Langkah 1: unggah file lalu cek isinya. Belum ada akun yang dibuat. */
    public function store(Request $request, AccountImporter $importer): RedirectResponse
    {
        $request->validate([
            'type' => ['required', Rule::in(array_keys(AccountImporter::HEADERS))],
            'file' => ['required', 'file', 'extensions:xlsx', 'max:5120'],
        ], [
            'type.required' => 'Pilih jenis data: siswa atau guru.',
            'file.required' => 'Pilih file Excel dulu.',
            'file.extensions' => 'File tidak sesuai template. Unduh template lalu coba lagi.',
            'file.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        try {
            $check = $importer->check($request->file('file')->getRealPath(), $request->type);
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['file' => $e->getMessage()]);
        }

        $request->session()->put('import_check', ['type' => $request->type, ...$check]);
        $request->session()->forget('import_result');

        return redirect()->route('admin.impor.cek');
    }

    /** Hasil cek: jumlah baris siap dan baris bermasalah, sebelum admin menekan Simpan. */
    public function preview(Request $request): View|RedirectResponse
    {
        $check = $request->session()->get('import_check');

        if (! $check) {
            return redirect()->route('admin.impor.index');
        }

        return view('admin.impor.cek', ['check' => $check]);
    }

    /** Langkah 2: buat akun untuk baris yang lolos cek. */
    public function save(Request $request, AccountImporter $importer): RedirectResponse
    {
        // Diambil sekaligus dihapus dari sesi, supaya Simpan yang ditekan dua kali tidak membuat akun dobel
        $check = $request->session()->pull('import_check');

        if (! $check || $check['valid'] === []) {
            return redirect()->route('admin.impor.index');
        }

        $result = $importer->create($check['valid'], $check['type']);
        $problems = collect([...$check['problems'], ...$result['problems']])->sortBy('row')->values()->all();

        // Kata sandi awal disimpan terenkripsi di sesi, hanya untuk diunduh admin.
        $request->session()->put('import_result', [
            'type' => $check['type'],
            'created_count' => count($result['created']),
            'problems' => $problems,
            'credentials' => Crypt::encrypt($result['created']),
            'at' => now()->format('Ymd-His'),
        ]);

        return redirect()->route('admin.impor.hasil');
    }

    /** M08b: hasil impor. */
    public function result(Request $request): View|RedirectResponse
    {
        $result = $request->session()->get('import_result');

        if (! $result) {
            return redirect()->route('admin.impor.index');
        }

        return view('admin.impor.hasil', ['result' => $result]);
    }

    /** Unduh daftar akun + kata sandi awal untuk dibagikan. */
    public function download(Request $request): Response|RedirectResponse
    {
        $result = $request->session()->get('import_result');

        if (! $result || $result['created_count'] === 0) {
            return redirect()->route('admin.impor.index');
        }

        $rows = [['Nama', $result['type'] === 'siswa' ? 'NISN' : 'NIP/Username', 'Rombel', 'Kata Sandi Awal']];
        foreach (Crypt::decrypt($result['credentials']) as $account) {
            $rows[] = [$account['name'], $account['username'], $account['rombel'], $account['password']];
        }

        return response(SimpleXlsx::write($rows, 'Akun Baru'), 200, [
            'Content-Type' => self::XLSX,
            'Content-Disposition' => "attachment; filename=\"akun-{$result['type']}-{$result['at']}.xlsx\"",
        ]);
    }
}
