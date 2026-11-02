<x-templates.mobile title="Notifikasi (Guru)" nav="guru" active="notifikasi">
    <x-slot:header>
        <x-molecules.top-bar title="Notifikasi" badge="3 baru" />
    </x-slot:header>

    <x-atoms.button variant="neutral" icon="check">Tandai Semua Sudah Dibaca</x-atoms.button>

    <x-molecules.section-heading title="Hari ini" />
    <x-molecules.item-notification title="24 tugas dikumpulkan" body="Latihan Pecahan Senilai · Matematika 4-A" time="08.15" />
    <x-molecules.item-notification title="Pertanyaan baru" body="Ahmad Fauzi bertanya di Latihan Pecahan Senilai" time="07.40" icon="message-square" />

    <x-molecules.section-heading title="Kemarin" />
    <x-molecules.item-notification title="Pengumpulan terlambat" body="Budi Santoso mengirim lewat tenggat" time="Kemarin, 15.40" icon="clock" tone="red" />
    <x-molecules.item-notification title="Materi sudah dibaca" body="28 siswa membuka Konsep Pecahan Senilai" time="Kemarin, 09.12" icon="book-open" tone="green" :unread="false" />
</x-templates.mobile>
