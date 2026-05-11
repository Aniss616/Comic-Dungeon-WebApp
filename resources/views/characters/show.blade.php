@extends('layouts.app')

@section('title', $character->name)

@section('content')

<div class="space-y-8">

    {{-- BACK --}}
    <a href="{{ route('explore') }}?tab=characters"
       class="section-link">
        Back to Characters
    </a>

    {{-- HERO --}}
    <div class="card" style="padding:2rem;">
        <div class="flex flex-wrap gap-2" style="gap:2rem; align-items:flex-start;">

            {{-- IMAGE --}}
            <div style="width:240px; flex-shrink:0;">
                @if ($character->image)
                    <img
                        src="{{ $character->image }}"
                        alt="{{ $character->name }}"
                        style="
                            width:100%;
                            aspect-ratio:1/1;
                            object-fit:cover;
                            object-position:top;
                            border-radius:var(--sl-radius-lg);
                            border:1px solid var(--sl-border-md);
                            background:var(--sl-surface);
                        "
                    />
                @else
                    <div style="
                        width:100%;
                        aspect-ratio:1/1;
                        border-radius:var(--sl-radius-lg);
                        background:var(--sl-surface);
                        border:1px solid var(--sl-border);
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        font-family:var(--font-display);
                        font-size:3rem;
                        color:var(--sl-faint);
                    ">
                        ?
                    </div>
                @endif

                {{-- USER ACTIONS --}}
                <div class="mt-2">
                    @auth
                        @php
                            $isFavourite = Auth::user()->favouriteCharacters()->where('character_id', $character->id)->exists();
                        @endphp

                        <button
                            id="charFavBtn"
                            onclick="toggleFavouriteCharacter({{ $character->id }})"
                            class="btn {{ $isFavourite ? 'btn-primary' : 'btn-ghost' }}"
                            style="width:100%; justify-content:center;"
                        >
                            {{ $isFavourite ? 'Favourited' : 'Favourite' }}
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                           class="section-link"
                           style="display:block; margin-top:0.75rem;">
                            Sign in to favourite
                        </a>
                    @endauth
                </div>
            </div>

            {{-- INFO --}}
            <div style="flex:1; min-width:300px;">

                <span class="page-header-eyebrow">Character</span>

                <h1 class="page-header-title" style="font-size:clamp(2.5rem,5vw,4rem);">
                    {{ $character->name }}
                </h1>

                @if ($character->real_name)
                    <p class="mt-1 text-muted">
                        Real Name:
                        <span style="color:var(--sl-text); font-weight:600;">
                            {{ $character->real_name }}
                        </span>
                    </p>
                @endif

                @if ($character->aliases && count($character->aliases) > 0)
                    <p class="mt-1 text-faint" style="font-size:13px;">
                        {{ implode(', ', $character->aliases) }}
                    </p>
                @endif

                {{-- DETAILS --}}
                <div class="grid-4 mt-4">

                    <div class="card" style="padding:1rem;">
                        <div class="stat-label">Gender</div>
                        <div style="margin-top:0.4rem; font-weight:600;">
                            {{ $character->gender_label }}
                        </div>
                    </div>

                    @if ($character->origin)
                        <div class="card" style="padding:1rem;">
                            <div class="stat-label">Origin</div>
                            <div style="margin-top:0.4rem; font-weight:600;">
                                {{ $character->origin }}
                            </div>
                        </div>
                    @endif

                    @if ($character->birth)
                        <div class="card" style="padding:1rem;">
                            <div class="stat-label">Birth</div>
                            <div style="margin-top:0.4rem; font-weight:600;">
                                {{ $character->birth }}
                            </div>
                        </div>
                    @endif

                    @if ($character->publisher)
                        <div class="card" style="padding:1rem;">
                            <div class="stat-label">Publisher</div>
                            <div style="margin-top:0.4rem; font-weight:600;">
                                {{ $character->publisher }}
                            </div>
                        </div>
                    @endif
                </div>

                {{-- FIRST APPEARANCE --}}
                <div class="grid-2 mt-4">

                    @if ($firstAppearance)
                        <div class="card" style="padding:1.25rem;">
                            <div class="badge badge-red mb-2">
                                First Appearance
                            </div>

                            <a href="{{ route('issues.show', $firstAppearance->id) }}"
                               style="
                                    display:block;
                                    color:var(--sl-text);
                                    text-decoration:none;
                                    font-weight:600;
                               ">
                                {{ $firstAppearance->name ?? 'Untitled' }}
                                #{{ $firstAppearance->issue_number ?? '?' }}
                            </a>

                            @if ($firstAppearance->cover_date)
                                <p class="text-faint mt-1" style="font-size:12px;">
                                    {{ $firstAppearance->cover_date }}
                                </p>
                            @endif
                        </div>
                    @endif

                    @if ($bestStart)
                        <div class="card" style="padding:1.25rem;">
                            <div class="badge badge-amber mb-2">
                                Best Starting Issue
                            </div>

                            <a href="{{ route('issues.show', $bestStart->id) }}"
                               style="
                                    display:block;
                                    color:var(--sl-text);
                                    text-decoration:none;
                                    font-weight:600;
                               ">
                                {{ $bestStart->name ?? 'Untitled' }}
                                #{{ $bestStart->issue_number ?? '?' }}
                            </a>

                            @if ($bestStart->volume)
                                <p class="text-faint mt-1" style="font-size:12px;">
                                    {{ $bestStart->volume->name }}
                                </p>
                            @endif
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </div>

    {{-- POWERS --}}
    @if ($character->powers && count($character->powers) > 0)

        <div>
            <div class="section-heading">
                <h2 class="section-title">Powers & Abilities</h2>
                <div class="section-rule"></div>
            </div>

            <div class="card" style="padding:1.5rem;">
                <div class="flex flex-wrap gap-1">
                    @foreach ($character->powers as $index => $power)
                        <span class="badge badge-red {{ $index >= 8 ? 'hidden power-extra' : '' }}"
                              style="padding:0.45rem 0.8rem;">
                            {{ $power }}
                        </span>
                    @endforeach
                </div>

                @if (count($character->powers) > 8)
                    <button onclick="toggleExtra('power-extra', this)"
                            class="btn btn-ghost mt-3">
                        Show {{ count($character->powers) - 8 }} more
                    </button>
                @endif
            </div>
        </div>

    @endif

    {{-- RELATIONSHIPS --}}
    @if (($character->teams && count($character->teams) > 0) ||
         ($character->character_friends && count($character->character_friends) > 0) ||
         ($character->character_enemies && count($character->character_enemies) > 0))

        <div class="grid-3">

            {{-- TEAMS --}}
            @if ($character->teams && count($character->teams) > 0)
                <div class="card" style="padding:1.5rem;">
                    <div class="section-heading" style="margin-bottom:1rem;">
                        <h3 class="section-title" style="font-size:1.1rem;">Teams</h3>
                    </div>

                    <div class="flex flex-wrap gap-1">
                        @foreach ($character->teams as $index => $team)
                            <span class="badge badge-neutral {{ $index >= 5 ? 'hidden team-extra' : '' }}">
                                {{ $team['name'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- FRIENDS --}}
            @if ($character->character_friends && count($character->character_friends) > 0)
                <div class="card" style="padding:1.5rem;">
                    <div class="section-heading" style="margin-bottom:1rem;">
                        <h3 class="section-title" style="font-size:1.1rem;">Allies</h3>
                    </div>

                    <div class="flex flex-wrap gap-1">
                        @foreach ($character->character_friends as $index => $friend)
                            <span class="badge badge-amber {{ $index >= 5 ? 'hidden friend-extra' : '' }}">
                                {{ $friend['name'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ENEMIES --}}
            @if ($character->character_enemies && count($character->character_enemies) > 0)
                <div class="card" style="padding:1.5rem;">
                    <div class="section-heading" style="margin-bottom:1rem;">
                        <h3 class="section-title" style="font-size:1.1rem;">Enemies</h3>
                    </div>

                    <div class="flex flex-wrap gap-1">
                        @foreach ($character->character_enemies as $index => $enemy)
                            <span class="badge badge-red {{ $index >= 5 ? 'hidden enemy-extra' : '' }}">
                                {{ $enemy['name'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    @endif

    {{-- ISSUES --}}
    @if ($issues->count() > 0)

        <div>
            <div class="section-heading">
                <h2 class="section-title">Comic Appearances</h2>
                <div class="section-rule"></div>
            </div>

            <div class="grid-5">
                @foreach ($issues->sortBy('cover_date')->values() as $index => $issue)

                    <a href="{{ route('issues.show', $issue->id) }}"
                       class="cover-card {{ $index >= 12 ? 'hidden issue-extra' : '' }}">

                        <div class="cover-card-img">
                            @if ($issue->image)
                                <img
                                    src="{{ $issue->image }}"
                                    alt="{{ $issue->name }}"
                                />
                            @else
                                <div class="cover-card-placeholder">
                                    ISSUE
                                </div>
                            @endif
                        </div>

                        <div class="cover-card-body">
                            <div class="cover-card-title">
                                {{ $issue->name ?? 'Untitled' }}
                            </div>

                            <div class="cover-card-meta">
                                {{ $issue->volume->name ?? '' }}
                                @if ($issue->issue_number)
                                    · #{{ $issue->issue_number }}
                                @endif
                            </div>
                        </div>

                    </a>

                @endforeach
            </div>

            @if ($issues->count() > 12)
                <button
                    onclick="toggleExtra('issue-extra', this)"
                    class="btn btn-ghost mt-4 w-full"
                    style="justify-content:center;">
                    Show {{ $issues->count() - 12 }} more issues
                </button>
            @endif
        </div>

    @endif

    {{-- BIOGRAPHY --}}
    @if (count($descriptionSections) > 0)

        <div>
            <div class="section-heading">
                <h2 class="section-title">Biography</h2>
                <div class="section-rule"></div>
            </div>

            <div class="grid-2">

                @foreach ($descriptionSections as $index => $section)

                    <div class="card {{ $index >= 4 ? 'hidden bio-extra' : '' }}"
                         style="padding:1.5rem;">

                        @if ($section['title'])
                            <div class="badge badge-red mb-2">
                                {{ $section['title'] }}
                            </div>
                        @endif

                        <p class="text-muted" style="line-height:1.8;">
                            {{ Str::limit($section['content'], 300) }}
                        </p>

                        @if (strlen($section['content']) > 300)
                            <button
                                onclick="toggleSection(this)"
                                data-full="{{ e($section['content']) }}"
                                data-short="{{ e(Str::limit($section['content'], 300)) }}"
                                class="section-link mt-2"
                                style="background:none; border:none; cursor:pointer;">
                                Read more
                            </button>
                        @endif

                    </div>

                @endforeach

            </div>

            @if (count($descriptionSections) > 4)
                <button
                    onclick="toggleExtra('bio-extra', this)"
                    class="btn btn-ghost mt-4 w-full"
                    style="justify-content:center;">
                    Show {{ count($descriptionSections) - 4 }} more sections
                </button>
            @endif

        </div>

    @endif

@endsection

@push('scripts')
<script>
    function toggleExtra(className, btn) {
        const items = document.querySelectorAll('.' + className);
        const isHidden = items[0].classList.contains('hidden');

        items.forEach(el => el.classList.toggle('hidden'));

        const count = items.length;
        btn.textContent = isHidden ? 'Show less' : `Show ${count} more`;
    }

    function toggleSection(btn) {
        const p = btn.previousElementSibling;
        const isFull = btn.textContent.trim() === 'Read less';

        p.textContent = isFull ? btn.dataset.short : btn.dataset.full;
        btn.textContent = isFull ? 'Read more' : 'Read less';
    }

    async function toggleFavouriteCharacter(id) {

        const res = await fetch(`/characters/${id}/favourite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        });

        const data = await res.json();

        const btn = document.getElementById('charFavBtn');

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