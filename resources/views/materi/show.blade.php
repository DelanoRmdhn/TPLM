{{-- M03c / M03c2 Buka materi: video YouTube tampil langsung, gambar & PDF dibuka di browser (PB-10). --}}
@extends('layouts.app')

@section('title', 'Materi')
@section('back', route('kelas.show', $classroom))
@section('back-label', 'Kembali ke '.$classroom->name)
@section('topbar', 'overlay')

@section('hero')
    <x-subject-hero :subject="$classroom->subject">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/25 px-3 py-1 text-caption font-extrabold">
            <x-icon name="book-open" variant="fill" class="size-4" />
            Materi
        </span>
        <h1 class="mt-3 text-display leading-tight font-extrabold">{{ $material->title }}</h1>
        <p class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 font-semibold opacity-90">
            <span class="inline-flex items-center gap-1.5"><x-icon name="chalkboard-teacher" class="size-5" />{{ $material->author->name }}</span>
            <span class="inline-flex items-center gap-1.5"><x-icon name="calendar-blank" class="size-5" />{{ $material->created_at->translatedFormat('j M Y') }}</span>
        </p>
    </x-subject-hero>
@endsection

@section('content')

    <x-card>
        <p class="flex items-center gap-1.5 text-caption font-bold text-text-muted">
            <x-icon name="bookmark-simple" class="size-4 text-brand" />
            {{ $material->topic?->name ?? 'Tanpa topik' }}
        </p>
        @if ($material->description)
            <p class="mt-2 whitespace-pre-line">{{ $material->description }}</p>
        @else
            <p class="mt-2 text-text-muted">Buka lampiran di bawah ini ya.</p>
        @endif
    </x-card>

    <div class="grid gap-4 lg:grid-cols-2">
    @foreach ($material->attachments as $attachment)
        <x-card padding="p-4" class="space-y-3">

            @if ($attachment->isLink())
                @if ($id = $attachment->youtubeId())
                    <div class="aspect-video overflow-hidden rounded-control bg-black">
                        <iframe class="size-full" src="https://www.youtube-nocookie.com/embed/{{ $id }}"
                            title="Video YouTube" loading="lazy" allowfullscreen
                            allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"></iframe>
                    </div>
                @endif
                <x-button :href="$attachment->url" target="_blank" rel="noopener" variant="secondary" icon="youtube-logo" block>Buka di YouTube</x-button>

            @else
                <x-attachment-link :attachment="$attachment" bare />

                {{-- PB-10: gambar tampil langsung, PDF dibuka di browser tanpa aplikasi tambahan --}}
                @if ($attachment->isImage())
                    <img src="{{ route('lampiran.show', $attachment) }}" alt="{{ $attachment->original_name }}" class="w-full rounded-control" loading="lazy">
                @endif

                <div class="grid grid-cols-2 gap-2">
                    @if ($attachment->isPdf() || $attachment->isImage())
                        <x-button :href="route('lampiran.show', $attachment)" target="_blank" icon="eye">Buka</x-button>
                    @endif
                    <x-button :href="route('lampiran.show', [$attachment, 'unduh' => 1])" variant="secondary" icon="download-simple"
                        :class="$attachment->isPdf() || $attachment->isImage() ? '' : 'col-span-2'">Unduh</x-button>
                </div>
            @endif

        </x-card>
    @endforeach
    </div>

    @if ($canManage)
        <div class="grid grid-cols-2 gap-2">
            <x-button :href="route('kelas.materi.edit', [$classroom, $material])" variant="secondary" icon="pencil-simple">Ubah Materi</x-button>
            <form method="POST" action="{{ route('kelas.materi.destroy', [$classroom, $material]) }}"
                data-confirm="{{ 'Hapus materi '.$material->title.'? Siswa tidak bisa membukanya lagi.' }}" data-confirm-label="Ya, hapus" data-confirm-tone="danger">
                @csrf
                @method('DELETE')
                <x-button variant="ghost" icon="trash" block class="text-danger hover:bg-danger-50">Hapus Materi</x-button>
            </form>
        </div>
    @endif

@endsection
