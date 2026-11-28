{{-- Notifikasi (semua peran). Mengetuk notifikasi = tandai dibaca lalu buka halamannya. --}}
@extends('layouts.app')

@section('title', 'Notifikasi')

@if ($unreadNotifications > 0)
    @section('header-action')
        <form method="POST" action="{{ route('notifikasi.read-all') }}">
            @csrf
            <x-button variant="ghost" size="sm" icon="checks">Tandai semua dibaca</x-button>
        </form>
    @endsection
@endif

@section('content')

    @php
        $types = [
            \App\Notifications\AssignmentPublished::class => ['clipboard-text', 'bg-brand-100 text-brand'],
            \App\Notifications\GradePublished::class => ['trophy', 'bg-sunny-50 text-warning'],
            \App\Notifications\SubmissionReceived::class => ['paper-plane-tilt', 'bg-mapel-teal-soft text-mapel-teal'],
            \App\Notifications\TeacherRegistered::class => ['user-plus', 'bg-mapel-purple-soft text-mapel-purple'],
        ];
    @endphp

    @if ($notifications->isEmpty())
        <x-card>
            <x-empty-state icon="bell" title="Belum ada notifikasi">Kabar tentang tugas dan nilai akan muncul di sini.</x-empty-state>
        </x-card>
    @else
        <ul class="space-y-2 lg:max-w-3xl">
            @foreach ($notifications as $notification)
                @php([$icon, $tone] = $types[$notification->type] ?? ['bell', 'bg-brand-50 text-brand'])
                <li>
                    <form method="POST" action="{{ route('notifikasi.read', $notification->id) }}">
                        @csrf
                        <button type="submit" @class([
                            'flex w-full items-start gap-3 rounded-card p-4 text-left transition active:scale-[0.99]',
                            'bg-surface shadow-card' => $notification->unread(),
                            'bg-surface/60' => $notification->read(),
                        ])>
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-full {{ $tone }}">
                                <x-icon :name="$icon" class="size-6" />
                            </span>
                            <span class="min-w-0 flex-1">
                                <span @class(['block', 'font-extrabold' => $notification->unread(), 'text-text-muted' => $notification->read()])>
                                    {{ $notification->data['message'] ?? 'Notifikasi' }}
                                    @if ($notification->unread()) <span class="sr-only">(belum dibaca)</span> @endif
                                </span>
                                <span class="mt-0.5 block text-caption text-text-muted">{{ $notification->created_at->diffForHumans() }}</span>
                            </span>
                            @if ($notification->unread())
                                <span class="mt-2 size-3 shrink-0 rounded-full bg-brand" aria-hidden="true"></span>
                            @endif
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>

        {{ $notifications->links() }}
    @endif

@endsection
