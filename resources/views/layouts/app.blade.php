<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Comic Dungeon – @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex flex-col">

    {{-- NAVBAR --}}
    <nav class="bg-zinc-900 border-b border-zinc-800 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-yellow-400 font-black text-xl tracking-widest uppercase">⚡ Comic Dungeon</span>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-zinc-400 text-sm">{{ Auth::user()->name ?? Auth::user()->username }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-sm text-zinc-400 hover:text-red-400 transition">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    {{-- MAIN --}}
    <main class="flex-1 p-6 max-w-7xl mx-auto w-full">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="text-center text-zinc-700 text-xs py-4 border-t border-zinc-800">
        Comic Dungeon &copy; {{ date('Y') }} — Powered by Comic Vine
    </footer>
@stack('scripts')
</body>
</html>