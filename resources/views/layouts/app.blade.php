<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Comic Dungeon – @yield('title', 'Home')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex flex-col">

    {{-- NAVBAR --}}
    <nav class="bg-zinc-900 border-b border-zinc-800 px-6 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">

            {{-- LOGO --}}
            <a href="{{ route('characters.index') }}" class="text-yellow-400 font-black text-xl tracking-widest uppercase">
                ⚡ Comic Dungeon
            </a>

            {{-- NAV LINKS --}}
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('characters.index') }}"
                   class="text-sm {{ request()->routeIs('characters*') ? 'text-yellow-400 font-semibold' : 'text-zinc-400 hover:text-zinc-100' }} transition">
                    Characters
                </a>
                <a href="{{ route('volumes.index') }}"
                   class="text-sm {{ request()->routeIs('volumes*') ? 'text-yellow-400 font-semibold' : 'text-zinc-400 hover:text-zinc-100' }} transition">
                    Volumes
                </a>
                <a href="{{ route('search') }}"
                   class="text-sm {{ request()->routeIs('search*') ? 'text-yellow-400 font-semibold' : 'text-zinc-400 hover:text-zinc-100' }} transition">
                    Search
                </a>
                @auth
                <a href="{{ route('dashboard') }}"
                   class="text-sm {{ request()->routeIs('dashboard*') ? 'text-yellow-400 font-semibold' : 'text-zinc-400 hover:text-zinc-100' }} transition">
                    Dashboard
                </a>
                @endauth
            </div>

            {{-- AUTH --}}
            <div class="flex items-center gap-4">
                @auth
                    <span class="text-zinc-500 text-sm hidden md:block">
                        {{ Auth::user()->username }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-zinc-400 hover:text-red-400 transition">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-zinc-400 hover:text-yellow-400 transition">
                        Login
                    </a>
                @endauth
            </div>

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