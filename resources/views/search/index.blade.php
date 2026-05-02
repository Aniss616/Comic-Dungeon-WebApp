@extends('layouts.app')

@section('title', 'Search')

@section('content')

    <div class="space-y-8">

        {{-- HEADER --}}
        <div>
            <h2 class="text-2xl font-black text-yellow-400 uppercase tracking-widest">Search</h2>
            <p class="text-zinc-500 text-sm mt-1">Search characters and volumes in the dungeon</p>
        </div>

        {{-- SEARCH BAR --}}
        <form method="GET" action="{{ route('search') }}" class="flex gap-3">
            <input
                type="text"
                name="q"
                value="{{ $q ?? '' }}"
                placeholder="Search e.g. Moon Knight, Batman, X-Men..."
                autofocus
                class="flex-1 bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-yellow-400 transition"
            />
            <button
                type="submit"
                class="bg-yellow-400 hover:bg-yellow-300 text-zinc-950 font-bold px-6 py-2.5 rounded-lg transition text-sm uppercase tracking-wide">
                Search
            </button>
        </form>

        {{-- RESULTS --}}
        @if ($q)

            {{-- CHARACTERS --}}
            <div>
                <h3 class="text-sm font-bold text-zinc-500 uppercase tracking-widest mb-4">
                    Characters <span class="text-zinc-700">({{ $characters->count() }})</span>
                </h3>

                @if ($characters->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach ($characters as $character)
                            <a href="{{ route('characters.show', $character->id) }}"
                               class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden hover:border-yellow-400 transition group">
                                <div class="aspect-square overflow-hidden bg-zinc-800">
                                    @if ($character->image)
                                        <img
                                            src="{{ $character->image }}"
                                            alt="{{ $character->name }}"
                                            class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-300"
                                        />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-zinc-600 text-3xl">?</div>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <p class="text-zinc-100 font-semibold text-sm truncate">{{ $character->name }}</p>
                                    <p class="text-zinc-500 text-xs truncate mt-0.5">{{ $character->description ?? 'No description' }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-zinc-600 text-sm">No characters found for "{{ $q }}"</p>
                @endif
            </div>

            {{-- VOLUMES --}}
            <div>
                <h3 class="text-sm font-bold text-zinc-500 uppercase tracking-widest mb-4">
                    Volumes <span class="text-zinc-700">({{ $volumes->count() }})</span>
                </h3>

                @if ($volumes->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach ($volumes as $volume)
                            <a href="{{ route('volumes.show', $volume->id) }}"
                               class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden hover:border-yellow-400 transition group">
                                <div class="aspect-[2/3] overflow-hidden bg-zinc-800">
                                    @if ($volume->cover_image)
                                        <img
                                            src="{{ $volume->cover_image }}"
                                            alt="{{ $volume->name }}"
                                            class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-300"
                                        />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-zinc-600 text-3xl">📚</div>
                                    @endif
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
                @else
                    <p class="text-zinc-600 text-sm">No volumes found for "{{ $q }}"</p>
                @endif
            </div>

        @else
            {{-- EMPTY STATE --}}
            <div class="text-center py-24">
                <p class="text-zinc-600 text-lg">Type something to search the dungeon</p>
                <p class="text-zinc-700 text-sm mt-2">Characters, volumes and more</p>
            </div>
        @endif

    </div>

@endsection