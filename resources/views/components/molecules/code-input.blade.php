@props(['value' => '', 'length' => 6])

{{-- Molekul: enam kotak kode kelas --}}
<div class="flex justify-center gap-2" role="group" aria-label="Kode kelas">
    @foreach (range(0, $length - 1) as $i)
        @php($char = $value[$i] ?? '')
        <input
            type="text"
            maxlength="1"
            value="{{ $char }}"
            inputmode="text"
            autocapitalize="characters"
            aria-label="Karakter {{ $i + 1 }}"
            class="size-12 rounded-[10px] border bg-white text-center text-xl font-bold uppercase text-slate-900 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 {{ $i === strlen($value) ? 'border-2 border-blue-600' : 'border-slate-300' }}"
        >
    @endforeach
</div>
