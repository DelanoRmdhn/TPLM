// Cek file di HP sebelum diunggah: jenis & ukuran harus sesuai, jadi siswa/guru langsung tahu
// kalau salah pilih file (tanpa menunggu unggahan selesai lalu ditolak server).
// Server tetap memeriksa ulang (SubmissionFile / Attachment).

const TYPE_LABELS = {
    jpg: 'JPG',
    jpeg: 'JPG',
    png: 'PNG',
    pdf: 'PDF',
    docx: 'Word (DOCX)',
    xlsx: 'Excel (XLSX)',
};

/** "JPG, PNG, atau PDF" */
export function describeTypes(extensions) {
    const labels = [...new Set(extensions.map((ext) => TYPE_LABELS[ext] ?? ext.toUpperCase()))];

    return labels.length > 1 ? `${labels.slice(0, -1).join(', ')}, atau ${labels.at(-1)}` : labels[0];
}

/**
 * Pisahkan file yang boleh dan yang ditolak.
 * @returns {{accepted: File[], problems: string[]}}
 */
export function checkFiles(files, extensions, maxMb) {
    const accepted = [];
    const problems = [];

    for (const file of files) {
        const ext = file.name.includes('.') ? file.name.split('.').pop().toLowerCase() : '';

        if (!extensions.includes(ext)) {
            problems.push(`${file.name} tidak bisa dipakai. Pilih file ${describeTypes(extensions)}.`);
        } else if (maxMb && file.size > maxMb * 1024 * 1024) {
            problems.push(`${file.name} terlalu besar. Ukuran paling besar ${maxMb} MB.`);
        } else {
            accepted.push(file);
        }
    }

    return { accepted, problems };
}

function formatSize(bytes) {
    return bytes >= 1048576 ? `${(bytes / 1048576).toFixed(1).replace('.', ',')} MB` : `${Math.max(1, Math.round(bytes / 1024))} KB`;
}

/** Tampilkan nama file yang dipilih di <ul id="..."> (atribut data-file-list pada input). */
function renderList(input) {
    const list = input.dataset.fileList ? document.getElementById(input.dataset.fileList) : null;
    if (!list) {
        return;
    }

    list.replaceChildren(
        ...[...input.files].map((file) => {
            const li = document.createElement('li');
            li.className = 'flex items-center justify-between gap-3 rounded-[12px] bg-background px-3 py-2 text-caption';
            const name = document.createElement('span');
            name.className = 'min-w-0 truncate font-bold';
            name.textContent = file.name;
            const size = document.createElement('span');
            size.className = 'shrink-0 text-text-muted';
            size.textContent = formatSize(file.size);
            li.append(name, size);

            return li;
        }),
    );
}

/** Input biasa: <input type="file" data-file-types="pdf,jpg,png" data-file-max-mb="10" [data-file-list="id-ul"]> */
export function initFileInputs() {
    document.querySelectorAll('input[type="file"][data-file-types]').forEach((input) => {
        input.addEventListener('change', () => {
            const extensions = input.dataset.fileTypes.split(',');
            const { accepted, problems } = checkFiles(input.files, extensions, Number(input.dataset.fileMaxMb) || null);

            if (problems.length > 0) {
                // Simpan hanya file yang boleh
                const dt = new DataTransfer();
                accepted.forEach((file) => dt.items.add(file));
                input.files = dt.files;

                window.showSheet?.(problems.length > 1 ? 'Beberapa file tidak bisa dipakai' : 'File tidak bisa dipakai', problems);
            }

            renderList(input);
        });
    });
}
