{{--
    Blok berwarna mapel selebar layar di bagian atas halaman detail (kelas, tugas, materi).
    Dipakai di @section('hero') bersama @section('topbar', 'overlay'). Isi halaman menumpuk di bawahnya.
--}}
@props(['subject'])

<section {{ $attributes->class(['relative overflow-hidden pt-[calc(env(safe-area-inset-top,0px)+64px)] pb-14', $subject->solidClasses()]) }}>
    <span class="pointer-events-none absolute -top-10 -left-10 size-40 rounded-full bg-white/10" aria-hidden="true"></span>
    <span class="pointer-events-none absolute right-6 bottom-6 size-16 rounded-full bg-white/10" aria-hidden="true"></span>
    <span class="pointer-events-none absolute -right-6 top-10 opacity-20" aria-hidden="true">
        <x-icon :name="$subject->iconName()" variant="fill" class="size-40" />
    </span>

    <div class="relative mx-auto max-w-3xl px-safe">
        {{ $slot }}
    </div>
</section>
