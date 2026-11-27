{{-- Daftar kelas (admin) + pindah guru — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Daftar Kelas')

@section('content')

    <a href="{{ route('admin.beranda') }}" class="inline-flex h-12 items-center text-brand underline">← Beranda</a>

    <form method="GET" class="grid grid-cols-2 gap-2 rounded-card border border-border bg-white p-4">
        <input name="q" value="{{ request('q') }}" placeholder="Cari nama kelas / guru"
            class="col-span-2 h-12 rounded-control border border-border px-3">
        <select name="status" aria-label="Status kelas" class="h-12 rounded-control border border-border px-2">
            <option value="">Kelas berjalan</option>
            <option value="arsip" @selected($archived)>Kelas diarsipkan</option>
        </select>
        <button type="submit" class="h-12 rounded-control bg-brand font-semibold text-white">Tampilkan</button>
    </form>

    <section class="rounded-card border border-border bg-white p-4">
        <h1 class="font-bold">{{ $archived ? 'Kelas diarsipkan' : 'Kelas berjalan' }} ({{ $classrooms->total() }})</h1>
        <p class="mt-1 text-caption text-text-muted">Pindahkan kelas ke guru lain kalau guru pengajarnya pindah tugas. Materi, tugas, dan nilai tetap di kelas.</p>

        <ul class="mt-2 divide-y divide-border">
            @forelse ($classrooms as $kelas)
                <li class="space-y-2 py-3">
                    <div>
                        <p class="font-semibold">{{ $kelas->name }}</p>
                        <p class="text-caption text-text-muted">
                            {{ $kelas->subject->name }} · {{ $kelas->rombel?->name ?? 'tanpa rombel' }} · {{ $kelas->academic_year }} · {{ $kelas->students_count }} siswa
                        </p>
                        <p class="text-caption">Guru: <span class="font-semibold">{{ $kelas->teacher->name }}</span>
                            @unless ($kelas->teacher->is_active) <span class="text-danger">(nonaktif)</span> @endunless
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.kelas.guru', $kelas) }}" class="flex gap-2"
                        onsubmit="return confirm(@js('Pindahkan kelas '.$kelas->name.' ke guru yang dipilih?'))">
                        @csrf
                        @method('PUT')
                        <label for="guru-{{ $kelas->id }}" class="sr-only">Guru baru untuk {{ $kelas->name }}</label>
                        <select id="guru-{{ $kelas->id }}" name="teacher_id" required class="h-12 min-w-0 flex-1 rounded-control border border-border px-2">
                            <option value="">Pindahkan ke guru…</option>
                            @foreach ($teachers as $teacher)
                                @continue($teacher->id === $kelas->teacher_id)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="h-12 shrink-0 rounded-control border border-brand px-4 font-semibold text-brand">Pindahkan</button>
                    </form>
                </li>
            @empty
                <li class="py-3 text-text-muted">Belum ada kelas.</li>
            @endforelse
        </ul>

        {{ $classrooms->links() }}
    </section>

@endsection
