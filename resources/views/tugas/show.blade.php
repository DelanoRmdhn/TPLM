{{-- M05 Detail tugas & kumpul foto (siswa), FR-18–FR-20, PB-12. Guru melihat tugas/pengumpulan.blade.php. --}}
@extends('layouts.app')

@section('title', 'Tugas')
@section('back', route('kelas.show', $classroom))
@section('back-label', 'Kembali ke '.$classroom->name)
@section('topbar', 'overlay')

{{-- Judul tugas di atas blok berwarna mapel --}}
@section('hero')
    <x-subject-hero :subject="$classroom->subject">
        <p class="font-semibold opacity-90">{{ $classroom->name }}</p>
        <h1 class="mt-1 text-display leading-tight font-extrabold">{{ $assignment->title }}</h1>
        <x-status-badge :status="$status" class="mt-3 bg-white! ring-2 ring-white/40" />
    </x-subject-hero>
@endsection

@section('content')

    {{-- Desktop: info tugas di kiri, nilai/pengumpulan/form kumpul di kanan --}}
    <div class="grid gap-4 lg:grid-cols-12 lg:items-start lg:gap-6">
    <div class="space-y-4 lg:col-span-7">

    {{-- Tugasnya --}}
    <x-card class="space-y-4">
        <div class="grid grid-cols-2 gap-2">
            <div @class(['rounded-control p-3', 'bg-danger-50 text-danger' => $assignment->isPastDue(), 'bg-background' => ! $assignment->isPastDue()])>
                <p class="flex items-center gap-1.5 text-caption font-bold {{ $assignment->isPastDue() ? '' : 'text-text-muted' }}">
                    <x-icon name="alarm" class="size-4" />
                    Tenggat
                </p>
                <p class="mt-0.5 font-extrabold">{{ $assignment->due_at->translatedFormat('D, j M Y') }}</p>
                <p class="text-caption font-semibold">{{ $assignment->due_at->format('H.i') }} WIB</p>
            </div>
            <div class="rounded-control bg-background p-3">
                <p class="flex items-center gap-1.5 text-caption font-bold text-text-muted">
                    <x-icon name="star" class="size-4" />
                    Nilai maksimal
                </p>
                <p class="mt-0.5 text-h2 font-extrabold">{{ $assignment->max_score }}</p>
            </div>
        </div>

        @if ($assignment->instructions)
            <div>
                <p class="mb-1 flex items-center gap-1.5 font-extrabold">
                    <x-icon name="list-checks" class="size-5 text-brand" />
                    Petunjuk
                </p>
                <p class="whitespace-pre-line">{{ $assignment->instructions }}</p>
            </div>
        @endif

        @include('tugas._lampiran', ['attachments' => $assignment->attachments])
    </x-card>
    </div>

    <div class="space-y-4 lg:sticky lg:top-6 lg:col-span-5">


    {{-- Nilai & umpan balik (FR-25) --}}
    @if ($grade)
        <section class="relative overflow-hidden rounded-card bg-success p-5 text-white shadow-card">
            <span class="absolute -top-6 -right-6 size-28 rounded-full bg-white/10" aria-hidden="true"></span>
            <div class="relative flex items-center gap-4">
                <span class="flex size-20 shrink-0 flex-col items-center justify-center rounded-full bg-white text-success motion-safe:animate-pop">
                    <span class="text-[30px] leading-none font-extrabold">{{ $grade->score }}</span>
                    <span class="text-caption font-bold">dari {{ $assignment->max_score }}</span>
                </span>
                <div>
                    <p class="flex items-center gap-1.5 text-h2 font-extrabold">
                        <x-icon name="trophy" variant="fill" class="size-6 text-sunny" />
                        Nilai kamu
                    </p>
                    <p class="text-white/85">Tugasmu sudah dinilai guru.</p>
                </div>
            </div>
            @if ($grade->feedback)
                <div class="relative mt-4 rounded-[18px] rounded-tl-[6px] bg-white p-4 text-text">
                    <p class="mb-1 flex items-center gap-1.5 text-caption font-bold text-text-muted">
                        <x-icon name="chat-circle-text" class="size-4" />
                        Pesan dari guru
                    </p>
                    <p class="whitespace-pre-line">{{ $grade->feedback }}</p>
                </div>
            @endif
        </section>
    @endif

    {{-- Yang sudah diserahkan --}}
    @if ($submission?->isSubmitted())
        <x-card class="space-y-3">
            <h2 class="flex items-center gap-2 text-h2 font-extrabold">
                <x-icon name="paper-plane-tilt" variant="fill" class="size-6 text-brand" />
                Tugas yang kamu serahkan
            </h2>
            <div class="flex flex-wrap gap-2">
                <x-chip icon="calendar-check">{{ $submission->submitted_at->translatedFormat('j M Y, H.i') }} WIB</x-chip>
                @if ($submission->isLate())
                    <x-chip icon="warning-circle" tone="danger">Terlambat</x-chip>
                @endif
            </div>

            @if ($submission->body)
                <p class="whitespace-pre-line rounded-control bg-background p-3">{{ $submission->body }}</p>
            @endif

            @if ($submission->files->isNotEmpty())
                <div class="grid grid-cols-3 gap-2">
                    @foreach ($submission->files as $file)
                        <a href="{{ route('pengumpulan.file', $file) }}" target="_blank" class="block overflow-hidden rounded-control bg-background">
                            @if ($file->isImage())
                                <img src="{{ route('pengumpulan.file', $file) }}" alt="{{ $file->original_name }}" class="aspect-square w-full object-cover" loading="lazy">
                            @else
                                <span class="flex aspect-square flex-col items-center justify-center gap-1 p-2 text-center">
                                    <x-icon :name="$file->isPdf() ? 'file-pdf' : 'file-doc'" class="size-8 text-brand" />
                                    <span class="line-clamp-2 text-caption break-all">{{ $file->original_name }}</span>
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif

            @if (! $grade && ! $classroom->isArchived())
                <form method="POST" action="{{ route('kelas.tugas.batal', [$classroom, $assignment]) }}"
                    data-confirm-title="Batalkan pengumpulan?"
                    data-confirm="{{ $assignment->isPastDue() ? 'Foto dan jawaban yang sudah dikirim akan dihapus. Tenggat sudah lewat, jadi kalau dikumpulkan ulang akan ditandai terlambat.' : 'Foto dan jawaban yang sudah dikirim akan dihapus. Kamu bisa mengumpulkan ulang.' }}"
                    data-confirm-label="Ya, batalkan" data-confirm-tone="danger">
                    @csrf
                    <x-button variant="ghost" icon="arrow-counter-clockwise" block class="text-danger hover:bg-danger-50">Batalkan Pengumpulan</x-button>
                </form>
            @endif
        </x-card>

    {{-- Form kumpul (PB-12): foto dari kamera / galeri, file, atau teks --}}
    @elseif (! $grade && ! $classroom->isArchived())
        <form id="form-kumpul" method="POST" action="{{ route('kelas.tugas.kumpul', [$classroom, $assignment]) }}" enctype="multipart/form-data"
            class="space-y-4 rounded-card bg-surface p-5 shadow-card">
            @csrf

            <h2 class="flex items-center gap-2 text-h2 font-extrabold">
                <x-icon name="upload-simple" class="size-6 text-brand" />
                Kumpulkan tugas
            </h2>

            @if ($assignment->isPastDue())
                <p class="flex items-start gap-2 rounded-control bg-danger-50 p-3 font-semibold text-danger">
                    <x-icon name="warning-circle" variant="fill" class="mt-0.5 size-5" />
                    Tenggat sudah lewat. Kamu masih bisa mengumpulkan, tapi akan ditandai terlambat.
                </p>
            @endif

            {{-- Hanya JPG/PNG (kamera) dan JPG/PNG/PDF/DOCX (file), sama dengan yang diterima server --}}
            <div class="grid grid-cols-2 gap-3">
                <label class="flex min-h-24 cursor-pointer flex-col items-center justify-center gap-1.5 rounded-[18px] bg-brand font-extrabold text-white shadow-button active:translate-y-[3px] active:shadow-none has-focus-visible:outline-3 has-focus-visible:outline-brand-200">
                    <x-icon name="camera" variant="fill" class="size-8" />
                    Ambil Foto
                    <input type="file" accept="image/jpeg,image/png" capture="environment" class="sr-only" data-picker>
                </label>
                <label class="flex min-h-24 cursor-pointer flex-col items-center justify-center gap-1.5 rounded-[18px] border-2 border-dashed border-brand-200 bg-brand-50 font-extrabold text-brand active:bg-brand-100 has-focus-visible:outline-3 has-focus-visible:outline-brand-200">
                    <x-icon name="images" class="size-8" />
                    Pilih File
                    <input type="file" accept=".jpg,.jpeg,.png,.pdf,.docx,image/jpeg,image/png,application/pdf" multiple class="sr-only" data-picker>
                </label>
            </div>

            {{-- Input yang benar-benar dikirim; diisi lewat JavaScript setelah foto dikompres --}}
            <input type="file" name="files[]" id="files" multiple class="hidden">

            <ul id="daftar-file" class="grid grid-cols-3 gap-2 empty:hidden" aria-label="File yang akan dikirim"></ul>
            <p id="info-file" class="flex items-center gap-1.5 text-caption text-text-muted">
                <x-icon name="info" class="size-4" />
                <span data-info-text>Bisa beberapa foto (JPG/PNG), PDF, atau Word. Foto otomatis dikecilkan supaya cepat terkirim.</span>
            </p>

            <div>
                <label for="body" class="mb-1.5 block font-bold">Jawaban tulisan <span class="font-semibold text-text-muted">(boleh kosong)</span></label>
                <textarea id="body" name="body" rows="3" maxlength="5000" placeholder="Tulis jawabanmu di sini"
                    class="w-full rounded-control border-2 border-border bg-surface p-4 outline-none focus:border-brand focus:ring-4 focus:ring-brand-100">{{ old('body') }}</textarea>
            </div>

            <x-button id="tombol-kirim" variant="success" icon="paper-plane-tilt" size="lg" block>Serahkan Tugas</x-button>
        </form>

        {{-- Cetakan untuk pratinjau file (diisi JavaScript) --}}
        <template id="file-tile">
            <li class="relative">
                <div data-preview class="flex aspect-square items-center justify-center overflow-hidden rounded-control bg-background">
                    <x-icon name="file-text" class="size-9 text-brand" data-file-icon />
                </div>
                <p data-name class="mt-1 truncate text-caption text-text-muted"></p>
                <button type="button" data-remove class="absolute -top-2 -right-2 flex size-9 items-center justify-center rounded-full bg-text text-white shadow-raised">
                    <x-icon name="x" variant="bold" class="size-4" />
                </button>
            </li>
        </template>

        <script>
            (() => {
                const MAX_FILES = 10;
                const MAX_SIDE = 1600;      // px, sisi terpanjang foto setelah dikompres
                const QUALITY = 0.8;        // kualitas JPEG
                const form = document.getElementById('form-kumpul');
                const realInput = document.getElementById('files');
                const list = document.getElementById('daftar-file');
                const info = document.querySelector('#info-file [data-info-text]');
                const defaultInfo = info.textContent;
                const button = document.getElementById('tombol-kirim');
                const tile = document.getElementById('file-tile');
                const picked = [];
                const canBuild = typeof DataTransfer !== 'undefined';
                const ALLOWED = @js(\App\Models\SubmissionFile::ALLOWED_EXTENSIONS);
                const MAX_MB = @js(\App\Models\SubmissionFile::MAX_SIZE_KB / 1024);

                // Browser lama: kirim langsung tanpa kompres
                if (!canBuild) {
                    document.querySelectorAll('[data-picker]').forEach((el) => el.setAttribute('name', 'files[]'));
                    return;
                }

                // NFR-05: kecilkan foto sebelum diunggah
                async function compress(file) {
                    if (!file.type.startsWith('image/')) return file;
                    try {
                        const bitmap = await createImageBitmap(file);
                        const scale = Math.min(1, MAX_SIDE / Math.max(bitmap.width, bitmap.height));
                        const canvas = document.createElement('canvas');
                        canvas.width = Math.round(bitmap.width * scale);
                        canvas.height = Math.round(bitmap.height * scale);
                        canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
                        const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', QUALITY));
                        if (!blob || blob.size >= file.size) return file;
                        const name = file.name.replace(/\.[^.]+$/, '') + '.jpg';
                        return new File([blob], name, { type: 'image/jpeg', lastModified: Date.now() });
                    } catch (e) {
                        return file; // gagal kompres: pakai file asli
                    }
                }

                function render() {
                    list.replaceChildren();
                    picked.forEach((file, i) => {
                        const item = tile.content.firstElementChild.cloneNode(true);
                        if (file.type.startsWith('image/')) {
                            const img = document.createElement('img');
                            img.src = URL.createObjectURL(file);
                            img.className = 'size-full object-cover';
                            img.alt = file.name;
                            item.querySelector('[data-preview]').replaceChildren(img);
                        }
                        item.querySelector('[data-name]').textContent = file.name;
                        const remove = item.querySelector('[data-remove]');
                        remove.setAttribute('aria-label', 'Hapus ' + file.name);
                        remove.addEventListener('click', () => { picked.splice(i, 1); render(); });
                        list.append(item);
                    });

                    const dt = new DataTransfer();
                    picked.forEach((f) => dt.items.add(f));
                    realInput.files = dt.files;
                    info.textContent = picked.length ? picked.length + ' file siap dikirim.' : defaultInfo;
                }

                document.querySelectorAll('[data-picker]').forEach((picker) => {
                    picker.addEventListener('change', async () => {
                        // Tolak file yang tidak didukung (mis. SVG, GIF, HEIC) sebelum masuk daftar
                        const { accepted, problems } = window.checkFiles(picker.files, ALLOWED, MAX_MB);
                        if (problems.length) {
                            window.showSheet(problems.length > 1 ? 'Beberapa file tidak bisa dikirim' : 'File ini tidak bisa dikirim', problems);
                        }

                        if (accepted.length) {
                            info.textContent = 'Menyiapkan foto...';
                        }
                        for (const file of accepted) {
                            if (picked.length >= MAX_FILES) {
                                window.showToast('Paling banyak ' + MAX_FILES + ' foto atau file.', 'error');
                                break;
                            }
                            picked.push(await compress(file));
                        }
                        picker.value = '';
                        render();
                    });
                });

                form.addEventListener('submit', () => {
                    button.disabled = true;
                    button.lastChild.textContent = ' Mengirim...';
                });
            })();
        </script>
    @endif

    </div>
    </div>

@endsection
