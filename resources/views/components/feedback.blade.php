{{--
    Semua umpan balik halaman di satu tempat (dipasang sekali di layout):
    - Toast: pesan sukses/info dari session('status'), muncul di bawah lalu hilang sendiri.
    - Sheet error: pesan validasi dari server ($errors), terbuka otomatis.
    - Sheet konfirmasi: pengganti confirm() untuk form ber-atribut data-confirm.
    - Sheet peringatan: dipakai JavaScript (window.showSheet) mis. file yang tidak didukung.
    JavaScript-nya di resources/js/feedback.js.
--}}
@props(['aboveNav' => false, 'statusToast' => true])

{{-- URL yang tidak cocok dengan route mana pun (404) tidak melewati middleware web, jadi $errors belum ada --}}
@php($errors ??= new \Illuminate\Support\ViewErrorBag)

{{-- Penanda perayaan konfeti, mis. setelah tugas berhasil diserahkan (resources/js/confetti.js) --}}
@if (session('celebrate'))
    <span data-celebrate hidden></span>
@endif

{{-- Wadah toast --}}
<div id="toast-region" aria-live="polite" aria-atomic="false"
    class="pointer-events-none fixed inset-x-0 z-50 flex flex-col items-center gap-2 px-4 {{ $aboveNav ? 'bottom-above-nav' : 'bottom-[max(16px,env(safe-area-inset-bottom))]' }}">
    {{-- Halaman bisa menampilkan status sendiri (mis. login) dengan @section('inline-status', true) --}}
    @if (session('status') && $statusToast)
        <x-toast>{{ session('status') }}</x-toast>
    @endif
</div>

{{-- Cetakan toast untuk window.showToast(pesan, tone) --}}
@foreach (['success', 'info', 'error'] as $tone)
    <template id="toast-template-{{ $tone }}">
        <x-toast :tone="$tone">&nbsp;</x-toast>
    </template>
@endforeach

{{-- Error validasi dari server --}}
@if ($errors->any())
    <x-bottom-sheet id="sheet-error" icon="warning-circle" tone="danger" :auto-open="true"
        :title="$errors->count() > 1 ? 'Ada '.$errors->count().' hal yang perlu dibenahi' : 'Ada yang perlu dibenahi'">
        <ul class="mt-1 space-y-2 text-left">
            @foreach ($errors->all() as $error)
                <li class="flex gap-2 rounded-control bg-danger-50 px-4 py-3 font-semibold text-danger">
                    <x-icon name="x-circle" variant="fill" class="mt-0.5 size-5" />
                    <span>{{ $error }}</span>
                </li>
            @endforeach
        </ul>
        <x-slot:actions>
            <x-button type="button" data-sheet-close block>Oke, aku perbaiki</x-button>
        </x-slot:actions>
    </x-bottom-sheet>
@endif

{{-- Konfirmasi (diisi JavaScript dari atribut data-confirm pada form) --}}
<x-bottom-sheet id="sheet-konfirmasi" icon="question" tone="warning" title="Yakin?">
    <p data-confirm-message></p>
    <x-slot:actions>
        <x-button type="button" data-confirm-ok block>Ya, lanjutkan</x-button>
        <x-button type="button" variant="ghost" data-sheet-close block>Batal</x-button>
    </x-slot:actions>
</x-bottom-sheet>

{{-- Peringatan dari JavaScript, mis. file yang tidak didukung --}}
<x-bottom-sheet id="sheet-peringatan" icon="warning-circle" tone="danger" title="Perhatian">
    <div data-sheet-body></div>
    <x-slot:actions>
        <x-button type="button" data-sheet-close block>Oke, mengerti</x-button>
    </x-slot:actions>
</x-bottom-sheet>
