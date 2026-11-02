@props(['title' => null])

{{-- Molekul: lembar bawah (Kata Sandi Sementara, Konfirmasi Nonaktifkan) --}}
<div class="fixed inset-0 z-40 flex items-end bg-slate-900/50">
    <div role="dialog" aria-modal="true" {{ $attributes->merge(['class' => 'mx-auto w-full max-w-[360px] rounded-t-3xl bg-white px-4 pb-6 pt-3']) }}>
        <span class="mx-auto mb-4 block h-1.5 w-10 rounded-full bg-slate-300"></span>

        @if ($title)<h2 class="text-xl font-bold text-slate-900">{{ $title }}</h2>@endif

        {{ $slot }}
    </div>
</div>
