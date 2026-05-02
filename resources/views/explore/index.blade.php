@extends('layouts.app')

@section('title', 'Explore')

@section('content')

    <div class="space-y-6">

        {{-- HEADER --}}
        <div>
            <h2 class="text-2xl font-black text-yellow-400 uppercase tracking-widest">Explore</h2>
            <p class="text-zinc-500 text-sm mt-1">Discover characters, volumes and issues</p>
        </div>

        {{-- SEARCH BAR --}}
        <form method="GET" action="{{ route('explore') }}" id="exploreForm">
            <input type="hidden" name="tab" id="activeTabInput" value="{{ $tab }}"/>
            <div class="flex gap-3">
                <input
                    type="text"
                    name="q"
                    id="searchInput"
                    value="{{ $q ?? '' }}"
                    placeholder="Search the dungeon..."
                    class="flex-1 bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-yellow-400 transition"
                />
                <button
                    type="submit"
                    class="bg-yellow-400 hover:bg-yellow-300 text-zinc-950 font-bold px-6 py-2.5 rounded-lg transition text-sm uppercase tracking-wide">
                    Search
                </button>
            </div>

            {{-- TABS --}}
            <div class="flex gap-2 mt-4">
                @foreach (['characters' => '🦸 Characters', 'volumes' => '📚 Volumes', 'issues' => '📄 Issues'] as $key => $label)
                    <button
                        type="submit"
                        onclick="setTab('{{ $key }}')"
                        class="px-5 py-2 rounded-lg text-sm font-semibold transition
                            {{ $tab === $key
                                ? 'bg-yellow-400 text-zinc-950'
                                : 'bg-zinc-800 text-zinc-400 hover:bg-zinc-700 hover:text-zinc-100' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </form>

        {{-- RESULTS --}}
        <div>

            {{-- CHARACTERS TAB --}}
            @if ($tab === 'characters')
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
                        <p class="text-zinc-600 text-lg">No characters found.</p>
                    </div>
                @endif

                {{-- PAGINATION --}}
                @if ($characters->hasPages())
                    <div class="flex justify-center mt-8">
                        {{ $characters->links() }}
                    </div>
                @endif
            @endif

            {{-- VOLUMES TAB --}}
            @if ($tab === 'volumes')
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
                    <div class="text-center py-24">
                        <p class="text-zinc-600 text-lg">No volumes found.</p>
                    </div>
                @endif

                {{-- PAGINATION --}}
                @if ($volumes->hasPages())
                    <div class="flex justify-center mt-8">
                        {{ $volumes->links() }}
                    </div>
                @endif
            @endif

            {{-- ISSUES TAB --}}
            @if ($tab === 'issues')
                @if ($issues->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach ($issues as $issue)
                            <a href="{{ route('issues.show', $issue->id) }}"
                               class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden hover:border-yellow-400 transition group">
                                <div class="aspect-[2/3] overflow-hidden bg-zinc-800">
                                    @if ($issue->image)
                                        <img
                                            src="{{ $issue->image }}"
                                            alt="{{ $issue->name }}"
                                            class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-300"
                                        />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-zinc-600 text-3xl">📄</div>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <p class="text-zinc-100 font-semibold text-sm truncate">{{ $issue->name ?? 'Untitled' }}</p>
                                    <p class="text-zinc-500 text-xs truncate mt-0.5">
                                        {{ $issue->volume->name ?? 'Unknown Volume' }}
                                        @if ($issue->issue_number) · #{{ $issue->issue_number }} @endif
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-24">
                        <p class="text-zinc-600 text-lg">No issues found.</p>
                    </div>
                @endif

                {{-- PAGINATION --}}
                @if ($issues->hasPages())
                    <div class="flex justify-center mt-8">
                        {{ $issues->links() }}
                    </div>
                @endif
            @endif

        </div>

    </div>

@endsection

@push('scripts')
<script>
    function setTab(tab) {
        document.getElementById('activeTabInput').value = tab;
    }
</script>
@endpush