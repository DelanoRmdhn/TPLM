{{-- Tugas Saya (FR-26, PB-17): belum selesai dikelompokkan per tenggat, sudah selesai terpisah. --}}
@extends('layouts.app')

@section('title', 'Tugas Saya')

@section('content')

    @php
        $groupIcons = [
            'Hari ini' => ['sun', 'text-warning'],
            'Besok' => ['sun-horizon', 'text-brand'],
            'Minggu ini' => ['calendar-blank', 'text-brand'],
            'Nanti' => ['calendar-dots', 'text-text-muted'],
            'Lewat tenggat' => ['warning-circle', 'text-danger'],
        ];
    @endphp

    {{-- Pilihan tampilan: belum / sudah selesai --}}
    <div class="grid grid-cols-2 gap-1 rounded-full bg-surface p-1 shadow-card" role="tablist" aria-label="Pilih daftar tugas">
        @foreach (['belum' => 'Belum selesai ('.$groups->flatten(1)->count().')', 'sudah' => 'Sudah selesai ('.$done->count().')'] as $key => $label)
            <button id="tab-{{ $key }}" type="button" role="tab" aria-controls="panel-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                data-tab="{{ $key }}"
                class="min-h-12 rounded-full px-3 font-bold text-text-muted transition aria-selected:bg-brand aria-selected:text-white aria-selected:shadow-button">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Belum selesai: urut tenggat terdekat, lewat tenggat ditandai (PB-17) --}}
    <div id="panel-belum" role="tabpanel" aria-labelledby="tab-belum" class="space-y-6 pt-1">
        @forelse ($groups as $group => $rows)
            @php([$icon, $color] = $groupIcons[$group] ?? ['calendar-blank', 'text-brand'])
            <section class="space-y-3">
                <h2 class="flex items-center gap-2 font-extrabold {{ $group === 'Lewat tenggat' ? 'text-danger' : '' }}">
                    <x-icon :name="$icon" variant="fill" class="size-6 {{ $color }}" />
                    {{ $group }}
                    <span class="rounded-full bg-surface px-2 text-caption text-text-muted shadow-card">{{ $rows->count() }}</span>
                </h2>
                @foreach ($rows as $row)
                    <x-task-card :row="$row" in-pending-list />
                @endforeach
            </section>
        @empty
            <x-card>
                <x-empty-state icon="confetti" title="Hore, semua tugas sudah dikerjakan!">Istirahat dulu, nanti cek lagi ya.</x-empty-state>
            </x-card>
        @endforelse
    </div>

    {{-- Sudah selesai: diserahkan & dinilai (PB-13) --}}
    <div id="panel-sudah" role="tabpanel" aria-labelledby="tab-sudah" class="hidden space-y-3 pt-1">
        @forelse ($done as $row)
            <x-task-card :row="$row" />
        @empty
            <x-card>
                <x-empty-state icon="paper-plane-tilt" title="Belum ada yang dikumpulkan">Tugas yang sudah kamu serahkan akan muncul di sini.</x-empty-state>
            </x-card>
        @endforelse
    </div>

    <script>
        document.querySelectorAll('[data-tab]').forEach((tab) => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('[data-tab]').forEach((other) => {
                    const active = other === tab;
                    other.setAttribute('aria-selected', String(active));
                    document.getElementById('panel-' + other.dataset.tab).classList.toggle('hidden', !active);
                });
            });
        });
    </script>

@endsection
