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
        <div class="max-w-7xl mx-auto grid grid-cols-3 items-center">

            {{-- LEFT — LOGO --}}
            <div>
                <a href="{{ route('home') }}" class="text-yellow-400 font-black text-xl tracking-widest uppercase">
                    ⚡ Comic Dungeon
                </a>
            </div>

            {{-- CENTER — NAV LINKS --}}
            <div class="flex items-center justify-center gap-8">
                <a href="{{ route('home') }}"
                   class="text-sm font-semibold {{ request()->routeIs('home') ? 'text-yellow-400' : 'text-zinc-400 hover:text-zinc-100' }} transition">
                    Home
                </a>
                <a href="{{ route('explore') }}"
                   class="text-sm font-semibold {{ request()->routeIs('explore') ? 'text-yellow-400' : 'text-zinc-400 hover:text-zinc-100' }} transition">
                    Explore
                </a>
                @auth
                    @if(Auth::user()->is_admin)
                        <a href="{{ route('dashboard') }}"
                            class="text-sm font-semibold {{ request()->routeIs('dashboard') ? 'text-yellow-400' : 'text-zinc-400 hover:text-zinc-100' }} transition">
                            Dashboard
                        </a>
                    @endif
                @endauth
            </div>

            {{-- RIGHT — PROFILE / LOGIN --}}
            <div class="flex items-center justify-end gap-4">
                @auth
                    <a href="{{ route('profile') }}" class="flex items-center gap-2 group">
                        {{-- Avatar --}}
                        <div class="w-9 h-9 rounded-full bg-zinc-800 border border-zinc-700 group-hover:border-yellow-400 transition overflow-hidden flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-zinc-500 group-hover:text-yellow-400 transition" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                            </svg>
                        </div>
                        <span class="text-zinc-400 text-sm group-hover:text-yellow-400 transition hidden md:block">
                            {{ Auth::user()->username }}
                        </span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-zinc-600 hover:text-red-400 transition">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="flex items-center gap-2 bg-yellow-400 hover:bg-yellow-300 text-zinc-950 font-bold text-sm px-4 py-2 rounded-lg transition">
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