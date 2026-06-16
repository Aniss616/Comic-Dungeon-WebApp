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
                    'volumes'    => 'Volumes',
                    'issues'     => 'Issues',
                    'arcs'       => 'Story Arcs',
                    'teams'      => 'Teams',
                    'locations'  => 'Locations',
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

                    <a href="{{ route('characters.show', $character->id) }}" class="cover-card">

                        <div class="cover-card-img" style="aspect-ratio:1/1;">

                            @if ($character->image)
                                <img
                                    src="{{ $character->image }}"
                                    alt="{{ $character->name }}"
                                    class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-300"
                                />
                            @else
                                <div class="cover-card-placeholder">?</div>
                            @endif

                        </div>

                        <div class="cover-card-body">
                            <div class="cover-card-title">{{ $character->name }}</div>
                            <div class="cover-card-meta">{{ $character->publisher ?? 'Unknown Publisher' }}</div>
                        </div>

                    </a>

                @endforeach

            </div>

        @else

            <div class="card" style="padding:4rem 2rem; text-align:center;">
                <p class="text-muted">No characters found in the archive.</p>
            </div>

        @endif

        @if ($characters->hasPages())
            <div class="mt-4">{{ $characters->links() }}</div>
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

                    <a href="{{ route('volumes.show', $volume->id) }}" class="cover-card">

                        <div class="cover-card-img">

                            @if ($volume->cover_image)
                                <img src="{{ $volume->cover_image }}" alt="{{ $volume->name }}" />
                            @else
                                <div class="cover-card-placeholder">No Image</div>
                            @endif

                        </div>

                        <div class="cover-card-body">
                            <div class="cover-card-title">{{ $volume->name }}</div>
                            <div class="cover-card-meta">{{ $volume->publisher->name ?? 'Unknown Publisher' }}</div>
                        </div>

                    </a>

                @endforeach

            </div>

        @else

            <div class="card" style="padding:4rem 2rem; text-align:center;">
                <p class="text-muted">No volumes found in the archive.</p>
            </div>

        @endif

        @if ($volumes->hasPages())
            <div class="mt-4">{{ $volumes->links() }}</div>
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

                    <a href="{{ route('issues.show', $issue->id) }}" class="cover-card">

                        <div class="cover-card-img">

                            @if ($issue->image)
                                <img src="{{ $issue->image }}" alt="{{ $issue->name }}" />
                            @else
                                <div class="cover-card-placeholder">No Image</div>
                            @endif

                        </div>

                        <div class="cover-card-body">
                            <div class="cover-card-title">{{ $issue->name ?? 'Untitled' }}</div>
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
                <p class="text-muted">No issues found in the archive.</p>
            </div>

        @endif

        @if ($issues->hasPages())
            <div class="mt-4">{{ $issues->links() }}</div>
        @endif

    @endif


    {{-- STORY ARCS --}}
    @if ($tab === 'arcs')

        <style>
            .arc-card {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                padding: 1.5rem;
                text-decoration: none;
                gap: 0.75rem;
                transition: border-color 0.2s ease, transform 0.2s ease, background 0.2s ease;
            }
            .arc-card:hover {
                border-color: rgba(192, 57, 43, 0.45);
                background: var(--sl-red-dim);
                transform: translateY(-3px);
            }
        </style>

        <div class="section-heading">
            <h2 class="section-title">Story Arcs</h2>
            <div class="section-rule"></div>

            @if($storyArcs->count())
                <span class="badge badge-red">
                    {{ $storyArcs->total() }} Results
                </span>
            @endif
        </div>

        @if ($storyArcs->count() > 0)

            <div class="grid-3">

                @foreach ($storyArcs as $arc)

                    <a href="{{ route('story-arcs.show', $arc->id) }}" class="card arc-card">

                        <div style="font-family:var(--font-display); font-size:1.15rem;
                                    font-weight:800; text-transform:uppercase;
                                    color:var(--sl-text); letter-spacing:0.04em; line-height:1.2;">
                            {{ $arc->name }}
                        </div>

                        <div style="display:flex; align-items:center; gap:0.5rem;">
                            <span class="badge badge-amber">
                                {{ $arc->issues_count }} {{ Str::plural('issue', $arc->issues_count) }}
                            </span>
                        </div>

                    </a>

                @endforeach

            </div>

        @else

            <div class="card" style="padding:4rem 2rem; text-align:center;">
                <p class="text-muted">No story arcs found in the archive.</p>
            </div>

        @endif

        @if ($storyArcs->hasPages())
            <div class="mt-4">{{ $storyArcs->links() }}</div>
        @endif

    @endif
    {{-- TEAMS --}}
    @if ($tab === 'teams')
        <style>
            .team-card {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                padding: 1.5rem;
                text-decoration: none;
                gap: 0.75rem;
                transition: border-color 0.2s ease, transform 0.2s ease, background 0.2s ease;
            }
            .team-card:hover {
                border-color: rgba(192, 57, 43, 0.45);
                background: var(--sl-red-dim);
                transform: translateY(-3px);
            }
        </style>
        <div class="section-heading">
            <h2 class="section-title">Teams</h2>
            <div class="section-rule"></div>
            @if($teams->count())
                <span class="badge badge-red">
                    {{ $teams->total() }} Results
                </span>
            @endif
        </div>
        @if ($teams->count() > 0)
            <div class="grid-3">
                @foreach ($teams as $team)
                    <a href="{{ route('teams.show', $team->id) }}" class="card team-card">
                        <div style="font-family:var(--font-display); font-size:1.15rem;
                                    font-weight:800; text-transform:uppercase;
                                    color:var(--sl-text); letter-spacing:0.04em; line-height:1.2;">
                            {{ $team->name }}
                        </div>
                        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                            <span class="badge badge-amber">
                                {{ $team->characters_count }} {{ Str::plural('member', $team->characters_count) }}
                            </span>
                            <span class="badge badge-neutral">
                                {{ $team->issues_count }} {{ Str::plural('issue', $team->issues_count) }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="card" style="padding:4rem 2rem; text-align:center;">
                <p class="text-muted">No teams found in the archive.</p>
            </div>
        @endif
        @if ($teams->hasPages())
            <div class="mt-4">{{ $teams->links() }}</div>
        @endif
    @endif
    {{-- LOCATIONS --}}
    @if ($tab === 'locations')
        <style>
            .location-card {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                padding: 1.5rem;
                text-decoration: none;
                gap: 0.75rem;
                transition: border-color 0.2s ease, transform 0.2s ease, background 0.2s ease;
            }
            .location-card:hover {
                border-color: rgba(192, 57, 43, 0.45);
                background: var(--sl-red-dim);
                transform: translateY(-3px);
            }
        </style>
        <div class="section-heading">
            <h2 class="section-title">Locations</h2>
            <div class="section-rule"></div>
            @if($locations->count())
                <span class="badge badge-red">{{ $locations->total() }} Results</span>
            @endif
        </div>
        @if ($locations->count() > 0)
            <div class="grid-3">
                @foreach ($locations as $location)
                    <a href="{{ route('locations.show', $location->id) }}" class="card location-card">
                        <div style="font-family:var(--font-display); font-size:1.15rem;
                                    font-weight:800; text-transform:uppercase;
                                    color:var(--sl-text); letter-spacing:0.04em; line-height:1.2;">
                            {{ $location->name }}
                        </div>
                        <div style="display:flex; align-items:center; gap:0.5rem;">
                            <span class="badge badge-amber">
                                {{ $location->issues_count }} {{ Str::plural('issue', $location->issues_count) }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="card" style="padding:4rem 2rem; text-align:center;">
                <p class="text-muted">No locations found in the archive.</p>
            </div>
        @endif
        @if ($locations->hasPages())
            <div class="mt-4">{{ $locations->links() }}</div>
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