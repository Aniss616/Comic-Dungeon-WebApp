@extends('layouts.app')

@section('title', 'Profile')

@section('content')

    <div class="space-y-8">

        {{-- PROFILE HEADER --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-8 flex flex-col md:flex-row items-center md:items-start gap-6">

            {{-- AVATAR --}}
            <div class="flex-shrink-0">
                <div class="w-24 h-24 rounded-full bg-zinc-800 border-2 border-zinc-700 overflow-hidden flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-zinc-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                    </svg>
                </div>
            </div>

            {{-- USER INFO --}}
            <div class="text-center md:text-left space-y-2">
                <h1 class="text-3xl font-black text-yellow-400 uppercase tracking-widest">
                    {{ $user->username }}
                </h1>
                <p class="text-zinc-500 text-sm">{{ $user->email }}</p>
                <div class="flex items-center justify-center md:justify-start gap-4 pt-2">
                    <div class="text-center">
                        <p class="text-yellow-400 font-black text-xl">{{ $readIssues->count() }}</p>
                        <p class="text-zinc-500 text-xs uppercase tracking-wider">Read</p>
                    </div>
                    <div class="w-px h-8 bg-zinc-800"></div>
                    <div class="text-center">
                        <p class="text-yellow-400 font-black text-xl">{{ $favouriteIssues->count() }}</p>
                        <p class="text-zinc-500 text-xs uppercase tracking-wider">Favourites</p>
                    </div>
                    <div class="w-px h-8 bg-zinc-800"></div>
                    <div class="text-center">
                        <p class="text-yellow-400 font-black text-xl">{{ $favouriteCharacters->count() }}</p>
                        <p class="text-zinc-500 text-xs uppercase tracking-wider">Characters</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- FAVOURITE CHARACTERS --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest mb-6">
                🦸 Favourite Characters
            </h3>
            @if ($favouriteCharacters->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
                    @foreach ($favouriteCharacters as $character)
                        <a href="{{ route('characters.show', $character->id) }}"
                           class="bg-zinc-800 rounded-xl overflow-hidden hover:border-yellow-400 border border-zinc-700 transition group">
                            <div class="aspect-square overflow-hidden">
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
                            <div class="p-2">
                                <p class="text-zinc-100 text-xs font-semibold truncate">{{ $character->name }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-zinc-600 text-sm">No favourite characters yet.</p>
                    <a href="{{ route('explore') }}" class="text-yellow-400 text-sm hover:underline mt-2 block">
                        Explore characters →
                    </a>
                </div>
            @endif
        </div>

        {{-- FAVOURITE ISSUES --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest mb-6">
                ❤️ Favourite Issues
            </h3>
            @if ($favouriteIssues->count() > 0)
                <div class="space-y-2">
                    @foreach ($favouriteIssues as $issue)
                        <a href="{{ route('issues.show', $issue->id) }}"
                           class="flex items-center gap-4 bg-zinc-800 hover:bg-zinc-700 rounded-xl px-4 py-3 transition group">
                            @if ($issue->image)
                                <img src="{{ $issue->image }}" class="w-10 h-14 object-cover object-top rounded flex-shrink-0"/>
                            @else
                                <div class="w-10 h-14 bg-zinc-700 rounded flex-shrink-0"></div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-zinc-100 font-semibold text-sm truncate group-hover:text-yellow-400 transition">
                                    {{ $issue->name ?? 'Untitled' }}
                                </p>
                                <p class="text-zinc-500 text-xs mt-0.5">
                                    {{ $issue->volume->name ?? '' }}
                                    @if ($issue->issue_number) · #{{ $issue->issue_number }} @endif
                                </p>
                            </div>
                            <span class="text-zinc-700 group-hover:text-yellow-400 transition">→</span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-zinc-600 text-sm">No favourite issues yet.</p>
                    <a href="{{ route('explore') }}" class="text-yellow-400 text-sm hover:underline mt-2 block">
                        Explore issues →
                    </a>
                </div>
            @endif
        </div>

        {{-- READ ISSUES --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest mb-6">
                ✅ Issues Read
            </h3>
            @if ($readIssues->count() > 0)
                <div class="space-y-2">
                    @foreach ($readIssues as $issue)
                        <a href="{{ route('issues.show', $issue->id) }}"
                           class="flex items-center gap-4 bg-zinc-800 hover:bg-zinc-700 rounded-xl px-4 py-3 transition group">
                            @if ($issue->image)
                                <img src="{{ $issue->image }}" class="w-10 h-14 object-cover object-top rounded flex-shrink-0"/>
                            @else
                                <div class="w-10 h-14 bg-zinc-700 rounded flex-shrink-0"></div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-zinc-100 font-semibold text-sm truncate group-hover:text-yellow-400 transition">
                                    {{ $issue->name ?? 'Untitled' }}
                                </p>
                                <p class="text-zinc-500 text-xs mt-0.5">
                                    {{ $issue->volume->name ?? '' }}
                                    @if ($issue->issue_number) · #{{ $issue->issue_number }} @endif
                                </p>
                            </div>
                            <span class="text-zinc-700 group-hover:text-yellow-400 transition">→</span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-zinc-600 text-sm">No issues read yet.</p>
                    <a href="{{ route('explore') }}" class="text-yellow-400 text-sm hover:underline mt-2 block">
                        Start reading →
                    </a>
                </div>
            @endif
        </div>

    </div>

@endsection