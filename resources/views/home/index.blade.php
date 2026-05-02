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
            {{-- Placeholder for recommendation system --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-black text-zinc-100 uppercase tracking-widest">
                        🎯 Recommended For You
                    </h2>
                    <span class="text-zinc-600 text-xs">Based on your reading history</span>
                </div>
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-8 text-center">
                    <p class="text-zinc-600">Recommendations will appear here once you start reading and favouriting issues.</p>
                    <a href="{{ route('explore') }}" class="text-yellow-400 text-sm hover:underline mt-2 block">
                        Start exploring to build your history →
                    </a>
                </div>
            </div>
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

@endsection