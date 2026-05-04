@extends('layouts.app')

@section('title', $issue->name ?? 'Issue')

@section('content')

<div class="space-y-8">

    {{-- BACK --}}
    <a href="{{ route('volumes.show', $issue->volume_id) }}" class="text-zinc-500 hover:text-yellow-400 text-sm transition">
        ← Back to {{ $issue->volume->name ?? 'Volume' }}
    </a>

    {{-- HERO --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 flex flex-col md:flex-row gap-8">

        {{-- COVER --}}
        <div class="flex-shrink-0 flex flex-col items-center gap-4">
            @if ($issue->image)
                <img
                    src="{{ $issue->image }}"
                    alt="{{ $issue->name }}"
                    class="w-48 object-cover rounded-xl border border-zinc-700"
                />
            @else
                <div class="w-48 h-64 bg-zinc-800 rounded-xl flex items-center justify-center text-zinc-600 text-5xl">📄</div>
            @endif

            {{-- USER ACTIONS --}}
            @auth
                @php
                    $isRead      = Auth::user()->reads()->where('issue_id', $issue->id)->exists();
                    $isFavourite = Auth::user()->favourites()->where('issue_id', $issue->id)->exists();
                @endphp
                <button
                    id="readBtn"
                    onclick="toggleRead({{ $issue->id }})"
                    class="w-full text-sm px-4 py-2 rounded-lg transition font-semibold
                        {{ $isRead ? 'bg-green-500 text-white' : 'bg-zinc-800 hover:bg-zinc-700 text-zinc-300' }}">
                    {{ $isRead ? '✅ Read' : '☐ Mark as Read' }}
                </button>
                <button
                    id="favBtn"
                    onclick="toggleFavouriteIssue({{ $issue->id }})"
                    class="w-full text-sm px-4 py-2 rounded-lg transition font-semibold
                        {{ $isFavourite ? 'bg-red-500 text-white' : 'bg-zinc-800 hover:bg-zinc-700 text-zinc-300' }}">
                    {{ $isFavourite ? '❤️ Favourited' : '🤍 Favourite' }}
                </button>
            @else
                <a href="{{ route('login') }}" class="text-yellow-400 text-xs hover:underline">
                    Login to track this issue
                </a>
            @endauth
        </div>

        {{-- INFO --}}
        <div class="flex-1 space-y-5">

            {{-- TITLE --}}
            <div>
                <p class="text-zinc-500 text-xs uppercase tracking-widest mb-1">
                    @if ($issue->volume)
                        <a href="{{ route('volumes.show', $issue->volume->id) }}" class="hover:text-yellow-400 transition">
                            {{ $issue->volume->name }}
                        </a>
                        @if ($issue->volume->publisher)
                            · {{ $issue->volume->publisher->name }}
                        @endif
                    @endif
                </p>
                <h1 class="text-4xl font-black text-yellow-400 uppercase tracking-widest leading-tight">
                    {{ $issue->name ?? 'Untitled' }}
                </h1>
                <p class="text-zinc-400 text-sm mt-1">
                    Issue <span class="text-zinc-200 font-semibold">#{{ $issue->issue_number ?? '?' }}</span>
                </p>
            </div>

            {{-- DATES --}}
            <div class="flex flex-wrap gap-3">
                @if ($issue->cover_date)
                    <div class="bg-zinc-800 border border-zinc-700 rounded-xl px-5 py-3 text-center">
                        <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Cover Date</p>
                        <p class="text-zinc-100 font-black text-lg">{{ $issue->cover_date }}</p>
                    </div>
                @endif
                @if ($issue->store_date)
                    <div class="bg-zinc-800 border border-zinc-700 rounded-xl px-5 py-3 text-center">
                        <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Store Date</p>
                        <p class="text-zinc-100 font-black text-lg">{{ $issue->store_date }}</p>
                    </div>
                @endif
                <div class="bg-zinc-800 border border-zinc-700 rounded-xl px-5 py-3 text-center">
                    <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Characters</p>
                    <p class="text-zinc-100 font-black text-lg">{{ $issue->characters->count() }}</p>
                </div>
                <div class="bg-zinc-800 border border-zinc-700 rounded-xl px-5 py-3 text-center">
                    <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Credits</p>
                    <p class="text-zinc-100 font-black text-lg">{{ $issue->people->count() }}</p>
                </div>
            </div>

            {{-- STORY ARCS --}}
            @if ($issue->story_arc_credits && count($issue->story_arc_credits) > 0)
                <div>
                    <p class="text-zinc-500 text-xs uppercase tracking-wider mb-2">📖 Story Arcs</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($issue->story_arc_credits as $arc)
                            <span class="bg-yellow-400/10 border border-yellow-400/30 text-yellow-400 text-xs px-3 py-1.5 rounded-full font-semibold">
                                {{ $arc['name'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- TEAMS --}}
            @if ($issue->teams && count($issue->teams) > 0)
                <div>
                    <p class="text-zinc-500 text-xs uppercase tracking-wider mb-2">🛡️ Teams</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($issue->teams as $team)
                            <span class="bg-zinc-800 border border-zinc-700 text-zinc-300 text-xs px-3 py-1.5 rounded-full">
                                {{ $team['name'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- LOCATIONS --}}
            @if ($issue->locations && count($issue->locations) > 0)
                <div>
                    <p class="text-zinc-500 text-xs uppercase tracking-wider mb-2">📍 Locations</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($issue->locations as $location)
                            <span class="bg-zinc-800 border border-zinc-700 text-zinc-300 text-xs px-3 py-1.5 rounded-full">
                                {{ $location['name'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- DESCRIPTION SECTIONS --}}
    @if (count($descriptionSections) > 0)
        <div class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest">📋 Story</h3>
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

    {{-- CHARACTERS --}}
    @if ($issue->characters->count() > 0)
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest mb-6">
                🦸 Characters
                <span class="text-zinc-600 font-normal text-sm ml-2">({{ $issue->characters->count() }})</span>
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach ($issue->characters as $character)
                    <a href="{{ route('characters.show', $character->id) }}"
                       class="bg-zinc-800 hover:bg-zinc-700 rounded-xl overflow-hidden transition group border border-zinc-700 hover:border-yellow-400">
                        <div class="aspect-square overflow-hidden bg-zinc-700">
                            @if ($character->image)
                                <img
                                    src="{{ $character->image }}"
                                    alt="{{ $character->name }}"
                                    class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-300"
                                />
                            @else
                                <div class="w-full h-full flex items-center justify-center text-zinc-600 text-2xl">?</div>
                            @endif
                        </div>
                        <div class="p-2">
                            <p class="text-zinc-100 text-xs font-semibold truncate group-hover:text-yellow-400 transition">
                                {{ $character->name }}
                            </p>
                            @if ($character->real_name)
                                <p class="text-zinc-500 text-xs truncate">{{ $character->real_name }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- CREDITS --}}
    @if ($issue->people->count() > 0)
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest mb-6">
                ✏️ Credits
                <span class="text-zinc-600 font-normal text-sm ml-2">({{ $issue->people->count() }})</span>
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                @foreach ($issue->people as $person)
                    <div class="bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3">
                        <p class="text-zinc-100 font-semibold text-sm truncate">{{ $person->name }}</p>
                        @if ($person->pivot->role)
                            <p class="text-yellow-400 text-xs mt-0.5 capitalize">{{ $person->pivot->role }}</p>
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
    function toggleSection(btn) {
        const p      = btn.previousElementSibling;
        const isFull = btn.textContent.trim() === 'Read less';
        p.textContent = isFull ? btn.dataset.short : btn.dataset.full;
        btn.textContent = isFull ? 'Read more' : 'Read less';
    }

    async function toggleRead(id) {
        const res  = await fetch(`/issues/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        });
        const data = await res.json();
        const btn  = document.getElementById('readBtn');
        if (data.status === 'read') {
            btn.textContent = '✅ Read';
            btn.className = 'w-full text-sm px-4 py-2 rounded-lg transition font-semibold bg-green-500 text-white';
        } else {
            btn.textContent = '☐ Mark as Read';
            btn.className = 'w-full text-sm px-4 py-2 rounded-lg transition font-semibold bg-zinc-800 hover:bg-zinc-700 text-zinc-300';
        }
    }

    async function toggleFavouriteIssue(id) {
        const res  = await fetch(`/issues/${id}/favourite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        });
        const data = await res.json();
        const btn  = document.getElementById('favBtn');
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