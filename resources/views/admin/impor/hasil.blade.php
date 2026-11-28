{{-- M08b Hasil impor: jumlah akun dibuat, unduh daftar kata sandi awal, baris bermasalah. --}}
@extends('layouts.app')

@section('title', 'Hasil Impor')
@section('back', route('admin.akun.index'))
@section('back-label', 'Kembali ke Kelola Akun')

@section('content')

    <div class="grid grid-cols-2 gap-3">
        <x-stat :value="$result['created_count']" label="Akun dibuat" icon="check-circle" tone="success" />
        <x-stat :value="count($result['problems'])" label="Baris bermasalah" icon="warning-circle" tone="danger" />
    </div>

    @if ($result['created_count'] > 0)
        <x-card class="space-y-3">
            <p class="flex items-start gap-2">
                <x-icon name="key" class="mt-0.5 size-5 text-brand" />
                Unduh daftar akun beserta kata sandi awal untuk dibagikan ke {{ $result['type'] === 'siswa' ? 'wali kelas' : 'guru' }}.
            </p>
            <x-button :href="route('admin.impor.unduh')" variant="success" icon="microsoft-excel-logo" block>Unduh Daftar Akun &amp; Kata Sandi</x-button>
        </x-card>
    @endif

    @if (count($result['problems']) > 0)
        <x-card padding="p-4">
            <h2 class="flex items-center gap-2 text-h2 font-extrabold text-danger">
                <x-icon name="warning-circle" class="size-6" />
                Baris bermasalah
            </h2>
            <ul class="mt-3 space-y-2">
                @foreach ($result['problems'] as $problem)
                    <li class="rounded-control bg-danger-50 p-3"><span class="font-extrabold text-danger">Baris {{ $problem['row'] }}:</span> {{ $problem['message'] }}</li>
                @endforeach
            </ul>
            <p class="mt-3 text-caption text-text-muted">Perbaiki baris di atas di file Excel, lalu impor lagi. Baris yang sudah berhasil akan dilaporkan "sudah terdaftar".</p>
        </x-card>
    @endif

    <div class="grid grid-cols-2 gap-3">
        <x-button :href="route('admin.impor.index')" variant="secondary" icon="upload-simple">Impor Lagi</x-button>
        <x-button :href="route('admin.akun.index')" variant="secondary" icon="users-three">Kelola Akun</x-button>
    </div>

@endsection
