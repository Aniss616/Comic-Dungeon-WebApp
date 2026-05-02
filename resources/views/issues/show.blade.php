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
            <div class="flex-shrink-0">
                @if ($issue->image)
                    <img
                        src="{{ $issue->image }}"
                        alt="{{ $issue->name }}"
                        class="w-48 object-cover rounded-xl border border-zinc-700"
                    />
                @else
                    <div class="w-48 h-64 bg-zinc-800 rounded-xl flex items-center justify-center text-zinc-600 text-5xl">📄</div>
                @endif
            </div>

            {{-- INFO --}}
            <div class="flex-1 space-y-4">
                <div>
                    <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">
                        @if ($issue->volume)
                            <a href="{{ route('volumes.show', $issue->volume->id) }}" class="hover:text-yellow-400 transition">
                                {{ $issue->volume->name }}
                            </a>
                            @if ($issue->volume->publisher)
                                · {{ $issue->volume->publisher->name }}
                            @endif
                        @endif
                    </p>
                    <h1 class="text-3xl font-black text-yellow-400 uppercase tracking-widest">
                        {{ $issue->name ?? 'Untitled' }}
                    </h1>
                    <p class="text-zinc-500 text-sm mt-1">
                        Issue #{{ $issue->issue_number ?? '?' }}
                        @if ($issue->cover_date) · {{ $issue->cover_date }} @endif
                    </p>
                </div>

                @if ($issue->description)
                    <p class="text-zinc-300 text-sm leading-relaxed">
                        {!! strip_tags($issue->description) !!}
                    </p>
                @endif

                {{-- USER ACTIONS --}}
                @auth
        @php
            $isRead      = Auth::user()->reads()->where('issue_id', $issue->id)->exists();
            $isFavourite = Auth::user()->favourites()->where('issue_id', $issue->id)->exists();
        @endphp

        <div class="flex gap-3 pt-2">
            <button
                id="readBtn"
                onclick="toggleRead({{ $issue->id }})"
                class="text-sm px-4 py-2 rounded-lg transition font-semibold
                    {{ $isRead ? 'bg-green-500 text-white' : 'bg-zinc-800 hover:bg-zinc-700 text-zinc-300' }}">
                {{ $isRead ? '✅ Read' : '☐ Mark as Read' }}
            </button>
            <button
                id="favBtn"
                onclick="toggleFavouriteIssue({{ $issue->id }})"
                class="text-sm px-4 py-2 rounded-lg transition font-semibold
                    {{ $isFavourite ? 'bg-red-500 text-white' : 'bg-zinc-800 hover:bg-zinc-700 text-zinc-300' }}">
                {{ $isFavourite ? '❤️ Favourited' : '🤍 Favourite' }}
            </button>
        </div>
    @else
        <p class="text-zinc-600 text-xs pt-2">
            <a href="{{ route('login') }}" class="text-yellow-400 hover:underline">Login</a>
            to mark as read or favourite this issue
        </p>
    @endauth

        {{-- CHARACTERS --}}
        @if ($issue->characters->count() > 0)
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest mb-6">
                    🦸 Characters <span class="text-zinc-600 font-normal text-sm">({{ $issue->characters->count() }})</span>
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach ($issue->characters as $character)
                        <a href="{{ route('characters.show', $character->id) }}"
                           class="bg-zinc-800 hover:bg-zinc-700 rounded-xl overflow-hidden transition group">
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
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- PEOPLE / CREDITS --}}
        @if ($issue->people->count() > 0)
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest mb-6">
                    ✏️ Credits <span class="text-zinc-600 font-normal text-sm">({{ $issue->people->count() }})</span>
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach ($issue->people as $person)
                        <div class="bg-zinc-800 rounded-xl px-4 py-3">
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
            btn.className = 'text-sm px-4 py-2 rounded-lg transition font-semibold bg-green-500 text-white';
        } else {
            btn.textContent = '☐ Mark as Read';
            btn.className = 'text-sm px-4 py-2 rounded-lg transition font-semibold bg-zinc-800 hover:bg-zinc-700 text-zinc-300';
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
            btn.className = 'text-sm px-4 py-2 rounded-lg transition font-semibold bg-red-500 text-white';
        } else {
            btn.textContent = '🤍 Favourite';
            btn.className = 'text-sm px-4 py-2 rounded-lg transition font-semibold bg-zinc-800 hover:bg-zinc-700 text-zinc-300';
        }
    }
</script>
@endpush