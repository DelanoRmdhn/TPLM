// Atom "kotak centang" dengan data-toggle-password menampilkan / menyembunyikan kolom kata sandi.
document.addEventListener('change', (event) => {
    const toggle = event.target.closest('[data-toggle-password]');

    if (!toggle) {
        return;
    }

    const field = document.querySelector(toggle.dataset.togglePassword);

    if (field) {
        field.type = toggle.checked ? 'text' : 'password';
    }
});
