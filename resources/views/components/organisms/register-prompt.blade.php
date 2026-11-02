@props(['open' => true, 'href' => '#'])

{{-- Organisme: blok pendaftaran di halaman masuk (Komponen · Blok pendaftaran). Tampil sesuai pengaturan admin. --}}
@if ($open)
    <section class="space-y-3 text-center">
        <p class="text-base text-slate-600">Belum punya akun?</p>
        <x-atoms.button variant="outline" icon="plus" :href="$href">Daftar Akun Baru</x-atoms.button>
    </section>

    <x-molecules.notice variant="info">
        Lupa kata sandi? Minta bantuan Wali Kelas atau Operator Sekolah.
    </x-molecules.notice>
@else
    <x-molecules.notice variant="neutral" icon="lock">
        Pendaftaran akun sedang ditutup. Minta akun ke Wali Kelas atau Operator Sekolah.
    </x-molecules.notice>
@endif
