@props(['moderate' => false])

{{-- Organisme: pengumuman di Forum kelas + komentar. Guru boleh mengatur izin komentar (moderasi). --}}
<x-molecules.card class="space-y-3">
    <div class="flex items-center gap-3">
        <x-atoms.avatar initials="SR" size="sm" />
        <div>
            <p class="text-base font-bold text-slate-900">Ibu Siti Rahma</p>
            <p class="text-sm text-slate-600">Hari ini, 07.30</p>
        </div>
    </div>

    <p class="text-base leading-6 text-slate-900">Besok kita belajar pecahan senilai. Bawa buku tulis dan pensil warna, ya.</p>

    <a href="#" class="flex items-center gap-2 rounded-xl bg-slate-100 px-3 py-3 text-base font-semibold text-blue-700">
        <x-atoms.icon name="file-text" class="size-5" />
        Jadwal pekan ini.pdf
    </a>

    @if ($moderate)
        <div class="flex items-center justify-between gap-3">
            <span class="text-base text-slate-900">Izinkan komentar siswa</span>
            <x-atoms.switch checked aria-label="Izinkan komentar siswa" />
        </div>
    @endif

    <p class="text-sm font-semibold text-slate-600">3 komentar</p>

    @unless ($moderate)
        <x-molecules.forum-comment initials="AF" name="Ahmad Fauzi" time="08.10" text="Baik, Bu. Boleh pakai krayon?" />

        <div class="space-y-2">
            <x-atoms.label for="komentar">Komentar</x-atoms.label>
            <x-atoms.input id="komentar" placeholder="Tulis komentar..." />
            <x-atoms.button icon="send">Kirim Komentar</x-atoms.button>
        </div>
    @endunless
</x-molecules.card>
