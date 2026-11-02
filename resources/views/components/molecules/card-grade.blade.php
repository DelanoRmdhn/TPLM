@props(['subject', 'title', 'score', 'teacher', 'comment', 'date'])

{{-- Molekul: kartu nilai (Komponen · Kartu nilai) --}}
<x-molecules.card {{ $attributes }}>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-semibold text-blue-700">{{ $subject }}</p>
            <p class="text-base font-bold text-slate-900">{{ $title }}</p>
        </div>

        <span class="shrink-0 rounded-[10px] bg-green-100 px-3 py-1.5 text-green-700">
            <span class="text-xl font-bold">{{ $score }}</span><span class="text-sm font-semibold">/100</span>
        </span>
    </div>

    <div class="mt-3 flex gap-2.5 rounded-xl bg-slate-100 p-3">
        <x-atoms.icon name="message-square" class="mt-0.5 size-5 text-slate-600" />
        <div class="min-w-0 text-base leading-6 text-slate-800">
            <p class="font-semibold">{{ $teacher }}</p>
            <p>{{ $comment }}</p>
        </div>
    </div>

    <p class="mt-3 text-sm text-slate-600">{{ $date }}</p>
</x-molecules.card>
