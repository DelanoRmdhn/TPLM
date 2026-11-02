<x-templates.mobile title="Pop-up Umpan Balik">
    <x-slot:header>
        <x-molecules.top-bar title="Pop-up umpan balik" subtitle="Muncul setelah tombol ditekan. Satu tombol Oke." />
    </x-slot:header>

    <div class="flex flex-col items-center gap-4">
        <x-molecules.popup title="Sudah disalin" icon="file-text" tone="blue">Tempel (paste) untuk membagikannya.</x-molecules.popup>
        <x-molecules.popup title="Terkirim" icon="send">Pesanmu sudah terkirim.</x-molecules.popup>
        <x-molecules.popup title="Pengingat terkirim" icon="bell">6 siswa menerima notifikasi untuk segera mengumpulkan tugas.</x-molecules.popup>
        <x-molecules.popup title="Kata sandi disimpan" icon="lock">Pakai kata sandi baru saat login berikutnya.</x-molecules.popup>
        <x-molecules.popup title="Semua sudah dibaca" icon="check" />
        <x-molecules.popup title="Kode baru dibuat" icon="refresh" tone="blue">Kode lama tidak bisa dipakai lagi. Kode baru: MTK9B7</x-molecules.popup>
        <x-molecules.popup title="Akun diaktifkan" icon="user">Budi Santoso bisa login lagi.</x-molecules.popup>
        <x-molecules.popup title="Berhasil ditambahkan" icon="plus" />
    </div>
</x-templates.mobile>
