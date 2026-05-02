@extends('layouts.app')

@section('title', 'Characters')

@section('content')

    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-black text-yellow-400 uppercase tracking-widest">Characters</h2>
                <p class="text-zinc-500 text-sm mt-1">{{ $characters->total() }} characters in the dungeon</p>
            </div>
            <a href="{{ route('search') }}"
               class="bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm px-4 py-2 rounded-lg transition">
                🔍 Search
            </a>
        </div>

        {{-- GRID --}}
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
        <div class="text-center py-24">
            <p class="text-zinc-600 text-lg">No characters imported yet.</p>
            @auth
                <a href="{{ route('dashboard') }}" class="text-yellow-400 text-sm hover:underline mt-2 block">
                Go to dashboard to import
                </a>
            @endauth
        </div>
    @endif

        {{-- PAGINATION --}}
        @if ($characters->hasPages())
            <div class="flex justify-center mt-8">
                {{ $characters->links() }}
            </div>
        @endif

    </div>

@endsection