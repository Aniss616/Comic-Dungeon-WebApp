@extends('layouts.app')

@section('title', 'Explore')

@section('content')

<div class="page-header">
    <span class="page-header-eyebrow">Archive</span>
    <h1 class="page-header-title">Explore</h1>
    <p class="page-header-sub">
        Discover characters, comic volumes, and issues hidden inside the dungeon archives.
    </p>
</div>

{{-- SEARCH --}}
<div class="card" style="padding:1.25rem; margin-bottom:2rem;">
    <form method="GET" action="{{ route('explore') }}" id="exploreForm">

        <input type="hidden" name="tab" id="activeTabInput" value="{{ $tab }}"/>

        <div class="flex gap-2 flex-wrap">

            <div class="search-wrap w-full" style="flex:1;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-3.5-3.5"></path>
                </svg>

                <input
                    type="text"
                    name="q"
                    id="searchInput"
                    value="{{ $q ?? '' }}"
                    placeholder="Search the dungeon archives..."
                    class="search-input"
                />
            </div>

            <button type="submit" class="btn btn-primary">
                Search
            </button>

        </div>

        {{-- TABS --}}
        <div class="mt-2">
            <div class="pill-tabs">

                @foreach ([
                    'characters' => 'Characters',
                    'volumes'   => 'Volumes',
                    'issues'    => 'Issues'
                ] as $key => $label)

                    <button
                        type="submit"
                        onclick="setTab('{{ $key }}')"
                        class="pill-tab {{ $tab === $key ? 'active' : '' }}">
                        {{ $label }}
                    </button>

                @endforeach

            </div>
        </div>

    </form>
</div>

{{-- RESULTS --}}
<div>

    {{-- CHARACTERS --}}
    @if ($tab === 'characters')

        <div class="section-heading">
            <h2 class="section-title">Characters</h2>
            <div class="section-rule"></div>

            @if($characters->count())
                <span class="badge badge-red">
                    {{ $characters->total() }} Results
                </span>
            @endif
        </div>

        @if ($characters->count() > 0)

            <div class="grid-5">

                @foreach ($characters as $character)

                    <a href="{{ route('characters.show', $character->id) }}"
                       class="cover-card">

                        <div class="cover-card-img" style="aspect-ratio:1/1;">

                            @if ($character->image)

                                <img
                                    src="{{ $character->image }}"
                                    alt="{{ $character->name }}"
                                    class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-300"
                                />

                            @else

                                <div class="cover-card-placeholder">
                                    ?
                                </div>

                            @endif

                        </div>

                        <div class="cover-card-body">

                            <div class="cover-card-title">
                                {{ $character->name }}
                            </div>

                            <div class="cover-card-meta">
                                {{ $character->publisher ?? 'Unknown Publisher' }}
                            </div>

                        </div>

                    </a>

                @endforeach

            </div>

        @else

            <div class="card" style="padding:4rem 2rem; text-align:center;">
                <p class="text-muted">
                    No characters found in the archive.
                </p>
            </div>

        @endif

        {{-- PAGINATION --}}
        @if ($characters->hasPages())
            <div class="mt-4">
                {{ $characters->links() }}
            </div>
        @endif

    @endif


    {{-- VOLUMES --}}
    @if ($tab === 'volumes')

        <div class="section-heading">
            <h2 class="section-title">Volumes</h2>
            <div class="section-rule"></div>

            @if($volumes->count())
                <span class="badge badge-amber">
                    {{ $volumes->total() }} Results
                </span>
            @endif
        </div>

        @if ($volumes->count() > 0)

            <div class="grid-5">

                @foreach ($volumes as $volume)

                    <a href="{{ route('volumes.show', $volume->id) }}"
                       class="cover-card">

                        <div class="cover-card-img">

                            @if ($volume->cover_image)

                                <img
                                    src="{{ $volume->cover_image }}"
                                    alt="{{ $volume->name }}"
                                />

                            @else

                                <div class="cover-card-placeholder">
                                    📚
                                </div>

                            @endif

                        </div>

                        <div class="cover-card-body">

                            <div class="cover-card-title">
                                {{ $volume->name }}
                            </div>

                            <div class="cover-card-meta">
                                {{ $volume->publisher->name ?? 'Unknown Publisher' }}
                            </div>

                        </div>

                    </a>

                @endforeach

            </div>

        @else

            <div class="card" style="padding:4rem 2rem; text-align:center;">
                <p class="text-muted">
                    No volumes found in the archive.
                </p>
            </div>

        @endif

        {{-- PAGINATION --}}
        @if ($volumes->hasPages())
            <div class="mt-4">
                {{ $volumes->links() }}
            </div>
        @endif

    @endif


    {{-- ISSUES --}}
    @if ($tab === 'issues')

        <div class="section-heading">
            <h2 class="section-title">Issues</h2>
            <div class="section-rule"></div>

            @if($issues->count())
                <span class="badge badge-neutral">
                    {{ $issues->total() }} Results
                </span>
            @endif
        </div>

        @if ($issues->count() > 0)

            <div class="grid-5">

                @foreach ($issues as $issue)

                    <a href="{{ route('issues.show', $issue->id) }}"
                       class="cover-card">

                        <div class="cover-card-img">

                            @if ($issue->image)

                                <img
                                    src="{{ $issue->image }}"
                                    alt="{{ $issue->name }}"
                                />

                            @else

                                <div class="cover-card-placeholder">
                                    📄
                                </div>

                            @endif

                        </div>

                        <div class="cover-card-body">

                            <div class="cover-card-title">
                                {{ $issue->name ?? 'Untitled' }}
                            </div>

                            <div class="cover-card-meta">
                                {{ $issue->volume->name ?? 'Unknown Volume' }}

                                @if ($issue->issue_number)
                                    · #{{ $issue->issue_number }}
                                @endif
                            </div>

                        </div>

                    </a>

                @endforeach

            </div>

        @else

            <div class="card" style="padding:4rem 2rem; text-align:center;">
                <p class="text-muted">
                    No issues found in the archive.
                </p>
            </div>

        @endif

        {{-- PAGINATION --}}
        @if ($issues->hasPages())
            <div class="mt-4">
                {{ $issues->links() }}
            </div>
        @endif

    @endif

</div>

@endsection

@push('scripts')
<script>
    function setTab(tab) {
        document.getElementById('activeTabInput').value = tab;
    }
</script>
@endpush