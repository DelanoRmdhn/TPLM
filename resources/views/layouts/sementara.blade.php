{{-- Layout SEMENTARA dari BE untuk halaman yang belum di-slicing. Nanti diganti Delano. --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title') — SDN 002 Karangmulya</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-background font-sans text-body text-text antialiased">

    @auth
        <header class="border-b border-border bg-white">
            <div class="mx-auto flex max-w-3xl items-center justify-between gap-3 px-4 py-3">
                <div class="min-w-0">
                    <p class="truncate font-bold">@yield('title')</p>
                    <p class="truncate text-caption text-text-muted">{{ auth()->user()->name }} · {{ auth()->user()->role->label() }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="h-12 rounded-control border border-border px-4 font-semibold">Keluar</button>
                </form>
            </div>
        </header>
    @endauth

    <main class="mx-auto max-w-3xl space-y-4 px-4 py-5">

        @if (session('status'))
            <div role="status" class="rounded-control border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-control border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>
