// Konfeti kecil untuk merayakan keberhasilan (mis. tugas terkirim). Tanpa paket tambahan.
// Tidak tampil kalau HP diatur "kurangi gerakan" (prefers-reduced-motion).

const COLORS = ['#3b5bff', '#ffc83d', '#ef4b43', '#1f9d55', '#7048f5', '#e0468f', '#0f9f8f'];
const PIECES = 70;
const DURATION = 2200;

export function celebrate() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const layer = document.createElement('div');
    layer.setAttribute('aria-hidden', 'true');
    layer.style.cssText = 'position:fixed;inset:0;pointer-events:none;overflow:hidden;z-index:60';
    document.body.append(layer);

    for (let i = 0; i < PIECES; i++) {
        const piece = document.createElement('span');
        const size = 6 + Math.random() * 8;
        const round = Math.random() > 0.5;
        piece.style.cssText = [
            'position:absolute',
            `left:${50 + (Math.random() - 0.5) * 30}%`,
            'top:38%',
            `width:${size}px`,
            `height:${round ? size : size * 0.45}px`,
            `background:${COLORS[i % COLORS.length]}`,
            `border-radius:${round ? '50%' : '2px'}`,
        ].join(';');
        layer.append(piece);

        // Meledak ke atas & samping, lalu jatuh sambil berputar
        const x = (Math.random() - 0.5) * window.innerWidth * 0.9;
        const peak = -(120 + Math.random() * 220);
        const fall = window.innerHeight * (0.45 + Math.random() * 0.4);
        const spin = (Math.random() - 0.5) * 900;

        piece.animate(
            [
                { transform: 'translate(0, 0) rotate(0deg)', opacity: 1 },
                { transform: `translate(${x * 0.6}px, ${peak}px) rotate(${spin * 0.4}deg)`, opacity: 1, offset: 0.3 },
                { transform: `translate(${x}px, ${fall}px) rotate(${spin}deg)`, opacity: 0 },
            ],
            { duration: DURATION * (0.75 + Math.random() * 0.5), easing: 'cubic-bezier(0.2, 0.7, 0.4, 1)', fill: 'forwards' },
        );
    }

    setTimeout(() => layer.remove(), DURATION * 1.4);
}

/** Jalankan konfeti kalau server menandai halaman ini (elemen [data-celebrate]). */
export function initCelebrate() {
    if (document.querySelector('[data-celebrate]')) {
        celebrate();
    }

    window.celebrate = celebrate;
}
