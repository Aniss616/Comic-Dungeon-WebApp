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
            <div class="flex-shrink-0">
                @if ($character->image)
                    <img
                        src="{{ $character->image }}"
                        alt="{{ $character->name }}"
                        class="w-48 h-48 object-cover object-top rounded-xl border border-zinc-700"
                    />
                @else
                    <div class="w-48 h-48 bg-zinc-800 rounded-xl flex items-center justify-center text-zinc-600 text-5xl">?</div>
                @endif
            </div>

            {{-- INFO --}}
            <div class="flex-1 space-y-4">
                <div>
                    <h1 class="text-3xl font-black text-yellow-400 uppercase tracking-widest">{{ $character->name }}</h1>
                    @if ($character->aliases && count($character->aliases) > 0)
                        <p class="text-zinc-500 text-sm mt-1">
                            Also known as: {{ implode(', ', $character->aliases) }}
                        </p>
                    @endif
                </div>

                @if ($character->description)
                    <p class="text-zinc-300 text-sm leading-relaxed">
                        {!! strip_tags($character->description) !!}
                    </p>
                @endif

                {{-- FIRST APPEARANCE & BEST START --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    @if ($firstAppearance)
                        <div class="bg-zinc-800 rounded-xl p-4">
                            <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">First Appearance</p>
                            <a href="{{ route('issues.show', $firstAppearance->id) }}"
                               class="text-zinc-100 font-semibold text-sm hover:text-yellow-400 transition">
                                {{ $firstAppearance->name ?? 'Untitled' }} #{{ $firstAppearance->issue_number ?? '?' }}
                            </a>
                            @if ($firstAppearance->cover_date)
                                <p class="text-zinc-600 text-xs mt-0.5">{{ $firstAppearance->cover_date }}</p>
                            @endif
                        </div>
                    @endif

                    @if ($bestStart)
                        <div class="bg-zinc-800 rounded-xl p-4">
                            <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Best Starting Issue</p>
                            <a href="{{ route('issues.show', $bestStart->id) }}"
                               class="text-zinc-100 font-semibold text-sm hover:text-yellow-400 transition">
                                {{ $bestStart->name ?? 'Untitled' }} #{{ $bestStart->issue_number ?? '?' }}
                            </a>
                            @if ($bestStart->volume)
                                <p class="text-zinc-600 text-xs mt-0.5">{{ $bestStart->volume->name }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- USER ACTIONS --}}
                @auth
        @php
            $isFavourite = Auth::user()->favouriteCharacters()->where('character_id', $character->id)->exists();
        @endphp

        <div class="flex gap-3 pt-2">
            <button
                id="charFavBtn"
                onclick="toggleFavouriteCharacter({{ $character->id }})"
                class="text-sm px-4 py-2 rounded-lg transition font-semibold
                    {{ $isFavourite ? 'bg-red-500 text-white' : 'bg-zinc-800 hover:bg-zinc-700 text-zinc-300' }}">
                {{ $isFavourite ? '❤️ Favourited' : '🤍 Favourite' }}
            </button>
        </div>
    @else
        <p class="text-zinc-600 text-xs pt-2">
            <a href="{{ route('login') }}" class="text-yellow-400 hover:underline">Login</a> to favourite this character
        </p>
    @endauth

        {{-- READING PATH --}}
        @if ($issues->count() > 0)
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest mb-6">
                    📚 Reading Path <span class="text-zinc-600 font-normal text-sm">({{ $issues->count() }} issues)</span>
                </h3>

                <div class="space-y-2">
                    @foreach ($issues as $issue)
                        <a href="{{ route('issues.show', $issue->id) }}"
                           class="flex items-center gap-4 bg-zinc-800 hover:bg-zinc-700 rounded-xl px-4 py-3 transition group">
                            @if ($issue->image)
                                <img
                                    src="{{ $issue->image }}"
                                    alt="{{ $issue->name }}"
                                    class="w-10 h-14 object-cover object-top rounded flex-shrink-0"
                                />
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
                            @auth
                                <span class="text-zinc-600 text-xs flex-shrink-0">Mark as read</span>
                            @endauth
                            <span class="text-zinc-700 group-hover:text-yellow-400 transition">→</span>
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

    </div>

@endsection
@push('scripts')
<script>
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
            btn.className = 'text-sm px-4 py-2 rounded-lg transition font-semibold bg-red-500 text-white';
        } else {
            btn.textContent = '🤍 Favourite';
            btn.className = 'text-sm px-4 py-2 rounded-lg transition font-semibold bg-zinc-800 hover:bg-zinc-700 text-zinc-300';
        }
    }
</script>
@endpush