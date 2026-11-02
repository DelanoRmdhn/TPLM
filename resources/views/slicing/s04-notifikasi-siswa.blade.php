<x-templates.mobile title="Notifikasi" nav="siswa" active="notifikasi">
    <x-slot:header>
        <x-molecules.top-bar title="Notifikasi" badge="3 baru" />
    </x-slot:header>

    <x-atoms.button variant="neutral" icon="check">Tandai Semua Sudah Dibaca</x-atoms.button>

    <x-molecules.section-heading title="Hari ini" />
    <x-molecules.item-notification title="Tugas baru" body="Latihan Pecahan Senilai · Matematika" time="07.30" />
    <x-molecules.item-notification title="Tenggat besok" body="Latihan Pecahan Senilai, besok 12.00 WIB" time="06.00" icon="clock" tone="amber" />

    <x-molecules.section-heading title="Kemarin" />
    <x-molecules.item-notification title="Nilai keluar" body="PR Perkalian Dasar: 95 dari 100" time="Kemarin, 15.20" icon="star" tone="green" />
    <x-molecules.item-notification title="Pengumuman baru" body="Ibu Siti: Ulangan harian Bab 1 hari Jumat" time="Kemarin, 10.05" icon="megaphone" :unread="false" />
</x-templates.mobile>
