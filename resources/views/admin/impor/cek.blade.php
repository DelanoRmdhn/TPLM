{{-- M08b Hasil cek impor (sebelum Simpan): berapa siap dibuat, baris mana yang bermasalah. --}}
@extends('layouts.app')

@section('title', 'Cek Data Impor')
@section('back', route('admin.impor.index'))
@section('back-label', 'Kembali ke Impor')

@section('content')

    @php($ready = count($check['valid']))
    @php($problemCount = count($check['problems']))

    <p class="flex items-start gap-2 text-text-muted">
        <x-icon name="info" class="mt-0.5 size-5" />
        Data {{ $check['type'] }} belum disimpan. Periksa dulu hasil cek di bawah.
    </p>

    <div class="grid grid-cols-2 gap-3">
        <x-stat :value="$ready" label="Siap dibuat" icon="check-circle" tone="success" />
        <x-stat :value="$problemCount" label="Baris bermasalah" icon="warning-circle" tone="danger" />
    </div>

    @if ($problemCount > 0)
        <x-card padding="p-4">
            <h2 class="flex items-center gap-2 text-h2 font-extrabold text-danger">
                <x-icon name="warning-circle" class="size-6" />
                Baris bermasalah (tidak dibuat)
            </h2>
            <ul class="mt-3 space-y-2">
                @foreach ($check['problems'] as $problem)
                    <li class="rounded-control bg-danger-50 p-3"><span class="font-extrabold text-danger">Baris {{ $problem['row'] }}:</span> {{ $problem['message'] }}</li>
                @endforeach
            </ul>
        </x-card>
    @endif

    @if ($ready > 0)
        <details class="group rounded-card bg-surface p-4 shadow-card">
            <summary class="flex min-h-11 cursor-pointer list-none items-center gap-2 font-extrabold">
                <x-icon name="users-three" class="size-6 text-success" />
                <span class="flex-1">Lihat {{ $ready }} akun yang akan dibuat</span>
                <x-icon name="caret-down" variant="bold" class="size-5 text-text-muted transition group-open:rotate-180" />
            </summary>
            <ul class="mt-3 space-y-2">
                @foreach ($check['valid'] as $row)
                    <li class="flex items-center justify-between gap-3 rounded-control bg-background p-3">
                        <span class="min-w-0 truncate font-bold">{{ $row['name'] }}</span>
                        <span class="flex shrink-0 gap-2">
                            <x-chip icon="identification-card">{{ $row['username'] }}</x-chip>
                            @if ($row['rombel'])
                                <x-chip icon="users-three">{{ $row['rombel'] }}</x-chip>
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        </details>

        <form method="POST" action="{{ route('admin.impor.simpan') }}">
            @csrf
            <x-button variant="success" icon="floppy-disk" size="lg" block>Simpan {{ $ready }} Akun</x-button>
        </form>
        <p class="text-center text-caption text-text-muted">Akun dibuat dengan kata sandi awal yang wajib diganti saat login pertama.</p>
    @else
        <x-card>
            <x-empty-state icon="file-x" title="Belum bisa disimpan">Belum ada baris yang bisa dibuat. Perbaiki file Excel lalu unggah lagi.</x-empty-state>
        </x-card>
    @endif

    <x-button :href="route('admin.impor.index')" variant="secondary" icon="arrow-counter-clockwise" block>Pilih File Lain</x-button>

@endsection
