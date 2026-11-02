<x-templates.mobile title="Hasil Slicing">
    <x-slot:header>
        <x-molecules.top-bar title="Hasil Slicing" subtitle="Pratinjau layar Figma · frontend saja" />
    </x-slot:header>

    @foreach ($groups as $group => $pages)
        <section class="space-y-2">
            <h2 class="text-base font-bold text-slate-900">{{ $group }}</h2>

            @foreach ($pages as $slug => $label)
                <a href="{{ route('slicing.show', $slug) }}" class="flex min-h-12 items-center justify-between gap-3 rounded-[10px] border border-slate-200 bg-white px-4 text-base font-semibold text-slate-900 hover:border-slate-300">
                    {{ $label }}
                    <x-atoms.icon name="chevron-right" class="size-5 text-slate-500" />
                </a>
            @endforeach
        </section>
    @endforeach
</x-templates.mobile>
