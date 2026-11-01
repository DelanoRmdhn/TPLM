{{-- M03c / M03c2 Buka materi — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', $material->title)

@section('content')

    <a href="{{ route('kelas.show', $classroom) }}" class="inline-flex h-12 items-center text-brand underline">← {{ $classroom->name }}</a>

    <section class="rounded-card border border-border bg-white p-4">
        <p class="text-caption text-text-muted">
            {{ $material->topic?->name ?? 'Tanpa topik' }} · {{ $material->author->name }} · {{ $material->created_at->translatedFormat('j M Y') }}
        </p>
        <h1 class="text-h1 font-bold">{{ $material->title }}</h1>

        @if ($material->description)
            <p class="mt-3 whitespace-pre-line">{{ $material->description }}</p>
        @endif
    </section>

    @foreach ($material->attachments as $attachment)
        <section class="space-y-3 rounded-card border border-border bg-white p-4">

            @if ($attachment->isLink())
                @if ($id = $attachment->youtubeId())
                    <div class="aspect-video overflow-hidden rounded-control bg-black">
                        <iframe class="size-full" src="https://www.youtube-nocookie.com/embed/{{ $id }}"
                            title="Video YouTube" loading="lazy" allowfullscreen
                            allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"></iframe>
                    </div>
                @endif
                <a href="{{ $attachment->url }}" target="_blank" rel="noopener" class="flex h-12 items-center justify-center rounded-control border border-border">Buka di YouTube</a>

            @else
                <p class="font-semibold">{{ $attachment->original_name }}</p>
                <p class="text-caption text-text-muted">{{ $attachment->readableSize() }}</p>

                {{-- PB-10: gambar tampil langsung, PDF dibuka di browser tanpa aplikasi tambahan --}}
                @if ($attachment->isImage())
                    <img src="{{ route('lampiran.show', $attachment) }}" alt="{{ $attachment->original_name }}" class="w-full rounded-control border border-border" loading="lazy">
                @endif

                <div class="grid grid-cols-2 gap-3">
                    @if ($attachment->isPdf() || $attachment->isImage())
                        <a href="{{ route('lampiran.show', $attachment) }}" target="_blank" class="flex h-12 items-center justify-center rounded-control bg-brand font-semibold text-white">Buka</a>
                    @endif
                    <a href="{{ route('lampiran.show', [$attachment, 'unduh' => 1]) }}" class="flex h-12 items-center justify-center rounded-control border border-border {{ $attachment->isPdf() || $attachment->isImage() ? '' : 'col-span-2' }}">Unduh</a>
                </div>
            @endif

        </section>
    @endforeach

    @if ($canManage)
        <form method="POST" action="{{ route('kelas.materi.destroy', [$classroom, $material]) }}"
            onsubmit="return confirm(@js('Hapus materi '.$material->title.'? File lampirannya ikut terhapus.'))">
            @csrf
            @method('DELETE')
            <button type="submit" class="h-12 w-full rounded-control border border-red-300 text-danger">Hapus Materi</button>
        </form>
    @endif

@endsection
