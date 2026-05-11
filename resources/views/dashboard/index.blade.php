@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="page-header">
    <span class="page-header-eyebrow">Control Panel</span>
    <h1 class="page-header-title">Dashboard</h1>
    <p class="page-header-sub">
        Import data, manage the archive, and monitor your Comic Dungeon database.
    </p>
</div>

{{-- STATS --}}
<div class="grid-5 mb-4">

    @foreach ([
        ['label' => 'Characters', 'value' => $stats['characters'], 'color' => 'badge-red'],
        ['label' => 'Volumes',    'value' => $stats['volumes'],    'color' => 'badge-amber'],
        ['label' => 'Issues',     'value' => $stats['issues'],     'color' => 'badge-neutral'],
        ['label' => 'Publishers', 'value' => $stats['publishers'], 'color' => 'badge-neutral'],
        ['label' => 'People',     'value' => $stats['people'],     'color' => 'badge-neutral'],
    ] as $stat)

        <div class="card" style="padding:1.25rem; text-align:center;">
            <div class="stat-number">{{ $stat['value'] }}</div>
            <div class="stat-label">{{ $stat['label'] }}</div>
        </div>

    @endforeach

</div>

{{-- IMPORT PANEL --}}
<div class="card" style="padding:1.5rem; margin-bottom:2rem;">

    <div class="section-heading">
        <h2 class="section-title">Import Data</h2>
        <div class="section-rule"></div>
    </div>

    <div class="grid-2">

        {{-- Import Issues --}}
        <div class="card" style="padding:1rem;">
            <div class="mb-2 text-muted" style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em;">
                Import Issues by Volume ID
            </div>

            <input type="number" id="volumeIdInput" class="search-input mb-2" placeholder="Volume ID">

            <button onclick="importIssues()" class="btn btn-primary w-full">
                Import Issues
            </button>

            <p id="importIssuesMsg" class="text-faint mt-1"></p>
        </div>

        {{-- Import Volume --}}
        <div class="card" style="padding:1rem;">
            <div class="mb-2 text-muted" style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em;">
                Import Volume
            </div>

            <input type="number" id="singleVolumeInput" class="search-input mb-2" placeholder="Volume ID">

            <button onclick="importVolume()" class="btn btn-primary w-full">
                Import Volume
            </button>

            <p id="importVolumeMsg" class="text-faint mt-1"></p>
        </div>

        {{-- Import Characters --}}
        <div class="card" style="padding:1rem;">
            <div class="mb-2 text-muted" style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em;">
                Import Characters
            </div>

            <div class="flex gap-2">
                <input id="charLimit" type="number" class="search-input" placeholder="Limit">
                <input id="charOffset" type="number" class="search-input" placeholder="Offset">
            </div>

            <button onclick="importCharacters()" class="btn btn-primary w-full mt-2">
                Import Characters
            </button>

            <p id="importCharsMsg" class="text-faint mt-1"></p>
        </div>

        {{-- Import People --}}
        <div class="card" style="padding:1rem;">
            <div class="mb-2 text-muted" style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em;">
                Import People
            </div>

            <div class="flex gap-2">
                <input id="peopleLimit" type="number" class="search-input" placeholder="Limit">
                <input id="peopleOffset" type="number" class="search-input" placeholder="Offset">
            </div>

            <button onclick="importPeople()" class="btn btn-primary w-full mt-2">
                Import People
            </button>

            <p id="importPeopleMsg" class="text-faint mt-1"></p>
        </div>

    </div>
</div>

{{-- COMIC VINE SEARCH --}}
<div class="card" style="padding:1.5rem; margin-bottom:2rem;">

    <div class="section-heading">
        <h2 class="section-title">Comic Vine Search</h2>
        <div class="section-rule"></div>
    </div>

    <div class="flex gap-2 mb-2">
        <input type="text" id="comicVineSearch" class="search-input" placeholder="Search characters, volumes, heroes...">
        <button onclick="searchComicVine()" class="btn btn-primary">Search</button>
    </div>

    <p id="searchMsg" class="text-faint mb-2"></p>

    <div id="searchResults"></div>

</div>

{{-- RANDOM CHARACTER --}}
<div class="card" style="padding:1.5rem; margin-bottom:2rem;">

    <div class="section-heading">
        <h2 class="section-title">Random Character</h2>
        <div class="section-rule"></div>
    </div>

    <button onclick="randomCharacter()" class="btn btn-primary">
        Roll Character
    </button>

    <p id="randomMsg" class="text-faint mt-1"></p>

    <div id="randomResult" class="mt-3 hidden">

        <div class="char-card">
            <img id="randomImage" class="char-avatar" />
            <div class="char-info">
                <div id="randomName" class="char-name"></div>
                <div id="randomDesc" class="char-meta"></div>
            </div>
        </div>

        <div class="mt-2 text-muted" style="font-size:12px; text-transform:uppercase;">
            First Appearance
        </div>
        <div id="randomFirstAppearance" class="text-muted"></div>

        <div class="mt-2 text-muted" style="font-size:12px; text-transform:uppercase;">
            Best Starting Issue
        </div>
        <div id="randomBestStart" class="text-muted"></div>

        <div class="divider"></div>

        <div id="readingPath"></div>
    </div>

</div>

{{-- RECENT --}}
<div class="grid-2">

    <div class="card" style="padding:1.25rem;">
        <div class="section-heading">
            <h2 class="section-title">Recent Volumes</h2>
            <div class="section-rule"></div>
        </div>

        @forelse ($recentVolumes as $volume)
            <div class="char-card">
                <img class="char-avatar" src="{{ $volume->cover_image }}">
                <div class="char-info">
                    <div class="char-name">{{ $volume->name }}</div>
                    <div class="char-meta">{{ $volume->publisher->name ?? 'Unknown Publisher' }}</div>
                </div>
            </div>
        @empty
            <p class="text-faint">No volumes imported yet.</p>
        @endforelse
    </div>

    <div class="card" style="padding:1.25rem;">
        <div class="section-heading">
            <h2 class="section-title">Recent Characters</h2>
            <div class="section-rule"></div>
        </div>

        @forelse ($recentCharacters as $character)
            <div class="char-card">
                <img class="char-avatar" src="{{ $character->image }}">
                <div class="char-info">
                    <div class="char-name">{{ $character->name }}</div>
                    <div class="char-meta">{{ $character->description ?? 'No description' }}</div>
                </div>
            </div>
        @empty
            <p class="text-faint">No characters imported yet.</p>
        @endforelse
    </div>

</div>

@endsection