// Toast, bottom sheet, dan konfirmasi. Markup-nya di resources/views/components/feedback.blade.php.

const TOAST_DURATION = 4500;

function dismissToast(toast) {
    toast.style.transition = 'opacity 200ms, transform 200ms';
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(8px)';
    setTimeout(() => toast.remove(), 220);
}

function armToast(toast) {
    toast.querySelector('[data-toast-close]')?.addEventListener('click', () => dismissToast(toast));
    setTimeout(() => dismissToast(toast), TOAST_DURATION);
}

/** Tampilkan toast. tone: success | info | error */
export function showToast(message, tone = 'success') {
    const region = document.getElementById('toast-region');
    const template = document.getElementById(`toast-template-${tone}`) ?? document.getElementById('toast-template-success');
    if (!region || !template) {
        return;
    }

    const toast = template.content.firstElementChild.cloneNode(true);
    toast.querySelector('[data-toast-text]').textContent = message;
    region.append(toast);
    armToast(toast);
}

/** Buka bottom sheet (elemen <dialog data-sheet>). */
export function openSheet(sheet) {
    if (!sheet) {
        return;
    }
    if (sheet.open) {
        sheet.close();
    }
    sheet.showModal();
}

/**
 * Sheet peringatan dengan judul & isi bebas, mis. daftar file yang ditolak.
 * body: string atau array string (jadi daftar).
 */
export function showSheet(title, body) {
    const sheet = document.getElementById('sheet-peringatan');
    if (!sheet) {
        return;
    }

    sheet.querySelector('[data-sheet-title]').textContent = title;
    const container = sheet.querySelector('[data-sheet-body]');
    container.replaceChildren();

    for (const line of Array.isArray(body) ? body : [body]) {
        const p = document.createElement('p');
        p.className = 'mt-2 rounded-[14px] bg-danger-50 px-4 py-3 text-left font-semibold text-danger';
        p.textContent = line;
        container.append(p);
    }

    openSheet(sheet);
}

function initSheets() {
    document.querySelectorAll('dialog[data-sheet]').forEach((sheet) => {
        // Ketuk area gelap di luar sheet = tutup
        sheet.addEventListener('click', (event) => {
            if (event.target === sheet) {
                sheet.close();
            }
        });
        sheet.querySelectorAll('[data-sheet-close]').forEach((button) => button.addEventListener('click', () => sheet.close()));

        // Dari server (mis. error validasi): ubah jadi modal supaya fokus masuk ke sheet
        if (sheet.hasAttribute('data-auto-open')) {
            openSheet(sheet);
        }
    });

    document.querySelectorAll('[data-sheet-open]').forEach((button) => {
        button.addEventListener('click', () => openSheet(document.getElementById(button.dataset.sheetOpen)));
    });
}

/**
 * Form dengan data-confirm="Pesan" minta konfirmasi lewat bottom sheet.
 * Opsional: data-confirm-title, data-confirm-label (teks tombol), data-confirm-tone="danger".
 */
function initConfirm() {
    const sheet = document.getElementById('sheet-konfirmasi');
    if (!sheet) {
        return;
    }

    const okButton = sheet.querySelector('[data-confirm-ok]');
    let pending = null;

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.dataset.confirm || form.dataset.confirmed === '1') {
            return;
        }

        event.preventDefault();
        pending = { form, submitter: event.submitter };

        const danger = form.dataset.confirmTone === 'danger';
        sheet.querySelector('[data-sheet-title]').textContent = form.dataset.confirmTitle ?? 'Yakin?';
        sheet.querySelector('[data-confirm-message]').textContent = form.dataset.confirm;
        okButton.textContent = form.dataset.confirmLabel ?? 'Ya, lanjutkan';
        okButton.classList.toggle('bg-danger', danger);
        okButton.classList.toggle('shadow-[0_3px_0_#b91c1c]', danger);
        okButton.classList.toggle('bg-brand', !danger);
        okButton.classList.toggle('shadow-button', !danger);

        openSheet(sheet);
    });

    okButton.addEventListener('click', () => {
        if (!pending) {
            return;
        }
        const { form, submitter } = pending;
        pending = null;
        sheet.close();
        form.dataset.confirmed = '1';
        form.requestSubmit(submitter && form.contains(submitter) ? submitter : undefined);
    });
}

export function initFeedback() {
    document.querySelectorAll('#toast-region [data-toast]').forEach(armToast);
    initSheets();
    initConfirm();

    window.showToast = showToast;
    window.showSheet = showSheet;
}
