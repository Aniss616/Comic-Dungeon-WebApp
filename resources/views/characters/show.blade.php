@extends('layouts.app')

@section('title', $character->name)

@section('content')

<div class="space-y-8">

    {{-- BACK --}}
    <a href="{{ route('explore') }}?tab=characters" class="text-zinc-500 hover:text-yellow-400 text-sm transition">
        ← Back to Characters
    </a>

    {{-- HERO --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 flex flex-col md:flex-row gap-8">

        {{-- IMAGE --}}
        <div class="flex-shrink-0 flex flex-col items-center gap-4">
            @if ($character->image)
                <img
                    src="{{ $character->image }}"
                    alt="{{ $character->name }}"
                    class="w-56 h-56 object-cover object-top rounded-xl border border-zinc-700"
                />
            @else
                <div class="w-56 h-56 bg-zinc-800 rounded-xl flex items-center justify-center text-zinc-600 text-5xl">?</div>
            @endif

            {{-- USER ACTIONS --}}
            @auth
                @php
                    $isFavourite = Auth::user()->favouriteCharacters()->where('character_id', $character->id)->exists();
                @endphp
                <button
                    id="charFavBtn"
                    onclick="toggleFavouriteCharacter({{ $character->id }})"
                    class="w-full text-sm px-4 py-2 rounded-lg transition font-semibold
                        {{ $isFavourite ? 'bg-red-500 text-white' : 'bg-zinc-800 hover:bg-zinc-700 text-zinc-300' }}">
                    {{ $isFavourite ? '❤️ Favourited' : '🤍 Favourite' }}
                </button>
            @else
                <a href="{{ route('login') }}" class="text-yellow-400 text-xs hover:underline">
                    Login to favourite
                </a>
            @endauth
        </div>

        {{-- INFO --}}
        <div class="flex-1 space-y-5">

            {{-- NAME --}}
            <div>
                <p class="text-zinc-500 text-xs uppercase tracking-widest mb-1">Character</p>
                <h1 class="text-4xl font-black text-yellow-400 uppercase tracking-widest leading-tight">
                    {{ $character->name }}
                </h1>
                @if ($character->real_name)
                    <p class="text-zinc-400 text-sm mt-1">
                        Real Name: <span class="text-zinc-200 font-semibold">{{ $character->real_name }}</span>
                    </p>
                @endif
                @if ($character->aliases && count($character->aliases) > 0)
                    <p class="text-zinc-500 text-xs mt-1">
                        Also known as: {{ implode(', ', $character->aliases) }}
                    </p>
                @endif
            </div>

            {{-- DETAILS GRID --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-zinc-800 rounded-xl p-3">
                    <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Gender</p>
                    <p class="text-zinc-100 font-semibold text-sm">{{ $character->gender_label }}</p>
                </div>
                @if ($character->origin)
                    <div class="bg-zinc-800 rounded-xl p-3">
                        <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Origin</p>
                        <p class="text-zinc-100 font-semibold text-sm">{{ $character->origin }}</p>
                    </div>
                @endif
                @if ($character->birth)
                    <div class="bg-zinc-800 rounded-xl p-3">
                        <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Birth</p>
                        <p class="text-zinc-100 font-semibold text-sm">{{ $character->birth }}</p>
                    </div>
                @endif
                @if ($character->publisher)
                    <div class="bg-zinc-800 rounded-xl p-3">
                        <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Publisher</p>
                        <p class="text-zinc-100 font-semibold text-sm">{{ $character->publisher }}</p>
                    </div>
                @endif
            </div>

            {{-- FIRST APPEARANCE & BEST START --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if ($firstAppearance)
                    <div class="bg-zinc-800 border border-zinc-700 rounded-xl p-4">
                        <p class="text-zinc-500 text-xs uppercase tracking-wider mb-2">⭐ First Appearance</p>
                        <a href="{{ route('issues.show', $firstAppearance->id) }}"
                           class="text-zinc-100 font-semibold text-sm hover:text-yellow-400 transition block">
                            {{ $firstAppearance->name ?? 'Untitled' }} #{{ $firstAppearance->issue_number ?? '?' }}
                        </a>
                        @if ($firstAppearance->cover_date)
                            <p class="text-zinc-600 text-xs mt-1">{{ $firstAppearance->cover_date }}</p>
                        @endif
                    </div>
                @endif
                @if ($bestStart)
                    <div class="bg-zinc-800 border border-zinc-700 rounded-xl p-4">
                        <p class="text-zinc-500 text-xs uppercase tracking-wider mb-2">📖 Best Starting Issue</p>
                        <a href="{{ route('issues.show', $bestStart->id) }}"
                           class="text-zinc-100 font-semibold text-sm hover:text-yellow-400 transition block">
                            {{ $bestStart->name ?? 'Untitled' }} #{{ $bestStart->issue_number ?? '?' }}
                        </a>
                        @if ($bestStart->volume)
                            <p class="text-zinc-600 text-xs mt-1">{{ $bestStart->volume->name }}</p>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- POWERS & ABILITIES --}}
@if ($character->powers && count($character->powers) > 0)
    <div class="relative bg-zinc-900 border border-yellow-400/20 rounded-2xl p-6 overflow-hidden">
        {{-- background glow --}}
        <div class="absolute inset-0 bg-gradient-to-br from-yellow-400/5 to-transparent pointer-events-none"></div>
        <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-400/5 rounded-bl-full pointer-events-none"></div>

        <h3 class="text-lg font-bold text-yellow-400 uppercase tracking-widest mb-4 flex items-center gap-2">
            <span class="text-2xl">⚡</span> Powers & Abilities
        </h3>
        <div class="flex flex-wrap gap-2">
            @foreach ($character->powers as $index => $power)
                <span class="bg-yellow-400/10 border border-yellow-400/30 text-yellow-400 text-xs px-3 py-1.5 rounded-full font-semibold
                    hover:bg-yellow-400/20 transition {{ $index >= 8 ? 'hidden power-extra' : '' }}">
                    {{ $power }}
                </span>
            @endforeach
        </div>
        @if (count($character->powers) > 8)
            <button onclick="toggleExtra('power-extra', this)"
                class="text-yellow-400 text-xs hover:underline mt-3 font-semibold">
                Show {{ count($character->powers) - 8 }} more
            </button>
        @endif
    </div>
@endif

{{-- TEAMS, FRIENDS, ENEMIES --}}
@if (($character->teams && count($character->teams) > 0) ||
     ($character->character_friends && count($character->character_friends) > 0) ||
     ($character->character_enemies && count($character->character_enemies) > 0))

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- TEAMS --}}
        @if ($character->teams && count($character->teams) > 0)
            <div class="relative bg-zinc-900 border border-blue-400/20 rounded-2xl p-6 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-400/5 to-transparent pointer-events-none"></div>
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-400/5 rounded-bl-full pointer-events-none"></div>

                <h3 class="text-lg font-bold text-blue-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="text-2xl">🛡️</span> Teams
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($character->teams as $index => $team)
                        <span class="bg-blue-400/10 border border-blue-400/30 text-blue-300 text-xs px-3 py-1.5 rounded-full
                            hover:bg-blue-400/20 transition {{ $index >= 5 ? 'hidden team-extra' : '' }}">
                            {{ $team['name'] }}
                        </span>
                    @endforeach
                </div>
                @if (count($character->teams) > 5)
                    <button onclick="toggleExtra('team-extra', this)"
                        class="text-blue-400 text-xs hover:underline mt-3 font-semibold">
                        Show {{ count($character->teams) - 5 }} more
                    </button>
                @endif
            </div>
        @endif

        {{-- FRIENDS --}}
        @if ($character->character_friends && count($character->character_friends) > 0)
            <div class="relative bg-zinc-900 border border-green-400/20 rounded-2xl p-6 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-green-400/5 to-transparent pointer-events-none"></div>
                <div class="absolute top-0 right-0 w-24 h-24 bg-green-400/5 rounded-bl-full pointer-events-none"></div>

                <h3 class="text-lg font-bold text-green-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="text-2xl">🤝</span> Friends
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($character->character_friends as $index => $friend)
                        <span class="bg-green-400/10 border border-green-400/30 text-green-300 text-xs px-3 py-1.5 rounded-full
                            hover:bg-green-400/20 transition {{ $index >= 5 ? 'hidden friend-extra' : '' }}">
                            {{ $friend['name'] }}
                        </span>
                    @endforeach
                </div>
                @if (count($character->character_friends) > 5)
                    <button onclick="toggleExtra('friend-extra', this)"
                        class="text-green-400 text-xs hover:underline mt-3 font-semibold">
                        Show {{ count($character->character_friends) - 5 }} more
                    </button>
                @endif
            </div>
        @endif

        {{-- ENEMIES --}}
        @if ($character->character_enemies && count($character->character_enemies) > 0)
            <div class="relative bg-zinc-900 border border-red-400/20 rounded-2xl p-6 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-red-400/5 to-transparent pointer-events-none"></div>
                <div class="absolute top-0 right-0 w-24 h-24 bg-red-400/5 rounded-bl-full pointer-events-none"></div>

                <h3 class="text-lg font-bold text-red-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="text-2xl">⚔️</span> Enemies
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($character->character_enemies as $index => $enemy)
                        <span class="bg-red-400/10 border border-red-400/30 text-red-300 text-xs px-3 py-1.5 rounded-full
                            hover:bg-red-400/20 transition {{ $index >= 5 ? 'hidden enemy-extra' : '' }}">
                            {{ $enemy['name'] }}
                        </span>
                    @endforeach
                </div>
                @if (count($character->character_enemies) > 5)
                    <button onclick="toggleExtra('enemy-extra', this)"
                        class="text-red-400 text-xs hover:underline mt-3 font-semibold">
                        Show {{ count($character->character_enemies) - 5 }} more
                    </button>
                @endif
            </div>
        @endif

    </div>
@endif

    {{-- READING PATH --}}
    @if ($issues->count() > 0)
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest mb-6">
                📚 Reading Path
                <span class="text-zinc-600 font-normal text-sm ml-2">({{ $issues->count() }} issues)</span>
            </h3>
            <div class="space-y-2">
                @foreach ($issues as $issue)
                    <a href="{{ route('issues.show', $issue->id) }}"
                       class="flex items-center gap-4 bg-zinc-800 hover:bg-zinc-700 rounded-xl px-4 py-3 transition group">
                        @if ($issue->image)
                            <img src="{{ $issue->image }}" alt="{{ $issue->name }}"
                                 class="w-10 h-14 object-cover object-top rounded flex-shrink-0"/>
                        @else
                            <div class="w-10 h-14 bg-zinc-700 rounded flex items-center justify-center text-zinc-500 text-xs flex-shrink-0">N/A</div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-zinc-100 font-semibold text-sm group-hover:text-yellow-400 transition truncate">
                                {{ $issue->name ?? 'Untitled' }}
                            </p>
                            <p class="text-zinc-500 text-xs mt-0.5">
                                {{ $issue->volume->name ?? '' }}
                                @if ($issue->issue_number) · #{{ $issue->issue_number }} @endif
                                @if ($issue->cover_date) · {{ $issue->cover_date }} @endif
                            </p>
                        </div>
                        <span class="text-zinc-700 group-hover:text-yellow-400 transition flex-shrink-0">→</span>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 text-center">
            <p class="text-zinc-600">No issues linked to this character yet.</p>
            @auth
                @if(Auth::user()->is_admin)
                    <a href="{{ route('dashboard') }}" class="text-yellow-400 text-sm hover:underline mt-2 block">
                        Import a volume to link issues
                    </a>
                @endif
            @endauth
        </div>
    @endif

    {{-- BIOGRAPHY --}}
    @if (count($descriptionSections) > 0)
        <div class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest">📋 Biography</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($descriptionSections as $index => $section)
                    @php
                        $accents = [
                            'border-yellow-400/40 bg-yellow-400/5',
                            'border-blue-400/40 bg-blue-400/5',
                            'border-purple-400/40 bg-purple-400/5',
                            'border-green-400/40 bg-green-400/5',
                            'border-red-400/40 bg-red-400/5',
                            'border-orange-400/40 bg-orange-400/5',
                            'border-pink-400/40 bg-pink-400/5',
                            'border-cyan-400/40 bg-cyan-400/5',
                        ];
                        $titleColors = [
                            'text-yellow-400',
                            'text-blue-400',
                            'text-purple-400',
                            'text-green-400',
                            'text-red-400',
                            'text-orange-400',
                            'text-pink-400',
                            'text-cyan-400',
                        ];
                        $accent     = $accents[$index % count($accents)];
                        $titleColor = $titleColors[$index % count($titleColors)];
                    @endphp
                    <div class="border {{ $accent }} rounded-2xl p-5 relative overflow-hidden hover:scale-[1.01] transition duration-200">
                        <div class="absolute top-0 right-0 w-16 h-16 opacity-10 rounded-bl-full {{ str_replace(['border-', '/40'], ['bg-', ''], explode(' ', $accent)[0]) }}"></div>
                        @if ($section['title'])
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-1 h-4 rounded-full {{ str_replace('text-', 'bg-', $titleColor) }}"></div>
                                <p class="{{ $titleColor }} text-xs font-black uppercase tracking-widest">
                                    {{ $section['title'] }}
                                </p>
                            </div>
                        @endif
                        <p class="text-zinc-300 text-sm leading-relaxed">
                            {{ Str::limit($section['content'], 300) }}
                        </p>
                        @if (strlen($section['content']) > 300)
                            <button
                                onclick="toggleSection(this)"
                                data-full="{{ e($section['content']) }}"
                                data-short="{{ e(Str::limit($section['content'], 300)) }}"
                                class="{{ $titleColor }} text-xs hover:underline mt-2 block font-semibold">
                                Read more
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

@endsection

@push('scripts')
<script>
    function toggleExtra(className, btn) {
        const items  = document.querySelectorAll('.' + className);
        const isHidden = items[0].classList.contains('hidden');
        items.forEach(el => el.classList.toggle('hidden'));
        const count  = items.length;
        btn.textContent = isHidden ? 'Show less' : `Show ${count} more`;
    }

    function toggleSection(btn) {
        const p      = btn.previousElementSibling;
        const isFull = btn.textContent.trim() === 'Read less';
        p.textContent = isFull ? btn.dataset.short : btn.dataset.full;
        btn.textContent = isFull ? 'Read more' : 'Read less';
    }

    async function toggleFavouriteCharacter(id) {
        const res  = await fetch(`/characters/${id}/favourite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        });
        const data = await res.json();
        const btn  = document.getElementById('charFavBtn');
        if (data.status === 'favourited') {
            btn.textContent = '❤️ Favourited';
            btn.className = 'w-full text-sm px-4 py-2 rounded-lg transition font-semibold bg-red-500 text-white';
        } else {
            btn.textContent = '🤍 Favourite';
            btn.className = 'w-full text-sm px-4 py-2 rounded-lg transition font-semibold bg-zinc-800 hover:bg-zinc-700 text-zinc-300';
        }
    }
</script>
@endpush