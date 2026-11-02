@props(['average', 'graded'])

{{-- Organisme: kartu rata-rata nilai siswa (M07) --}}
<section class="rounded-2xl bg-blue-600 p-4 text-white">
    <p class="text-sm font-semibold">Rata-rata nilaimu</p>
    <p class="mt-1 flex items-baseline gap-2">
        <span class="text-[40px] font-bold leading-[48px]">{{ $average }}</span>
        <span class="text-base font-semibold">dari 100</span>
    </p>
    <p class="text-sm">{{ $graded }} tugas sudah dinilai</p>
</section>
