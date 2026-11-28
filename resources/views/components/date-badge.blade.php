{{-- Lencana tanggal berwarna mapel: angka tanggal besar + bulan singkat, mis. "30 Nov". --}}
@props(['date', 'subject'])

<span {{ $attributes->class(['flex size-16 shrink-0 flex-col items-center justify-center rounded-[18px] leading-none', $subject->solidClasses()]) }}>
    <span class="text-[24px] font-extrabold">{{ $date->format('j') }}</span>
    <span class="mt-0.5 text-[12px] font-bold opacity-90">{{ $date->translatedFormat('M') }}</span>
</span>
