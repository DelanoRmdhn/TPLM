{{-- Notifikasi (semua peran) — SEMENTARA dari BE. Nanti diganti hasil slicing Delano. --}}
@extends('layouts.sementara')

@section('title', 'Notifikasi')

@section('content')

    <a href="{{ route(auth()->user()->homeRoute()) }}" class="inline-flex h-12 items-center text-brand underline">← Beranda</a>

    <section class="flex items-center justify-between gap-3 rounded-card border border-border bg-white p-4">
        <h1 class="text-h1 font-bold">Notifikasi</h1>
        @if ($unreadNotifications > 0)
            <form method="POST" action="{{ route('notifikasi.read-all') }}">
                @csrf
                <button type="submit" class="h-12 rounded-control border border-brand px-4 font-semibold text-brand">Tandai semua dibaca</button>
            </form>
        @endif
    </section>

    <section class="rounded-card border border-border bg-white p-4">
        <ul class="divide-y divide-border">
            @forelse ($notifications as $notification)
                <li>
                    <form method="POST" action="{{ route('notifikasi.read', $notification->id) }}">
                        @csrf
                        <button type="submit" class="flex min-h-12 w-full items-start gap-3 py-3 text-left">
                            <span @class(['mt-2 size-2.5 shrink-0 rounded-full', 'bg-brand' => $notification->unread(), 'bg-transparent' => $notification->read()])
                                aria-hidden="true"></span>
                            <span class="min-w-0 flex-1">
                                <span @class(['block', 'font-semibold' => $notification->unread()])>
                                    {{ $notification->data['message'] ?? 'Notifikasi' }}
                                    @if ($notification->unread()) <span class="sr-only">(belum dibaca)</span> @endif
                                </span>
                                <span class="block text-caption text-text-muted">{{ $notification->created_at->diffForHumans() }}</span>
                            </span>
                        </button>
                    </form>
                </li>
            @empty
                <li class="py-3 text-text-muted">Belum ada notifikasi.</li>
            @endforelse
        </ul>

        {{ $notifications->links() }}
    </section>

@endsection
