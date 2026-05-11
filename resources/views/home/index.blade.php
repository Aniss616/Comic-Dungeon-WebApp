@extends('layouts.app')

@section('title', 'Home')

@push('styles')
<style>
    /* ── HERO SCENE ── */
    .hero-scene {
        position: relative;
        width: 100%;
        height: 520px;
        overflow: hidden;
        margin-bottom: 4rem;
    }

    /* City canvas fills the hero */
    #city-canvas {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
    }

    /* Rain sits on top of city, below content */
    #rain-canvas {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
        pointer-events: none;
        opacity: 0.35;
    }

    /* Grain scoped to hero only — oversized to avoid edge flicker */
    .hero-grain {
        position: absolute;
        top: -10%;
        left: -10%;
        width: 120%;
        height: 120%;
        z-index: 2;
        pointer-events: none;
        opacity: 0.045;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
        background-repeat: repeat;
        background-size: 128px 128px;
        animation: grain-shift 0.12s steps(1) infinite;
        will-change: transform;
    }

    @keyframes grain-shift {
        0%   { transform: translate(0,    0   ); }
        10%  { transform: translate(-2%,  -3% ); }
        20%  { transform: translate(3%,   1%  ); }
        30%  { transform: translate(-1%,  4%  ); }
        40%  { transform: translate(4%,   -2% ); }
        50%  { transform: translate(-3%,  3%  ); }
        60%  { transform: translate(2%,   -4% ); }
        70%  { transform: translate(-4%,  1%  ); }
        80%  { transform: translate(3%,   3%  ); }
        90%  { transform: translate(-2%,  -1% ); }
        100% { transform: translate(1%,   -3% ); }
    }

    /* Fade bottom of hero into page */
    .hero-fade {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 180px;
        background: linear-gradient(to bottom, transparent, #0D0D0D);
        z-index: 3;
        pointer-events: none;
    }

    /* Hero content floats above everything */
    .hero-content {
        position: absolute;
        inset: 0;
        z-index: 4;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 2rem 1.5rem;
    }

    .hero-eyebrow {
        font-family: var(--font-display);
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--sl-red);
        margin-bottom: 1rem;
        opacity: 0;
        animation: fade-up 0.6s ease forwards 0.2s;
    }

    .hero-title {
        font-family: var(--font-display);
        font-size: clamp(3.5rem, 10vw, 7rem);
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        line-height: 0.95;
        color: var(--sl-text);
        text-shadow: 0 2px 40px rgba(0,0,0,0.8);
        margin-bottom: 1.25rem;
        opacity: 0;
        animation: fade-up 0.6s ease forwards 0.35s;
    }

    .hero-sub {
        font-size: 15px;
        color: var(--sl-muted);
        max-width: 440px;
        line-height: 1.7;
        margin-bottom: 2rem;
        text-shadow: 0 1px 12px rgba(0,0,0,0.9);
        opacity: 0;
        animation: fade-up 0.6s ease forwards 0.5s;
    }

    .hero-cta {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        flex-wrap: wrap;
        justify-content: center;
        opacity: 0;
        animation: fade-up 0.6s ease forwards 0.65s;
    }

    @keyframes fade-up {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── REST OF PAGE ── */
    .home-sections {
        display: flex;
        flex-direction: column;
        gap: 4rem;
    }
</style>
@endpush

@section('content')

    {{-- HERO SCENE --}}
    <div class="hero-scene">
        <canvas id="city-canvas"></canvas>
        <canvas id="rain-canvas"></canvas>
        <div class="hero-grain" aria-hidden="true"></div>
        <div class="hero-fade"></div>

        <div class="hero-content">
            <p class="hero-eyebrow">Your comic universe</p>
            <h1 class="hero-title">Comic<br>Dungeon</h1>
            <p class="hero-sub">
                Explore thousands of characters, follow story arcs, and build your reading list.
            </p>
            <div class="hero-cta">
                <a href="{{ route('explore') }}" class="btn btn-primary" style="font-family:var(--font-display); font-weight:700; letter-spacing:0.08em; text-transform:uppercase; font-size:13px; padding:0.55rem 1.5rem;">
                    Start Exploring
                </a>
                <button id="rollCharacterBtn" class="btn btn-ghost" style="font-family:var(--font-display); font-weight:700; letter-spacing:0.08em; text-transform:uppercase; font-size:13px;">
                    <span id="rollBtnText">Roll Character</span>
                </button>
                @guest
                    <a href="{{ route('login') }}" class="btn btn-ghost" style="font-family:var(--font-display); font-weight:700; letter-spacing:0.08em; text-transform:uppercase; font-size:13px;">
                        Sign In
                    </a>
                @endguest
            </div>
        </div>
    </div>

    {{-- BELOW HERO --}}
    <div class="home-sections">

        {{-- RECOMMENDATIONS --}}
        @auth
            @include('home.recommendations', ['recommendedIssues' => $recommendedIssues])
        @else
            <div style="background:var(--sl-raised); border:1px solid var(--sl-border); border-radius:var(--sl-radius-lg); padding:2.5rem; text-align:center; position:relative; overflow:hidden;">
                <div style="position:absolute; top:0; left:0; right:0; height:2px; background:linear-gradient(90deg, var(--sl-red), transparent);"></div>
                <h2 style="font-family:var(--font-display); font-size:1.5rem; font-weight:800; letter-spacing:0.08em; text-transform:uppercase; color:var(--sl-text); margin-bottom:0.75rem;">Get Personalised Recommendations</h2>
                <p style="color:var(--sl-muted); font-size:14px; max-width:460px; margin:0 auto 1.5rem; line-height:1.65;">
                    Create an account to track what you've read, favourite your characters, and get recommendations tailored to your taste.
                </p>
                <a href="{{ route('login') }}" class="btn btn-primary" style="font-family:var(--font-display); font-weight:700; letter-spacing:0.08em; text-transform:uppercase;">
                    Sign In to Get Started
                </a>
            </div>
        @endauth

        {{-- FEATURED CHARACTERS --}}
        @if ($featuredCharacters->count() > 0)
            <div>
                <div class="section-heading">
                    <h2 class="section-title">Characters</h2>
                    <div class="section-rule"></div>
                    <a href="{{ route('explore') }}?tab=characters" class="section-link">See all</a>
                </div>
                <div style="display:grid; grid-template-columns:repeat(6,1fr); gap:1.125rem;">
                    @foreach ($featuredCharacters as $character)
                        <a href="{{ route('characters.show', $character->id) }}" style="background:var(--sl-raised); border:1px solid var(--sl-border); border-radius:var(--sl-radius-lg); overflow:hidden; text-decoration:none; display:block; transition:border-color 0.2s, transform 0.2s;"
                           onmouseover="this.style.borderColor='rgba(192,57,43,0.3)';this.style.transform='translateY(-3px)'"
                           onmouseout="this.style.borderColor='rgba(255,255,255,0.06)';this.style.transform='translateY(0)'">
                            <div style="aspect-ratio:1/1; overflow:hidden; background:var(--sl-surface);">
                                <img src="{{ $character->image }}" alt="{{ $character->name }}"
                                     style="width:100%; height:100%; object-fit:cover; object-position:top; display:block; transition:transform 0.3s;"
                                     onmouseover="this.style.transform='scale(1.05)'"
                                     onmouseout="this.style.transform='scale(1)'"
                                     loading="lazy">
                            </div>
                            <div style="padding:0.625rem 0.75rem; border-top:1px solid var(--sl-border);">
                                <p style="font-size:12px; font-weight:500; color:var(--sl-text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $character->name }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- FEATURED VOLUMES --}}
        @if ($featuredVolumes->count() > 0)
            <div>
                <div class="section-heading">
                    <h2 class="section-title">Volumes</h2>
                    <div class="section-rule"></div>
                    <a href="{{ route('explore') }}?tab=volumes" class="section-link">See all</a>
                </div>
                <div style="display:grid; grid-template-columns:repeat(6,1fr); gap:1.125rem;">
                    @foreach ($featuredVolumes as $volume)
                        <a href="{{ route('volumes.show', $volume->id) }}" class="cover-card">
                            <div class="cover-card-img">
                                @if($volume->cover_image)
                                    <img src="{{ $volume->cover_image }}" alt="{{ $volume->name }}" loading="lazy">
                                @else
                                    <div class="cover-card-placeholder">{{ strtoupper(substr($volume->name,0,2)) }}</div>
                                @endif
                            </div>
                            <div class="cover-card-body">
                                <div class="cover-card-title">{{ $volume->name }}</div>
                                <div class="cover-card-meta">{{ $volume->publisher->name ?? 'Unknown Publisher' }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    {{-- RANDOM CHARACTER MODAL --}}
    <div id="characterModal" style="display:none; position:fixed; inset:0; z-index:200; align-items:center; justify-content:center; padding:1rem;" role="dialog" aria-modal="true" aria-labelledby="modalCharName">
        <div id="modalBackdrop" style="position:absolute; inset:0; background:rgba(0,0,0,0.75); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px); opacity:0; transition:opacity 0.25s;"></div>
        <div id="modalPanel" style="position:relative; z-index:10; background:var(--sl-surface); border:1px solid var(--sl-border-md); border-radius:var(--sl-radius-lg); width:100%; max-width:640px; box-shadow:0 24px 60px rgba(0,0,0,0.7); overflow:hidden; opacity:0; transform:scale(0.96) translateY(8px); transition:opacity 0.25s, transform 0.25s;">
            <div style="height:2px; background:linear-gradient(90deg, var(--sl-red), transparent);"></div>

            <div id="modalLoading" style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:5rem 2rem; gap:1rem;">
                <div style="width:32px; height:32px; border:2px solid var(--sl-border-md); border-top-color:var(--sl-red); border-radius:50%; animation:spin 0.7s linear infinite;"></div>
                <p style="font-family:var(--font-display); font-size:0.8rem; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:var(--sl-muted);">Rolling...</p>
            </div>

            <div id="modalContent" style="display:none;">
                <div style="display:flex; align-items:center; justify-content:space-between; padding:1.125rem 1.375rem 0;">
                    <span style="font-family:var(--font-display); font-size:0.7rem; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:var(--sl-muted);">Random Character</span>
                    <button id="modalClose" aria-label="Close" style="background:none; border:none; cursor:pointer; color:var(--sl-muted); font-size:1rem; line-height:1; padding:0.25rem; border-radius:3px; transition:color 0.15s;" onmouseover="this.style.color='var(--sl-text)'" onmouseout="this.style.color='var(--sl-muted)'">&times;</button>
                </div>

                <div style="display:flex; gap:1.25rem; padding:1.125rem 1.375rem;">
                    <div id="modalImage" style="display:none; flex-shrink:0; width:130px; border-radius:var(--sl-radius); overflow:hidden; border:1px solid var(--sl-border-md); align-self:flex-start;">
                        <img id="modalImg" src="" alt="" style="width:100%; display:block; object-fit:cover; object-position:top;">
                    </div>
                    <div style="flex:1; min-width:0; display:flex; flex-direction:column; gap:0.875rem;">
                        <div>
                            <h2 id="modalCharName" style="font-family:var(--font-display); font-size:1.5rem; font-weight:800; letter-spacing:0.06em; text-transform:uppercase; color:var(--sl-text); line-height:1; margin-bottom:0.25rem;"></h2>
                            <p id="modalRealName" style="display:none; font-size:12px; color:var(--sl-muted);"></p>
                        </div>
                        <div id="modalBadges" style="display:flex; flex-wrap:wrap; gap:0.4rem;"></div>
                        <div id="modalAliasesWrap" style="display:none;">
                            <p style="font-family:var(--font-display); font-size:0.65rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--sl-faint); margin-bottom:0.25rem;">Aliases</p>
                            <p id="modalAliases" style="font-size:12px; color:var(--sl-muted); line-height:1.5;"></p>
                        </div>
                        <div id="modalPowersWrap" style="display:none;">
                            <p style="font-family:var(--font-display); font-size:0.65rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--sl-faint); margin-bottom:0.4rem;">Abilities</p>
                            <div id="modalPowers" style="display:flex; flex-wrap:wrap; gap:0.35rem;"></div>
                        </div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; border-top:1px solid var(--sl-border);">
                    <div style="padding:1rem 1.375rem; border-right:1px solid var(--sl-border);">
                        <p style="font-family:var(--font-display); font-size:0.65rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--sl-faint); margin-bottom:0.625rem;">First Appearance</p>
                        <div id="modalFirstAppearance"><p style="font-size:12px; color:var(--sl-faint);">No issues linked.</p></div>
                    </div>
                    <div style="padding:1rem 1.375rem;">
                        <p style="font-family:var(--font-display); font-size:0.65rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--sl-faint); margin-bottom:0.625rem;">Best Start</p>
                        <div id="modalBestStart"><p style="font-size:12px; color:var(--sl-faint);">No issues linked.</p></div>
                    </div>
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between; padding:0.875rem 1.375rem; border-top:1px solid var(--sl-border);">
                    <a id="modalProfileLink" href="#" style="font-family:var(--font-display); font-size:0.75rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:var(--sl-red); text-decoration:none; opacity:0.85; transition:opacity 0.15s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.85'">View Full Profile</a>
                    <button id="modalRollAgain" class="btn btn-ghost" style="font-family:var(--font-display); font-weight:700; letter-spacing:0.07em; text-transform:uppercase; font-size:0.75rem;">Roll Again</button>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<style>
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
<script type="module">
    import { initRain } from '{{ Vite::asset('resources/js/rain.js') }}';

    // ── CITY BACKGROUND ──────────────────────────────────────────
    (function initCity() {
        const canvas = document.getElementById('city-canvas');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        let W = 0;
        let H = 0;

        let buildings = [];
        let windows   = [];

        function buildCity() {
            buildings = [];
            windows   = [];

            const buildingCount = Math.floor(W / 38);
            let x = 0;

            for (let i = 0; i < buildingCount; i++) {
                const w = 28 + Math.random() * 55;
                const h = 60 + Math.random() * (H * 0.72);
                const y = H - h;

                buildings.push({ x, y, w, h });

                if (Math.random() > 0.6) {
                    const tw = 6 + Math.random() * 12;
                    const th = 20 + Math.random() * 60;
                    buildings.push({ x: x + w / 2 - tw / 2, y: y - th, w: tw, h: th });
                }

                const cols = Math.floor(w / 10);
                const rows = Math.floor(h / 14);
                for (let col = 0; col < cols; col++) {
                    for (let row = 0; row < rows; row++) {
                        if (Math.random() < 0.35) {
                            windows.push({
                                x: x + 4 + col * 10,
                                y: y + 6 + row * 14,
                                w: 5,
                                h: 7,
                                lit:          Math.random() > 0.4,
                                flickerRate:  0.002 + Math.random() * 0.006,
                                flickerOffset: Math.random() * Math.PI * 2,
                                warm:         Math.random() > 0.5,
                            });
                        }
                    }
                }

                x += w + 2 + Math.random() * 6;
                if (x > W) break;
            }
        }

        function draw(t) {
            if (!W || !H || !isFinite(W) || !isFinite(H)) return;

            ctx.clearRect(0, 0, W, H);

            // Sky
            const sky = ctx.createLinearGradient(0, 0, 0, H);
            sky.addColorStop(0,   '#05050A');
            sky.addColorStop(0.5, '#0A0A12');
            sky.addColorStop(1,   '#111118');
            ctx.fillStyle = sky;
            ctx.fillRect(0, 0, W, H);

            // Horizon glow
            const glow = ctx.createRadialGradient(W * 0.5, H * 0.85, 0, W * 0.5, H * 0.85, W * 0.6);
            glow.addColorStop(0,   'rgba(160,40,20,0.18)');
            glow.addColorStop(0.5, 'rgba(100,20,10,0.06)');
            glow.addColorStop(1,   'transparent');
            ctx.fillStyle = glow;
            ctx.fillRect(0, 0, W, H);

            // Fog layers
            ctx.save();
            ctx.globalAlpha = 0.06;
            for (let f = 0; f < 3; f++) {
                const fg = ctx.createLinearGradient(0, H * 0.5, 0, H);
                fg.addColorStop(0, 'transparent');
                fg.addColorStop(1, 'rgba(180,190,210,0.8)');
                ctx.fillStyle = fg;
                const shift = Math.sin(t * 0.0002 + f * 2.1) * 40;
                ctx.fillRect(shift, H * 0.5, W, H * 0.5);
            }
            ctx.restore();

            // Building silhouettes
            ctx.fillStyle = '#080810';
            buildings.forEach(b => ctx.fillRect(b.x, b.y, b.w, b.h));

            // Building rim light
            ctx.strokeStyle = 'rgba(100,120,160,0.08)';
            ctx.lineWidth = 1;
            buildings.forEach(b => ctx.strokeRect(b.x, b.y, b.w, b.h));

            // Windows
            windows.forEach(win => {
                if (!win.lit) return;
                const flicker = Math.sin(t * win.flickerRate + win.flickerOffset);
                if (flicker < -0.92) return;
                const alpha = 0.45 + flicker * 0.2;

                ctx.globalAlpha = Math.max(0.1, alpha);
                ctx.fillStyle   = win.warm ? '#D4832A' : '#9AB4CC';
                ctx.fillRect(win.x, win.y, win.w, win.h);

                ctx.globalAlpha = alpha * 0.15;
                ctx.fillStyle   = win.warm ? '#D4832A' : '#7A99BB';
                ctx.fillRect(win.x - 3, win.y - 3, win.w + 6, win.h + 6);

                ctx.globalAlpha = 1;
            });

            // Random window flicker
            if (Math.random() < 0.008) {
                const w = windows[Math.floor(Math.random() * windows.length)];
                if (w) w.lit = !w.lit;
            }

            // Ground reflection
            const ref = ctx.createLinearGradient(0, H * 0.88, 0, H);
            ref.addColorStop(0, 'rgba(160,40,20,0.08)');
            ref.addColorStop(1, 'rgba(0,0,0,0)');
            ctx.fillStyle = ref;
            ctx.fillRect(0, H * 0.88, W, H * 0.12);
        }

        let animId;
        function loop(t) {
            animId = requestAnimationFrame(loop);
            draw(t);
        }

        function resize() {
            W = canvas.width  = canvas.offsetWidth;
            H = canvas.height = canvas.offsetHeight;
            if (W > 0 && H > 0) buildCity();
        }

        // Resize first so W/H are valid before loop starts
        window.addEventListener('resize', resize);
        resize();

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) cancelAnimationFrame(animId);
            else loop(performance.now());
        });

        // Start loop only after resize has set valid dimensions
        requestAnimationFrame(loop);
    })();

    // ── RAIN (home page only) ─────────────────────────────────────
    initRain();

    // ── RANDOM CHARACTER MODAL ────────────────────────────────────
    (function () {
        const ROLL_URL = "{{ route('random.character') }}";

        const modal        = document.getElementById('characterModal');
        const backdrop     = document.getElementById('modalBackdrop');
        const panel        = document.getElementById('modalPanel');
        const loading      = document.getElementById('modalLoading');
        const content      = document.getElementById('modalContent');
        const rollBtn      = document.getElementById('rollCharacterBtn');
        const rollBtnText  = document.getElementById('rollBtnText');
        const closeBtn     = document.getElementById('modalClose');
        const rollAgainBtn = document.getElementById('modalRollAgain');

        function openModal() {
            modal.style.display = 'flex';
            requestAnimationFrame(() => {
                backdrop.style.opacity = '1';
                panel.style.opacity    = '1';
                panel.style.transform  = 'scale(1) translateY(0)';
            });
        }

        function closeModal() {
            backdrop.style.opacity = '0';
            panel.style.opacity    = '0';
            panel.style.transform  = 'scale(0.96) translateY(8px)';
            setTimeout(() => modal.style.display = 'none', 250);
        }

        function showLoading() {
            loading.style.display = 'flex';
            content.style.display = 'none';
        }

        function populateModal(data) {
            const char = data.character;

            const imgWrap = document.getElementById('modalImage');
            const img     = document.getElementById('modalImg');
            if (char.image) { img.src = char.image; img.alt = char.name; imgWrap.style.display = 'block'; }
            else imgWrap.style.display = 'none';

            document.getElementById('modalCharName').textContent = char.name;

            const realNameEl = document.getElementById('modalRealName');
            if (char.real_name) { realNameEl.textContent = char.real_name; realNameEl.style.display = 'block'; }
            else realNameEl.style.display = 'none';

            const badges = document.getElementById('modalBadges');
            badges.innerHTML = '';
            [char.publisher || null, char.origin || null, (char.gender && char.gender !== 'Unknown') ? char.gender : null]
                .filter(Boolean).forEach(text => {
                    const span = document.createElement('span');
                    span.className = 'badge badge-neutral';
                    span.textContent = text;
                    badges.appendChild(span);
                });

            const aliasWrap = document.getElementById('modalAliasesWrap');
            const aliasEl   = document.getElementById('modalAliases');
            const aliases   = char.aliases;
            if (aliases && aliases.length) {
                aliasEl.textContent      = Array.isArray(aliases) ? aliases.join(', ') : aliases;
                aliasWrap.style.display  = 'block';
            } else aliasWrap.style.display = 'none';

            const powersWrap = document.getElementById('modalPowersWrap');
            const powersEl   = document.getElementById('modalPowers');
            const powers     = char.powers;
            if (powers && powers.length) {
                powersEl.innerHTML = '';
                powers.slice(0, 8).forEach(p => {
                    const label = typeof p === 'object' ? (p.name || '') : p;
                    const span  = document.createElement('span');
                    span.className   = 'badge badge-amber';
                    span.textContent = label;
                    powersEl.appendChild(span);
                });
                if (powers.length > 8) {
                    const more = document.createElement('span');
                    more.style.cssText = 'font-size:11px; color:var(--sl-faint); align-self:center;';
                    more.textContent   = '+' + (powers.length - 8) + ' more';
                    powersEl.appendChild(more);
                }
                powersWrap.style.display = 'block';
            } else powersWrap.style.display = 'none';

            function issueCard(issue) {
                if (!issue) return '<p style="font-size:12px; color:var(--sl-faint);">No issues linked.</p>';
                const name  = (issue.name && issue.name !== 'TBD') ? ' &mdash; ' + issue.name : '';
                const thumb = issue.image
                    ? `<img src="${issue.image}" style="width:36px; height:50px; object-fit:cover; border-radius:3px; border:1px solid var(--sl-border-md); flex-shrink:0;" alt="">`
                    : '';
                return `
                    <a href="/issues/${issue.id}" style="display:flex; align-items:center; gap:0.625rem; text-decoration:none;">
                        ${thumb}
                        <div>
                            <p style="font-size:12px; font-weight:500; color:var(--sl-text); line-height:1.3; transition:color 0.15s;"
                               onmouseover="this.style.color='var(--sl-red)'"
                               onmouseout="this.style.color='var(--sl-text)'">
                                ${issue.volume_name ?? 'Unknown Volume'}
                            </p>
                            <p style="font-size:11px; color:var(--sl-muted); margin-top:1px;">#${issue.issue_number}${name}</p>
                        </div>
                    </a>`;
            }

            document.getElementById('modalFirstAppearance').innerHTML = issueCard(data.first_appearance);
            document.getElementById('modalBestStart').innerHTML        = issueCard(data.best_start);
            document.getElementById('modalProfileLink').href           = '/characters/' + char.id;

            loading.style.display = 'none';
            content.style.display = 'block';
        }

        async function rollCharacter() {
            showLoading();
            rollBtnText.textContent = 'Rolling...';
            rollBtn.disabled = true;
            try {
                const res  = await fetch(ROLL_URL, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                if (data.error) throw new Error(data.error);
                populateModal(data);
            } catch (err) {
                loading.innerHTML = `<p style="font-size:13px; color:var(--sl-red); padding:4rem 2rem; text-align:center;">${err.message || 'Something went wrong.'}</p>`;
            } finally {
                rollBtnText.textContent = 'Roll Character';
                rollBtn.disabled = false;
            }
        }

        rollBtn.addEventListener('click',     () => { openModal(); rollCharacter(); });
        rollAgainBtn.addEventListener('click',  rollCharacter);
        closeBtn.addEventListener('click',      closeModal);
        backdrop.addEventListener('click',      closeModal);
        document.addEventListener('keydown',    e => {
            if (e.key === 'Escape' && modal.style.display !== 'none') closeModal();
        });
    })();
</script>
@endpush

@endsection