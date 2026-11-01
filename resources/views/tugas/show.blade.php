{{-- M05 Detail tugas & kumpul foto (siswa) — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', $assignment->title)

@section('content')

    <a href="{{ route('kelas.show', $classroom) }}" class="inline-flex h-12 items-center text-brand underline">← {{ $classroom->name }}</a>

    <section class="space-y-2 rounded-card border border-border bg-white p-4">
        <span class="inline-block rounded-full px-3 py-1 text-caption font-semibold {{ $status->badgeClass() }}">{{ $status->label() }}</span>
        <h1 class="text-h1 font-bold">{{ $assignment->title }}</h1>
        <p class="{{ $assignment->isPastDue() ? 'text-danger' : 'text-text-muted' }}">
            Tenggat: {{ $assignment->due_at->translatedFormat('l, j F Y, H.i') }} WIB
        </p>
        <p class="text-caption text-text-muted">Nilai maksimal {{ $assignment->max_score }}</p>

        @if ($assignment->instructions)
            <p class="whitespace-pre-line pt-2">{{ $assignment->instructions }}</p>
        @endif

        @include('tugas._lampiran', ['attachments' => $assignment->attachments])
    </section>

    {{-- Nilai & umpan balik (FR-25) --}}
    @if ($grade)
        <section class="rounded-card border border-green-200 bg-green-50 p-4">
            <p class="text-caption text-green-800">Nilai kamu</p>
            <p class="text-h1 font-bold text-green-800">{{ $grade->score }} <span class="text-body font-normal">/ {{ $assignment->max_score }}</span></p>
            @if ($grade->feedback)
                <p class="mt-2 whitespace-pre-line">💬 {{ $grade->feedback }}</p>
            @endif
        </section>
    @endif

    {{-- Yang sudah diserahkan --}}
    @if ($submission?->isSubmitted())
        <section class="space-y-3 rounded-card border border-border bg-white p-4">
            <h2 class="font-bold">Tugas yang kamu serahkan</h2>
            <p class="text-caption text-text-muted">
                {{ $submission->submitted_at->translatedFormat('l, j F Y, H.i') }} WIB
                @if ($submission->isLate()) · <span class="font-semibold text-danger">Terlambat</span> @endif
            </p>

            @if ($submission->body)
                <p class="whitespace-pre-line">{{ $submission->body }}</p>
            @endif

            <div class="grid grid-cols-2 gap-2">
                @foreach ($submission->files as $file)
                    <a href="{{ route('pengumpulan.file', $file) }}" target="_blank" class="block overflow-hidden rounded-control border border-border">
                        @if ($file->isImage())
                            <img src="{{ route('pengumpulan.file', $file) }}" alt="{{ $file->original_name }}" class="aspect-square w-full object-cover" loading="lazy">
                        @else
                            <span class="flex aspect-square items-center justify-center p-2 text-center text-caption">📄 {{ $file->original_name }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>

    {{-- Form kumpul (PB-12): foto dari kamera / galeri, file, atau teks --}}
    @elseif (! $grade)
        <form id="form-kumpul" method="POST" action="{{ route('kelas.tugas.kumpul', [$classroom, $assignment]) }}" enctype="multipart/form-data"
            class="space-y-3 rounded-card border border-border bg-white p-4">
            @csrf

            <h2 class="font-bold">Kumpulkan tugas</h2>

            @if ($assignment->isPastDue())
                <p class="rounded-control bg-red-50 p-3 text-caption text-red-700">Tenggat sudah lewat. Kamu masih bisa mengumpulkan, tapi akan ditandai terlambat.</p>
            @endif

            <div class="grid grid-cols-2 gap-3">
                <label class="flex h-12 cursor-pointer items-center justify-center rounded-control bg-brand font-semibold text-white">
                    📷 Ambil Foto
                    <input type="file" accept="image/*" capture="environment" class="sr-only" data-picker>
                </label>
                <label class="flex h-12 cursor-pointer items-center justify-center rounded-control border border-brand font-semibold text-brand">
                    🖼️ Pilih File
                    <input type="file" accept="image/*,.pdf,.docx" multiple class="sr-only" data-picker>
                </label>
            </div>

            {{-- Input yang benar-benar dikirim; diisi lewat JavaScript setelah foto dikompres --}}
            <input type="file" name="files[]" id="files" multiple class="hidden">

            <ul id="daftar-file" class="space-y-2"></ul>
            <p id="info-file" class="text-caption text-text-muted">Bisa beberapa foto. Foto otomatis dikecilkan supaya cepat terkirim.</p>

            <div>
                <label for="body" class="mb-1 block font-semibold">Jawaban teks (boleh kosong)</label>
                <textarea id="body" name="body" rows="3" maxlength="5000" class="w-full rounded-control border border-border p-3">{{ old('body') }}</textarea>
            </div>

            <button type="submit" id="tombol-kirim" class="h-12 w-full rounded-control bg-green-700 font-semibold text-white">Serahkan Tugas</button>
        </form>

        <script>
            (() => {
                const MAX_FILES = 10;
                const MAX_SIDE = 1600;      // px, sisi terpanjang foto setelah dikompres
                const QUALITY = 0.8;        // kualitas JPEG
                const form = document.getElementById('form-kumpul');
                const realInput = document.getElementById('files');
                const list = document.getElementById('daftar-file');
                const info = document.getElementById('info-file');
                const button = document.getElementById('tombol-kirim');
                const picked = [];
                const canBuild = typeof DataTransfer !== 'undefined';

                // Browser lama: kirim langsung tanpa kompres
                if (!canBuild) {
                    document.querySelectorAll('[data-picker]').forEach((el) => el.setAttribute('name', 'files[]'));
                    return;
                }

                const formatSize = (b) => b >= 1048576 ? (b / 1048576).toFixed(1).replace('.', ',') + ' MB' : Math.max(1, Math.round(b / 1024)) + ' KB';

                // NFR-05: kecilkan foto sebelum diunggah
                async function compress(file) {
                    if (!file.type.startsWith('image/') || file.type === 'image/gif') return file;
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
                    list.innerHTML = '';
                    picked.forEach((file, i) => {
                        const li = document.createElement('li');
                        li.className = 'flex items-center gap-3 rounded-control border border-border p-2';
                        if (file.type.startsWith('image/')) {
                            const img = document.createElement('img');
                            img.src = URL.createObjectURL(file);
                            img.className = 'size-14 shrink-0 rounded object-cover';
                            img.alt = '';
                            li.append(img);
                        }
                        const text = document.createElement('span');
                        text.className = 'min-w-0 flex-1 truncate';
                        text.textContent = file.name + ' · ' + formatSize(file.size);
                        const remove = document.createElement('button');
                        remove.type = 'button';
                        remove.className = 'h-12 shrink-0 rounded-control border border-border px-3';
                        remove.textContent = 'Hapus';
                        remove.addEventListener('click', () => { picked.splice(i, 1); render(); });
                        li.append(text, remove);
                        list.append(li);
                    });

                    const dt = new DataTransfer();
                    picked.forEach((f) => dt.items.add(f));
                    realInput.files = dt.files;
                    info.textContent = picked.length ? picked.length + ' file siap dikirim.' : 'Bisa beberapa foto. Foto otomatis dikecilkan supaya cepat terkirim.';
                }

                document.querySelectorAll('[data-picker]').forEach((picker) => {
                    picker.addEventListener('change', async () => {
                        info.textContent = 'Menyiapkan foto...';
                        for (const file of picker.files) {
                            if (picked.length >= MAX_FILES) { alert('Maksimal ' + MAX_FILES + ' foto/file.'); break; }
                            picked.push(await compress(file));
                        }
                        picker.value = '';
                        render();
                    });
                });

                form.addEventListener('submit', () => {
                    button.disabled = true;
                    button.textContent = 'Mengirim...';
                });
            })();
        </script>
    @endif

@endsection
