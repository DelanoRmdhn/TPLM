{{-- Daftar tugas + materi dalam satu topik: tugas dulu, lalu materi. Tiap item kartu putih sendiri. --}}
<ul class="grid gap-2.5 lg:grid-cols-2">
    @include('kelas._tugas-list', ['assignments' => $assignments ?? collect()])

    @forelse ($materials as $material)
        <li>
            <a href="{{ route('kelas.materi.show', [$classroom, $material]) }}"
                class="flex items-center gap-3.5 rounded-[22px] bg-surface p-3 pr-4 shadow-card transition active:scale-[0.99]">
                <span class="flex size-16 shrink-0 items-center justify-center rounded-[18px] bg-mapel-teal-soft text-mapel-teal">
                    <x-icon name="book-open" variant="fill" class="size-8" />
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block text-[12px] font-extrabold tracking-wide text-mapel-teal uppercase">Materi</span>
                    <span class="mt-0.5 block leading-snug font-extrabold">{{ $material->title }}</span>
                    <span class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-[12px] font-bold text-text-muted">
                        <span class="inline-flex items-center gap-1"><x-icon name="calendar-blank" class="size-3.5" />{{ $material->created_at->translatedFormat('j M Y') }}</span>
                        @if ($material->attachments->isNotEmpty())
                            <span class="inline-flex items-center gap-1"><x-icon name="paperclip" class="size-3.5" />{{ $material->attachments->count() }} lampiran</span>
                        @endif
                    </span>
                </span>
                <x-icon name="caret-right" variant="bold" class="size-5 text-text-muted" />
            </a>
        </li>
    @empty
        @if (($assignments ?? collect())->isEmpty())
            <li class="rounded-[22px] border-2 border-dashed lg:col-span-2 border-border p-4 text-center text-caption text-text-muted">Belum ada tugas atau materi di topik ini.</li>
        @endif
    @endforelse
</ul>
