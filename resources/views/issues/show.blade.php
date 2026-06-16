@extends('layouts.app')

@section('title', $issue->name ?? 'Issue')

@section('content')

<div class="space-y-8">

    {{-- BACK --}}
    <a href="{{ route('volumes.show', $issue->volume_id) }}"
       class="section-link">
        Back to {{ $issue->volume->name ?? 'Volume' }}
    </a>

    {{-- HERO --}}
    <div class="card" style="padding:2rem;">

        <div class="flex flex-wrap gap-2"
             style="gap:2rem; align-items:flex-start;">

            {{-- COVER --}}
            <div style="width:220px; flex-shrink:0;">

                @if ($issue->image)

                    <img
                        src="{{ $issue->image }}"
                        alt="{{ $issue->name }}"
                        style="
                            width:100%;
                            border-radius:var(--sl-radius-lg);
                            border:1px solid var(--sl-border-md);
                            object-fit:cover;
                            background:var(--sl-surface);
                        "
                    />

                @else

                    <div style="
                        width:100%;
                        aspect-ratio:2/3;
                        border-radius:var(--sl-radius-lg);
                        border:1px solid var(--sl-border);
                        background:var(--sl-surface);
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        font-family:var(--font-display);
                        font-size:2rem;
                        letter-spacing:0.08em;
                        color:var(--sl-faint);
                    ">
                        ISSUE
                    </div>

                @endif

                {{-- STREAMING --}}
                @if($issue->site_detail_url)

                    @php
                        $publisherName = strtolower($issue->volume->publisher->name ?? '');
                        $issueQuery = urlencode(($issue->volume->name ?? '') . ' ' . ($issue->issue_number ?? ''));

                        $url = $issue->site_detail_url;
                        $label = "Comic Vine";
                        $btnClass = 'btn btn-ghost';

                        if (str_contains($publisherName, 'marvel')) {
                            $url = "https://www.marvel.com/search?category=comic&options%5B0%5D=unlimited&query=" . $issueQuery;
                            $label = "Marvel";
                            $btnClass = 'btn btn-primary';
                        } elseif (str_contains($publisherName, 'dc comics')) {
                            $url = "https://www.dcuniverseinfinite.com/";
                            $label = "DC Universe";
                            $btnClass = 'btn btn-primary';
                        } elseif (str_contains($publisherName, 'image')) {
                            $url = "https://imagecomics.com/";
                            $label = "Image Comics";
                            $btnClass = 'btn btn-ghost';
                        }
                    @endphp

                    <div class="mt-2">
                        <a href="{{ $url }}"
                           target="_blank"
                           class="{{ $btnClass }}"
                           style="
                                width:100%;
                                justify-content:center;
                                display:flex;
                           ">
                            Read on {{ $label }}
                        </a>

                        <p class="text-faint mt-1"
                           style="
                                font-size:11px;
                                text-align:center;
                                line-height:1.5;
                           ">
                            Support your local comic shop
                        </p>
                    </div>

                @endif

                {{-- USER ACTIONS --}}
                @auth

                    @php
                        $isRead = Auth::user()
                            ->reads()
                            ->where('issue_id', $issue->id)
                            ->exists();

                        $isFavourite = Auth::user()
                            ->favourites()
                            ->where('issue_id', $issue->id)
                            ->exists();
                    @endphp

                    <div class="mt-2 flex flex-wrap"
                         style="gap:0.75rem;">

                        <button
                            id="readBtn"
                            onclick="toggleRead({{ $issue->id }})"
                            class="btn {{ $isRead ? 'btn-primary' : 'btn-ghost' }}"
                            style="
                                width:100%;
                                justify-content:center;
                            ">
                            {{ $isRead ? 'Read' : 'Mark as Read' }}
                        </button>

                        <button
                            id="favBtn"
                            onclick="toggleFavouriteIssue({{ $issue->id }})"
                            class="btn {{ $isFavourite ? 'btn-primary' : 'btn-ghost' }}"
                            style="
                                width:100%;
                                justify-content:center;
                            ">
                            {{ $isFavourite ? 'Favourited' : 'Favourite' }}
                        </button>

                    </div>

                @else

                    <a href="{{ route('login') }}"
                       class="section-link"
                       style="display:block; margin-top:1rem;">
                        Sign in to track this issue
                    </a>

                @endauth

            </div>

            {{-- INFO --}}
            <div style="flex:1; min-width:320px;">

                <span class="page-header-eyebrow">

                    @if ($issue->volume)

                        <a href="{{ route('volumes.show', $issue->volume->id) }}"
                           style="color:inherit; text-decoration:none;">
                            {{ $issue->volume->name }}
                        </a>

                        @if ($issue->volume->publisher)
                            · {{ $issue->volume->publisher->name }}
                        @endif

                    @endif

                </span>

                <h1 class="page-header-title"
                    style="font-size:clamp(2.5rem,5vw,4rem);">
                    {{ $issue->name ?? 'Untitled' }}
                </h1>

                <p class="page-header-sub">
                    Issue
                    <span style="color:var(--sl-text); font-weight:600;">
                        #{{ $issue->issue_number ?? '?' }}
                    </span>
                </p>

                {{-- STATS --}}
                <div class="flex flex-wrap gap-2 mt-4">

                    @if ($issue->cover_date)

                        <div class="card"
                             style="
                                padding:1rem 1.3rem;
                                min-width:150px;
                                text-align:center;
                             ">

                            <div class="stat-label mb-1">
                                Cover Date
                            </div>

                            <div style="
                                font-family:var(--font-display);
                                font-size:1.35rem;
                                font-weight:800;
                                color:var(--sl-text);
                            ">
                                {{ $issue->cover_date }}
                            </div>

                        </div>

                    @endif

                    @if ($issue->store_date)

                        <div class="card"
                             style="
                                padding:1rem 1.3rem;
                                min-width:150px;
                                text-align:center;
                             ">

                            <div class="stat-label mb-1">
                                Store Date
                            </div>

                            <div style="
                                font-family:var(--font-display);
                                font-size:1.35rem;
                                font-weight:800;
                                color:var(--sl-text);
                            ">
                                {{ $issue->store_date }}
                            </div>

                        </div>

                    @endif

                    <div class="card"
                         style="
                            padding:1rem 1.3rem;
                            min-width:140px;
                            text-align:center;
                         ">

                        <div class="stat-number">
                            {{ $issue->characters->count() }}
                        </div>

                        <div class="stat-label">
                            Characters
                        </div>

                    </div>

                    <div class="card"
                         style="
                            padding:1rem 1.3rem;
                            min-width:140px;
                            text-align:center;
                         ">

                        <div class="stat-number">
                            {{ $issue->people->count() }}
                        </div>

                        <div class="stat-label">
                            Credits
                        </div>

                    </div>

                </div>

                {{-- STORY ARCS --}}
                @if ($issue->story_arc_credits && count($issue->story_arc_credits) > 0)
                    <div class="mt-4">
                        <div class="section-heading" style="margin-bottom:1rem;">
                            <h3 class="section-title" style="font-size:1rem;">Story Arcs</h3>
                            <div class="section-rule"></div>
                        </div>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($issue->story_arc_credits as $arc)
                                @php
                                    $arcModel = \App\Models\StoryArc::where('comic_vine_id', $arc['id'])->first();
                                @endphp

                            @if ($arcModel)
                                <a href="{{ route('story-arcs.show', $arcModel->id) }}"
                                    class="badge badge-red"
                                    style="text-decoration:none; transition: background 0.2s ease, border-color 0.2s ease;">
                                        {{ $arc['name'] }}
                                </a>
                            @else
                                <span class="badge badge-red">{{ $arc['name'] }}</span>
                            @endif
                             @endforeach
                            </div>
                        </div>
                @endif

                {{-- TEAMS --}}
                @if ($linkedTeams->isNotEmpty())

                    <div class="mt-4">

                        <div class="section-heading"
                             style="margin-bottom:1rem;">

                            <h3 class="section-title"
                                style="font-size:1rem;">
                                Teams
                            </h3>

                            <div class="section-rule"></div>

                        </div>

                        <div class="flex flex-wrap gap-1">

                            @foreach($linkedTeams as $t)
                                @if($t['team'])
                                    <a href="{{ route('teams.show', $t['team']->id) }}" class="badge badge-amber">{{ $t['name'] }}</a>
                                @else
                                     <span class="badge badge-amber">{{ $t['name'] }}</span>
                                @endif
                            @endforeach

                        </div>

                    </div>

                @endif

                {{-- LOCATIONS --}}
                @if ($issue->locations && count($issue->locations) > 0)

                    <div class="mt-4">

                        <div class="section-heading"
                             style="margin-bottom:1rem;">

                            <h3 class="section-title"
                                style="font-size:1rem;">
                                Locations
                            </h3>

                            <div class="section-rule"></div>

                        </div>

                        <div class="flex flex-wrap gap-1">

                            @foreach ($issue->locations as $location)

                                <span class="badge badge-amber">
                                    {{ $location['name'] }}
                                </span>

                            @endforeach

                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>

    {{-- STORY --}}
    @if (count($descriptionSections) > 0)

        <div>

            <div class="section-heading">
                <h2 class="section-title">Story</h2>
                <div class="section-rule"></div>
            </div>

            <div class="grid-2">

                @foreach ($descriptionSections as $section)

                    <div class="card"
                         style="padding:1.5rem;">

                        @if ($section['title'])

                            <div class="badge badge-red mb-2">
                                {{ $section['title'] }}
                            </div>

                        @endif

                        <p class="text-muted"
                           style="line-height:1.8;">

                            {{ Str::limit($section['content'], 300) }}

                        </p>

                        @if (strlen($section['content']) > 300)

                            <button
                                onclick="toggleSection(this)"
                                data-full="{{ e($section['content']) }}"
                                data-short="{{ e(Str::limit($section['content'], 300)) }}"
                                class="section-link mt-2"
                                style="
                                    background:none;
                                    border:none;
                                    cursor:pointer;
                                ">
                                Read more
                            </button>

                        @endif

                    </div>

                @endforeach

            </div>

        </div>

    @endif

    {{-- CHARACTERS --}}
    @if ($issue->characters->count() > 0)

        <div>

            <div class="section-heading">

                <h2 class="section-title">
                    Characters
                </h2>

                <div class="section-rule"></div>

                <span class="text-faint"
                      style="
                        font-family:var(--font-display);
                        font-size:0.8rem;
                        letter-spacing:0.08em;
                        text-transform:uppercase;
                      ">
                    {{ $issue->characters->count() }}
                </span>

            </div>

            <div class="grid-5">

                @foreach ($issue->characters as $character)

                    <a href="{{ route('characters.show', $character->id) }}"
                       class="cover-card">

                        <div class="cover-card-img"
                             style="aspect-ratio:1/1;">

                            @if ($character->image)

                                <img
                                    src="{{ $character->image }}"
                                    alt="{{ $character->name }}"
                                    style="object-position: top;"
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

                            @if ($character->real_name)

                                <div class="cover-card-meta">
                                    {{ $character->real_name }}
                                </div>

                            @endif

                        </div>

                    </a>

                @endforeach

            </div>

        </div>

    @endif

    {{-- CREDITS --}}
    @if ($issue->people->count() > 0)

        <div>

            <div class="section-heading">

                <h2 class="section-title">
                    Credits
                </h2>

                <div class="section-rule"></div>

                <span class="text-faint"
                      style="
                        font-family:var(--font-display);
                        font-size:0.8rem;
                        letter-spacing:0.08em;
                        text-transform:uppercase;
                      ">
                    {{ $issue->people->count() }}
                </span>

            </div>

            <div class="grid-4">

                @foreach ($issue->people as $person)

                    <div class="card"
                         style="padding:1rem 1.2rem;">

                        <div style="
                            font-weight:600;
                            color:var(--sl-text);
                            margin-bottom:0.3rem;
                        ">
                            {{ $person->name }}
                        </div>

                        @if ($person->pivot->role)

                            <div class="badge badge-red">
                                {{ $person->pivot->role }}
                            </div>

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

    function toggleSection(btn) {

        const p = btn.previousElementSibling;

        const isFull =
            btn.textContent.trim() === 'Read less';

        p.textContent =
            isFull
                ? btn.dataset.short
                : btn.dataset.full;

        btn.textContent =
            isFull
                ? 'Read more'
                : 'Read less';
    }

    async function toggleRead(id) {

        const res = await fetch(`/issues/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        });

        const data = await res.json();

        const btn = document.getElementById('readBtn');

        if (data.status === 'read') {

            btn.textContent = 'Read';
            btn.className = 'btn btn-primary';
            btn.style.width = '100%';
            btn.style.justifyContent = 'center';

        } else {

            btn.textContent = 'Mark as Read';
            btn.className = 'btn btn-ghost';
            btn.style.width = '100%';
            btn.style.justifyContent = 'center';
        }
    }

    async function toggleFavouriteIssue(id) {

        const res = await fetch(`/issues/${id}/favourite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        });

        const data = await res.json();

        const btn = document.getElementById('favBtn');

        if (data.status === 'favourited') {

            btn.textContent = 'Favourited';
            btn.className = 'btn btn-primary';
            btn.style.width = '100%';
            btn.style.justifyContent = 'center';

        } else {

            btn.textContent = 'Favourite';
            btn.className = 'btn btn-ghost';
            btn.style.width = '100%';
            btn.style.justifyContent = 'center';
        }
    }

</script>
@endpush