<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Comic Dungeon – @yield('title', 'Home')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --sl-black:      #0D0D0D;
            --sl-surface:    #141418;
            --sl-raised:     #1C1C22;
            --sl-border:     rgba(255,255,255,0.06);
            --sl-border-md:  rgba(255,255,255,0.11);
            --sl-red:        #C0392B;
            --sl-red-dim:    rgba(192,57,43,0.12);
            --sl-amber:      #D4832A;
            --sl-amber-dim:  rgba(212,131,42,0.10);
            --sl-text:       #E8E4DC;
            --sl-muted:      rgba(232,228,220,0.45);
            --sl-faint:      rgba(232,228,220,0.18);
            --sl-radius:     5px;
            --sl-radius-lg:  10px;
            --font-display:  'Barlow Condensed', sans-serif;
            --font-body:     'DM Sans', sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        body {
            background: var(--sl-black);
            color: var(--sl-text);
            font-family: var(--font-body);
            font-size: 15px;
            line-height: 1.6;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            overflow-y: auto;
        }

        #page-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
            z-index: 10;
        }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--sl-black); }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }

        /* ── NAVBAR ── */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(13,13,13,0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--sl-border);
        }

        .navbar-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
            height: 58px;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 1rem;
        }

        .navbar-logo {
            font-family: var(--font-display);
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--sl-text);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.15s;
        }

        .navbar-logo:hover { color: var(--sl-red); }

        .navbar-logo-mark {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: var(--sl-red);
            border-radius: 50%;
            flex-shrink: 0;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 0.125rem;
        }

        .navbar-nav a {
            font-family: var(--font-display);
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--sl-muted);
            text-decoration: none;
            padding: 0.35rem 0.75rem;
            border-radius: var(--sl-radius);
            transition: color 0.15s, background 0.15s;
        }

        .navbar-nav a:hover { color: var(--sl-text); background: rgba(255,255,255,0.04); }
        .navbar-nav a.active { color: var(--sl-red); }

        .navbar-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 13px;
            font-weight: 500;
            color: var(--sl-muted);
            text-decoration: none;
            padding: 0.3rem 0.75rem;
            border-radius: var(--sl-radius);
            border: 1px solid var(--sl-border);
            transition: color 0.15s, border-color 0.15s;
        }

        .navbar-user:hover { color: var(--sl-text); border-color: var(--sl-border-md); }

        .navbar-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--sl-raised);
            border: 1px solid var(--sl-border-md);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .navbar-avatar svg { width: 13px; height: 13px; color: var(--sl-muted); }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 500;
            padding: 0.4rem 1rem;
            border-radius: var(--sl-radius);
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
            white-space: nowrap;
            letter-spacing: 0.02em;
        }

        .btn-primary { background: var(--sl-red); color: #fff; border-color: var(--sl-red); }
        .btn-primary:hover { background: #d44030; border-color: #d44030; }

        .btn-ghost { background: transparent; color: var(--sl-muted); border-color: var(--sl-border); }
        .btn-ghost:hover { color: var(--sl-text); border-color: var(--sl-border-md); background: rgba(255,255,255,0.04); }

        .btn-logout {
            background: transparent; border: none;
            color: var(--sl-faint); font-size: 13px;
            cursor: pointer; padding: 0.4rem 0.5rem;
            font-family: var(--font-body); transition: color 0.15s;
        }
        .btn-logout:hover { color: var(--sl-red); }

        /* ── MAIN ── */
        .page-main {
            flex: 1;
            max-width: 1280px;
            width: 100%;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 4rem;
        }

        /* ── FLASH ── */
        .flash { padding: 0.7rem 1rem; border-radius: var(--sl-radius); font-size: 13px; margin-bottom: 1.5rem; }
        .flash-success { background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.18); color: #4ade80; }
        .flash-error   { background: var(--sl-red-dim); border: 1px solid rgba(192,57,43,0.25); color: #f87171; }

        /* ── CARDS ── */
        .card { background: var(--sl-raised); border: 1px solid var(--sl-border); border-radius: var(--sl-radius-lg); overflow: hidden; }

        .cover-card {
            background: var(--sl-raised); border: 1px solid var(--sl-border);
            border-radius: var(--sl-radius-lg); overflow: hidden;
            text-decoration: none; display: block;
            transition: border-color 0.2s, transform 0.2s;
        }
        .cover-card:hover { border-color: rgba(192,57,43,0.3); transform: translateY(-3px); }
        .cover-card-img { aspect-ratio: 2/3; background: var(--sl-surface); overflow: hidden; }
        .cover-card-img img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s; }
        .cover-card:hover .cover-card-img img { transform: scale(1.04); }
        .cover-card-placeholder {
            width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
            font-family: var(--font-display); font-size: 2.5rem; font-weight: 800; letter-spacing: 0.05em; color: var(--sl-faint);
        }
        .cover-card-body { padding: 0.75rem 1rem; border-top: 1px solid var(--sl-border); }
        .cover-card-title { font-size: 13px; font-weight: 500; color: var(--sl-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
        .cover-card-meta { font-size: 11px; color: var(--sl-muted); }

        .char-card {
            background: var(--sl-raised); border: 1px solid var(--sl-border);
            border-radius: var(--sl-radius-lg); display: flex; align-items: center;
            gap: 0.875rem; padding: 0.875rem 1.125rem; text-decoration: none;
            transition: border-color 0.2s, background 0.2s;
        }
        .char-card:hover { border-color: rgba(192,57,43,0.25); background: var(--sl-surface); }
        .char-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; flex-shrink: 0; background: var(--sl-surface); border: 1px solid var(--sl-border-md); }
        .char-avatar-placeholder {
            width: 48px; height: 48px; border-radius: 50%; flex-shrink: 0;
            background: var(--sl-red-dim); border: 1px solid rgba(192,57,43,0.2);
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-display); font-size: 1.1rem; font-weight: 800; color: var(--sl-red);
        }
        .char-info { flex: 1; min-width: 0; }
        .char-name { font-size: 14px; font-weight: 500; color: var(--sl-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .char-meta { font-size: 12px; color: var(--sl-muted); }

        /* ── BADGE ── */
        .badge {
            display: inline-block; font-size: 10px; font-weight: 600;
            font-family: var(--font-display); letter-spacing: 0.08em;
            text-transform: uppercase; padding: 0.2rem 0.5rem; border-radius: 3px;
        }
        .badge-red     { background: var(--sl-red-dim); color: var(--sl-red); border: 1px solid rgba(192,57,43,0.2); }
        .badge-amber   { background: var(--sl-amber-dim); color: var(--sl-amber); border: 1px solid rgba(212,131,42,0.2); }
        .badge-neutral { background: rgba(255,255,255,0.05); color: var(--sl-muted); border: 1px solid var(--sl-border); }

        /* ── SECTION HEADING ── */
        .section-heading { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 1.25rem; gap: 1rem; }
        .section-title { font-family: var(--font-display); font-size: 1.6rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: var(--sl-text); }
        .section-rule { flex: 1; height: 1px; background: var(--sl-border); margin: 0 0.75rem; align-self: center; }
        .section-link { font-family: var(--font-display); font-size: 0.8rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--sl-red); text-decoration: none; opacity: 0.8; transition: opacity 0.15s; flex-shrink: 0; }
        .section-link:hover { opacity: 1; }

        /* ── PAGE HEADER ── */
        .page-header { margin-bottom: 2.5rem; }
        .page-header-eyebrow { font-family: var(--font-display); font-size: 0.75rem; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; color: var(--sl-red); display: block; margin-bottom: 0.4rem; }
        .page-header-title { font-family: var(--font-display); font-size: clamp(2.5rem, 6vw, 4rem); font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; line-height: 1; color: var(--sl-text); }
        .page-header-sub { font-size: 14px; color: var(--sl-muted); margin-top: 0.75rem; max-width: 500px; }

        /* ── PILL TABS ── */
        .pill-tabs { display: flex; gap: 0.2rem; background: var(--sl-surface); border: 1px solid var(--sl-border); border-radius: var(--sl-radius); padding: 3px; width: fit-content; }
        .pill-tab { font-family: var(--font-display); font-size: 0.8rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; padding: 0.3rem 0.875rem; border-radius: 4px; border: none; background: transparent; color: var(--sl-muted); cursor: pointer; text-decoration: none; display: inline-block; transition: all 0.15s; }
        .pill-tab.active, .pill-tab:hover { background: var(--sl-raised); color: var(--sl-text); }
        .pill-tab.active { color: var(--sl-red); }

        /* ── SEARCH ── */
        .search-wrap { position: relative; }
        .search-wrap svg { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: var(--sl-faint); pointer-events: none; }
        .search-input { width: 100%; background: var(--sl-surface); border: 1px solid var(--sl-border); border-radius: var(--sl-radius); color: var(--sl-text); font-family: var(--font-body); font-size: 14px; padding: 0.55rem 1rem 0.55rem 2.4rem; outline: none; transition: border-color 0.15s; }
        .search-input::placeholder { color: var(--sl-faint); }
        .search-input:focus { border-color: rgba(192,57,43,0.4); }

        /* ── DIVIDER ── */
        .divider { height: 1px; background: var(--sl-border); margin: 2rem 0; }

        /* ── GRIDS ── */
        .grid-5 { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1.125rem; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.125rem; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.125rem; }
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.125rem; }

        @media (max-width: 1100px) { .grid-5 { grid-template-columns: repeat(4, 1fr); } .grid-4 { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px)  { .navbar-inner { padding: 0 1rem; } .page-main { padding: 1.5rem 1rem 3rem; } .grid-5, .grid-4 { grid-template-columns: repeat(2, 1fr); } .grid-3 { grid-template-columns: repeat(2, 1fr); } }

        /* ── STAT ── */
        .stat-number { font-family: var(--font-display); font-size: 2.25rem; font-weight: 800; letter-spacing: 0.04em; color: var(--sl-red); line-height: 1; }
        .stat-label  { font-family: var(--font-display); font-size: 0.7rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--sl-muted); margin-top: 0.2rem; }

        /* ── FOOTER ── */
        .site-footer { border-top: 1px solid var(--sl-border); padding: 1.5rem; text-align: center; font-family: var(--font-display); font-size: 0.75rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: var(--sl-faint); }

        /* ── UTILITIES ── */
        .text-red { color: var(--sl-red); } .text-amber { color: var(--sl-amber); } .text-muted { color: var(--sl-muted); } .text-faint { color: var(--sl-faint); }
        .flex { display: flex; } .items-center { align-items: center; } .flex-wrap { flex-wrap: wrap; }
        .gap-1 { gap: 0.5rem; } .gap-2 { gap: 1rem; }
        .mt-1 { margin-top: 0.5rem; } .mt-2 { margin-top: 1rem; } .mt-3 { margin-top: 1.5rem; } .mt-4 { margin-top: 2rem; }
        .mb-1 { margin-bottom: 0.5rem; } .mb-2 { margin-bottom: 1rem; } .mb-3 { margin-bottom: 1.5rem; }
        .w-full { width: 100%; }
    </style>
    @stack('styles')
</head>
<body>

    <div id="page-wrapper">

        <nav class="navbar">
            <div class="navbar-inner">

                <a href="{{ route('home') }}" class="navbar-logo">
                    <span class="navbar-logo-mark"></span>
                    Comic Dungeon
                </a>

                <div class="navbar-nav">
                    <a href="{{ route('home') }}"    class="{{ request()->routeIs('home')      ? 'active' : '' }}">Home</a>
                    <a href="{{ route('explore') }}" class="{{ request()->routeIs('explore')   ? 'active' : '' }}">Explore</a>
                    @auth
                        @if(Auth::user()->is_admin)
                            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                        @endif
                    @endauth
                </div>

                <div class="navbar-actions">
                    @auth
                        <a href="{{ route('profile') }}" class="navbar-user">
                            <div class="navbar-avatar">
                                <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                                </svg>
                            </div>
                            {{ Auth::user()->username }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn-logout">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"    class="btn btn-ghost">Sign in</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">Join</a>
                    @endauth
                </div>

            </div>
        </nav>

        @if(session('success') || session('error'))
            <div style="max-width:1280px; margin:0 auto; padding:1rem 1.5rem 0;">
                @if(session('success'))
                    <div class="flash flash-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="flash flash-error">{{ session('error') }}</div>
                @endif
            </div>
        @endif

        <main class="page-main">
            @yield('content')
        </main>

        <footer class="site-footer">
            Comic Dungeon &copy; {{ date('Y') }} &mdash; Powered by Comic Vine
        </footer>

    </div>

    @stack('scripts')
</body>
</html>