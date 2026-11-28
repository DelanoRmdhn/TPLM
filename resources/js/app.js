import { initCelebrate } from './confetti';
import { initFeedback } from './feedback';
import { checkFiles, describeTypes, initFileInputs } from './files';

// Dipakai skrip di halaman (mis. form kumpul tugas)
window.checkFiles = checkFiles;
window.describeTypes = describeTypes;

// Modul dimuat setelah HTML selesai di-parse, jadi elemen sudah ada
initFeedback();
initFileInputs();
initCelebrate();

// Tombol mata di kolom kata sandi (components/password-input)
document.querySelectorAll('[data-toggle-password]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.togglePassword);
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        button.setAttribute('aria-pressed', String(show));
        button.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    });
});

// PWA: daftarkan service worker (lihat public/sw.js)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}

// Tombol "Pasang Aplikasi di HP" ([data-install-app]).
// Android/Chrome: memakai prompt pasang bawaan browser.
// iPhone/browser lain: tampilkan cara memasang lewat menu browser ([data-install-help]).
const installButtons = document.querySelectorAll('[data-install-app]');
const installHelp = document.querySelector('[data-install-help]');
const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
let installPrompt = null;

if (isStandalone) {
    // Sudah dibuka sebagai aplikasi: tombol tidak perlu
    installButtons.forEach((button) => button.classList.add('hidden'));
}

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    installPrompt = event;
});

window.addEventListener('appinstalled', () => {
    installButtons.forEach((button) => button.classList.add('hidden'));
});

installButtons.forEach((button) => {
    button.addEventListener('click', async (event) => {
        event.preventDefault();

        if (installPrompt) {
            installPrompt.prompt();
            await installPrompt.userChoice;
            installPrompt = null;

            return;
        }

        installHelp?.classList.toggle('hidden');
    });
});
