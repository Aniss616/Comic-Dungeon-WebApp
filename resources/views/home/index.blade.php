@extends('layouts.app')

@section('title', 'Home')

@section('content')

    <div class="space-y-16">

        {{-- HERO --}}
        <div class="text-center py-16 space-y-6">
            <h1 class="text-5xl md:text-7xl font-black text-yellow-400 uppercase tracking-widest leading-tight">
                Comic Dungeon
            </h1>
            <p class="text-zinc-400 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
                Your ultimate companion for exploring the comic universe. Discover characters, follow story arcs, and build your reading list.
            </p>
            <p class="text-2xl md:text-3xl font-black text-zinc-100 uppercase tracking-widest">
                ⚡ Your Journey Starts Now
            </p>
            <div class="flex items-center justify-center gap-4 pt-4">
                <a href="{{ route('explore') }}"
                   class="bg-yellow-400 hover:bg-yellow-300 text-zinc-950 font-black px-8 py-3 rounded-xl transition text-sm uppercase tracking-widest">
                    Start Exploring
                </a>
                <button id="rollCharacterBtn"
                        class="bg-zinc-800 hover:bg-zinc-700 text-zinc-100 font-black px-8 py-3 rounded-xl transition text-sm uppercase tracking-widest flex items-center gap-2">
                    <span>🎲</span>
                    <span id="rollBtnText">Roll Character</span>
                </button>
                @guest
                    <a href="{{ route('login') }}"
                       class="bg-zinc-800 hover:bg-zinc-700 text-zinc-100 font-bold px-8 py-3 rounded-xl transition text-sm uppercase tracking-widest">
                        Login
                    </a>
                @endguest
            </div>
        </div>

        {{-- RECOMMENDATION SYSTEM / LOGIN CTA --}}
        @auth
            @include('home.recommendations', ['recommendedIssues' => $recommendedIssues])
        @else
            {{-- Guest CTA --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-10 text-center space-y-4">
                <h2 class="text-2xl font-black text-yellow-400 uppercase tracking-widest">
                    Get Personalised Recommendations
                </h2>
                <p class="text-zinc-400 max-w-xl mx-auto">
                    Create an account to track what you've read, favourite your characters, and get recommendations tailored to your taste.
                </p>
                <a href="{{ route('login') }}"
                   class="inline-block bg-yellow-400 hover:bg-yellow-300 text-zinc-950 font-black px-8 py-3 rounded-xl transition text-sm uppercase tracking-widest mt-2">
                    Login to Get Started
                </a>
            </div>
        @endauth

        {{-- FEATURED CHARACTERS --}}
        @if ($featuredCharacters->count() > 0)
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-black text-zinc-100 uppercase tracking-widest">🦸 Featured Characters</h2>
                    <a href="{{ route('explore') }}?tab=characters"
                       class="text-yellow-400 text-sm hover:underline">
                        See all →
                    </a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
                    @foreach ($featuredCharacters as $character)
                        <a href="{{ route('characters.show', $character->id) }}"
                           class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden hover:border-yellow-400 transition group">
                            <div class="aspect-square overflow-hidden bg-zinc-800">
                                <img
                                    src="{{ $character->image }}"
                                    alt="{{ $character->name }}"
                                    class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-300"
                                />
                            </div>
                            <div class="p-3">
                                <p class="text-zinc-100 font-semibold text-sm truncate">{{ $character->name }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- FEATURED VOLUMES --}}
        @if ($featuredVolumes->count() > 0)
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-black text-zinc-100 uppercase tracking-widest">📚 Featured Volumes</h2>
                    <a href="{{ route('explore') }}?tab=volumes"
                       class="text-yellow-400 text-sm hover:underline">
                        See all →
                    </a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
                    @foreach ($featuredVolumes as $volume)
                        <a href="{{ route('volumes.show', $volume->id) }}"
                           class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden hover:border-yellow-400 transition group">
                            <div class="aspect-[2/3] overflow-hidden bg-zinc-800">
                                <img
                                    src="{{ $volume->cover_image }}"
                                    alt="{{ $volume->name }}"
                                    class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-300"
                                />
                            </div>
                            <div class="p-3">
                                <p class="text-zinc-100 font-semibold text-sm truncate">{{ $volume->name }}</p>
                                <p class="text-zinc-500 text-xs truncate mt-0.5">
                                    {{ $volume->publisher->name ?? 'Unknown Publisher' }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    {{-- ============================================================
         RANDOM CHARACTER MODAL
         ============================================================ --}}
    <div id="characterModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
         role="dialog" aria-modal="true" aria-labelledby="modalCharName">

        {{-- Backdrop --}}
        <div id="modalBackdrop"
             class="absolute inset-0 bg-black/70 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>

        {{-- Panel --}}
        <div id="modalPanel"
             class="relative z-10 bg-zinc-900 border border-zinc-700 rounded-2xl w-full max-w-2xl
                    shadow-2xl shadow-black/60 opacity-0 scale-95
                    transition-all duration-300 ease-out overflow-hidden">

            {{-- Loading state --}}
            <div id="modalLoading" class="flex flex-col items-center justify-center py-20 gap-4">
                <span class="text-4xl animate-spin select-none">🎲</span>
                <p class="text-zinc-400 text-sm uppercase tracking-widest font-bold">Rolling…</p>
            </div>

            {{-- Content (hidden until loaded) --}}
            <div id="modalContent" class="hidden">

                {{-- Top bar --}}
                <div class="flex items-center justify-between px-6 pt-5 pb-0">
                    <span class="text-xs font-black text-zinc-500 uppercase tracking-widest">🎲 Random Character</span>
                    <button id="modalClose"
                            class="text-zinc-500 hover:text-zinc-100 transition text-xl leading-none"
                            aria-label="Close">✕</button>
                </div>

                {{-- Character body --}}
                <div class="flex gap-6 p-6">

                    {{-- Portrait --}}
                    <div id="modalImage" class="shrink-0 w-40 rounded-xl overflow-hidden border border-zinc-700 hidden">
                        <img id="modalImg" src="" alt="" class="w-full h-full object-cover object-top">
                    </div>

                    {{-- Details --}}
                    <div class="flex-1 space-y-3 min-w-0">

                        <div>
                            <h2 id="modalCharName" class="text-xl font-black text-yellow-400 uppercase leading-tight truncate"></h2>
                            <p id="modalRealName" class="text-zinc-400 text-xs mt-0.5 hidden"></p>
                        </div>

                        {{-- Badges --}}
                        <div class="flex flex-wrap gap-2" id="modalBadges"></div>

                        {{-- Aliases --}}
                        <div id="modalAliasesWrap" class="hidden">
                            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-0.5">Aliases</p>
                            <p id="modalAliases" class="text-zinc-300 text-xs leading-snug"></p>
                        </div>

                        {{-- Powers --}}
                        <div id="modalPowersWrap" class="hidden">
                            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-1">Abilities</p>
                            <div id="modalPowers" class="flex flex-wrap gap-1.5"></div>
                        </div>

                    </div>
                </div>

                {{-- First Appearance + Best Start --}}
                <div class="grid grid-cols-2 gap-px bg-zinc-800 border-t border-zinc-800">

                    <div class="bg-zinc-900 p-4 space-y-1.5">
                        <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">First Appearance</p>
                        <div id="modalFirstAppearance">
                            <p class="text-zinc-600 text-xs">No issues linked.</p>
                        </div>
                    </div>

                    <div class="bg-zinc-900 p-4 space-y-1.5">
                        <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Best Start</p>
                        <div id="modalBestStart">
                            <p class="text-zinc-600 text-xs">No issues linked.</p>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between px-6 py-4 border-t border-zinc-800">
                    <a id="modalProfileLink" href="#"
                       class="text-yellow-400 hover:underline text-xs font-bold uppercase tracking-widest">
                        View Full Profile →
                    </a>
                    <button id="modalRollAgain"
                            class="bg-zinc-800 hover:bg-zinc-700 text-zinc-100 font-black
                                   px-5 py-2 rounded-xl transition text-xs uppercase tracking-widest">
                        🎲 Roll Again
                    </button>
                </div>

            </div>{{-- /modalContent --}}
        </div>{{-- /modalPanel --}}
    </div>{{-- /characterModal --}}

@push('scripts')
<script>
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
        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.replace('opacity-0', 'opacity-100');
            panel.classList.replace('opacity-0', 'opacity-100');
            panel.classList.replace('scale-95', 'scale-100');
        });
    }

    function closeModal() {
        backdrop.classList.replace('opacity-100', 'opacity-0');
        panel.classList.replace('opacity-100', 'opacity-0');
        panel.classList.replace('scale-100', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function showLoading() {
        loading.classList.remove('hidden');
        content.classList.add('hidden');
    }

    function populateModal(data) {
        const char = data.character;

        const imgWrap = document.getElementById('modalImage');
        const img     = document.getElementById('modalImg');
        if (char.image) {
            img.src = char.image;
            img.alt = char.name;
            imgWrap.classList.remove('hidden');
        } else {
            imgWrap.classList.add('hidden');
        }

        document.getElementById('modalCharName').textContent = char.name;

        const realNameEl = document.getElementById('modalRealName');
        if (char.real_name) {
            realNameEl.textContent = char.real_name;
            realNameEl.classList.remove('hidden');
        } else {
            realNameEl.classList.add('hidden');
        }

        const badges = document.getElementById('modalBadges');
        badges.innerHTML = '';
        [
            char.publisher ? '🏢 ' + char.publisher : null,
            char.origin    ? '🌀 ' + char.origin    : null,
            (char.gender && char.gender !== 'Unknown') ? char.gender : null,
        ].filter(Boolean).forEach(text => {
            const span = document.createElement('span');
            span.className = 'bg-zinc-800 text-zinc-300 text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full';
            span.textContent = text;
            badges.appendChild(span);
        });

        const aliasWrap = document.getElementById('modalAliasesWrap');
        const aliasEl   = document.getElementById('modalAliases');
        const aliases   = char.aliases;
        if (aliases && aliases.length) {
            aliasEl.textContent = Array.isArray(aliases) ? aliases.join(', ') : aliases;
            aliasWrap.classList.remove('hidden');
        } else {
            aliasWrap.classList.add('hidden');
        }

        const powersWrap = document.getElementById('modalPowersWrap');
        const powersEl   = document.getElementById('modalPowers');
        const powers     = char.powers;
        if (powers && powers.length) {
            powersEl.innerHTML = '';
            powers.slice(0, 8).forEach(p => {
                const label = typeof p === 'object' ? (p.name || '') : p;
                const span  = document.createElement('span');
                span.className = 'bg-yellow-400/10 text-yellow-400 border border-yellow-400/20 text-[10px] font-semibold px-2 py-0.5 rounded-full';
                span.textContent = label;
                powersEl.appendChild(span);
            });
            if (powers.length > 8) {
                const more = document.createElement('span');
                more.className = 'text-zinc-600 text-[10px] self-center';
                more.textContent = '+' + (powers.length - 8) + ' more';
                powersEl.appendChild(more);
            }
            powersWrap.classList.remove('hidden');
        } else {
            powersWrap.classList.add('hidden');
        }

        function issueCard(issue) {
            if (!issue) return '<p class="text-zinc-600 text-xs">No issues linked.</p>';
            const name  = (issue.name && issue.name !== 'TBD') ? ' — ' + issue.name : '';
            const thumb = issue.image
                ? `<img src="${issue.image}" class="w-10 rounded border border-zinc-700 shrink-0" alt="">`
                : '';
            return `
                <a href="/issues/${issue.id}" class="flex items-center gap-3 group">
                    ${thumb}
                    <div>
                        <p class="text-zinc-100 text-xs font-bold group-hover:text-yellow-400 transition leading-tight">
                            ${issue.volume_name ?? 'Unknown Volume'}
                        </p>
                        <p class="text-zinc-500 text-[10px] mt-0.5">#${issue.issue_number}${name}</p>
                    </div>
                </a>`;
        }

        document.getElementById('modalFirstAppearance').innerHTML = issueCard(data.first_appearance);
        document.getElementById('modalBestStart').innerHTML        = issueCard(data.best_start);
        document.getElementById('modalProfileLink').href           = '/characters/' + char.id;

        loading.classList.add('hidden');
        content.classList.remove('hidden');
    }

    async function rollCharacter() {
        showLoading();
        rollBtnText.textContent = 'Rolling…';
        rollBtn.disabled = true;

        try {
            const res  = await fetch(ROLL_URL, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            if (data.error) throw new Error(data.error);
            populateModal(data);
        } catch (err) {
            loading.innerHTML = `<p class="text-red-400 text-sm px-8 py-16 text-center">${err.message || 'Something went wrong.'}</p>`;
        } finally {
            rollBtnText.textContent = 'Roll Character';
            rollBtn.disabled = false;
        }
    }

    rollBtn.addEventListener('click', () => { openModal(); rollCharacter(); });
    rollAgainBtn.addEventListener('click', rollCharacter);
    closeBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });
})();
</script>
@endpush

@endsection