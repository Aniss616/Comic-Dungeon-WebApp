@extends('layouts.app')

@section('title', $character->name)

@section('content')

<style>
    .sl-hidden { display: none !important; }
</style>

<div style="display:flex; flex-direction:column; gap:2rem;">

    {{-- BACK --}}
    <a href="{{ route('explore') }}?tab=characters"
       class="section-link">
        Back to Characters
    </a>

    {{-- HERO --}}
    <div class="card" style="padding:2rem;">
        <div style="display:flex; flex-wrap:wrap; gap:2rem; align-items:flex-start;">

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
                <div style="margin-top:0.75rem;">
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
                    <p style="margin-top:0.25rem; color:var(--sl-muted);">
                        Real Name:
                        <span style="color:var(--sl-text); font-weight:600;">
                            {{ $character->real_name }}
                        </span>
                    </p>
                @endif

                @if ($character->aliases && count($character->aliases) > 0)
                    <p style="margin-top:0.25rem; color:var(--sl-faint); font-size:13px;">
                        {{ implode(', ', $character->aliases) }}
                    </p>
                @endif

                {{-- DETAILS --}}
                <div class="grid-4" style="margin-top:1.5rem;">

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
                <div class="grid-2" style="margin-top:1.5rem;">

                    @if ($firstAppearance)
                        <div class="card" style="padding:1.25rem;">
                            <div class="badge badge-red" style="margin-bottom:0.5rem;">
                                First Appearance
                            </div>

                            <a href="{{ route('issues.show', $firstAppearance->id) }}"
                               style="display:block; color:var(--sl-text); text-decoration:none; font-weight:600;">
                                {{ $firstAppearance->name ?? 'Untitled' }}
                                #{{ $firstAppearance->issue_number ?? '?' }}
                            </a>

                            @if ($firstAppearance->cover_date)
                                <p style="color:var(--sl-faint); margin-top:0.25rem; font-size:12px;">
                                    {{ $firstAppearance->cover_date }}
                                </p>
                            @endif
                        </div>
                    @endif

                    @if ($bestStart)
                        <div class="card" style="padding:1.25rem;">
                            <div class="badge badge-amber" style="margin-bottom:0.5rem;">
                                Best Starting Issue
                            </div>

                            <a href="{{ route('issues.show', $bestStart->id) }}"
                               style="display:block; color:var(--sl-text); text-decoration:none; font-weight:600;">
                                {{ $bestStart->name ?? 'Untitled' }}
                                #{{ $bestStart->issue_number ?? '?' }}
                            </a>

                            @if ($bestStart->volume)
                                <p style="color:var(--sl-faint); margin-top:0.25rem; font-size:12px;">
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
                <div style="display:flex; flex-wrap:wrap; gap:0.4rem;">
                    @foreach ($character->powers as $index => $power)
                        <span class="badge badge-red{{ $index >= 8 ? ' sl-hidden' : '' }}"
                              data-group="power-extra"
                              style="padding:0.45rem 0.8rem;">
                            {{ $power }}
                        </span>
                    @endforeach
                </div>

                @if (count($character->powers) > 8)
                    <button onclick="toggleExtra('power-extra', this)"
                            class="btn btn-ghost"
                            style="margin-top:1rem;">
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

        <div>
            <div class="section-heading">
                <h2 class="section-title">Relationships</h2>
                <div class="section-rule"></div>
            </div>

            <div class="grid-3">

                {{-- TEAMS --}}
                @if ($character->teams && count($character->teams) > 0)
                    <div class="card" style="padding:1.5rem;">
                        <div class="section-heading" style="margin-bottom:1rem;">
                            <h3 class="section-title" style="font-size:1.1rem;">Teams</h3>
                        </div>

                        <div style="display:flex; flex-wrap:wrap; gap:0.4rem;">
                            @foreach ($character->teams as $index => $team)
                                <span class="badge badge-neutral{{ $index >= 5 ? ' sl-hidden' : '' }}"
                                      data-group="team-extra">
                                    {{ $team['name'] }}
                                </span>
                            @endforeach
                        </div>

                        @if (count($character->teams) > 5)
                            <button onclick="toggleExtra('team-extra', this)"
                                    class="btn btn-ghost"
                                    style="margin-top:1rem;">
                                Show {{ count($character->teams) - 5 }} more
                            </button>
                        @endif
                    </div>
                @endif

                {{-- ALLIES --}}
                @if ($character->character_friends && count($character->character_friends) > 0)
                    <div class="card" style="padding:1.5rem;">
                        <div class="section-heading" style="margin-bottom:1rem;">
                            <h3 class="section-title" style="font-size:1.1rem;">Allies</h3>
                        </div>

                        <div style="display:flex; flex-wrap:wrap; gap:0.4rem;">
                            @foreach ($character->character_friends as $index => $friend)
                                <span class="badge badge-amber{{ $index >= 5 ? ' sl-hidden' : '' }}"
                                      data-group="friend-extra">
                                    {{ $friend['name'] }}
                                </span>
                            @endforeach
                        </div>

                        @if (count($character->character_friends) > 5)
                            <button onclick="toggleExtra('friend-extra', this)"
                                    class="btn btn-ghost"
                                    style="margin-top:1rem;">
                                Show {{ count($character->character_friends) - 5 }} more
                            </button>
                        @endif
                    </div>
                @endif

                {{-- ENEMIES --}}
                @if ($character->character_enemies && count($character->character_enemies) > 0)
                    <div class="card" style="padding:1.5rem;">
                        <div class="section-heading" style="margin-bottom:1rem;">
                            <h3 class="section-title" style="font-size:1.1rem;">Enemies</h3>
                        </div>

                        <div style="display:flex; flex-wrap:wrap; gap:0.4rem;">
                            @foreach ($character->character_enemies as $index => $enemy)
                                <span class="badge badge-red{{ $index >= 5 ? ' sl-hidden' : '' }}"
                                      data-group="enemy-extra">
                                    {{ $enemy['name'] }}
                                </span>
                            @endforeach
                        </div>

                        @if (count($character->character_enemies) > 5)
                            <button onclick="toggleExtra('enemy-extra', this)"
                                    class="btn btn-ghost"
                                    style="margin-top:1rem;">
                                Show {{ count($character->character_enemies) - 5 }} more
                            </button>
                        @endif
                    </div>
                @endif

            </div>
        </div>

    @endif

    {{-- ISSUES --}}
    @if ($issues->count() > 0)

        @php $sortedIssues = $issues->sortBy('cover_date')->values(); @endphp

        <div>
            <div class="section-heading">
                <h2 class="section-title">Comic Appearances</h2>
                <div class="section-rule"></div>
            </div>

            <div class="grid-5">
                @foreach ($sortedIssues as $index => $issue)

                    <a href="{{ route('issues.show', $issue->id) }}"
                       class="cover-card{{ $index >= 10 ? ' sl-hidden' : '' }}"
                       data-group="issue-extra">

                        <div class="cover-card-img">
                            @if ($issue->image)
                                <img src="{{ $issue->image }}" alt="{{ $issue->name }}" />
                            @else
                                <div class="cover-card-placeholder">ISSUE</div>
                            @endif
                        </div>

                        <div class="cover-card-body">
                            <div class="cover-card-title">
                                {{ $issue->name ?? 'Untitled' }}
                            </div>
                            <div class="cover-card-meta">
                                {{ $issue->volume->name ?? '' }}
                                @if ($issue->issue_number) · #{{ $issue->issue_number }} @endif
                            </div>
                        </div>

                    </a>

                @endforeach
            </div>

            @if ($sortedIssues->count() > 10)
                <button
                    onclick="toggleExtra('issue-extra', this)"
                    class="btn btn-ghost"
                    style="margin-top:1.5rem; width:100%; justify-content:center;">
                    Show {{ $sortedIssues->count() - 10 }} more issues
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

                    @php
                        $decoded = html_entity_decode($section['content'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        $short   = Str::limit($decoded, 300);
                    @endphp

                    <div class="card{{ $index >= 4 ? ' sl-hidden' : '' }}"
                         data-group="bio-extra"
                         style="padding:1.5rem;">

                        @if ($section['title'])
                            <div class="badge badge-red" style="margin-bottom:0.5rem;">
                                {{ $section['title'] }}
                            </div>
                        @endif

                        <p style="color:var(--sl-muted); line-height:1.8;">
                            {{ $short }}
                        </p>

                        @if (strlen($decoded) > 300)
                            <button
                                onclick="toggleSection(this)"
                                data-full="{{ e($decoded) }}"
                                data-short="{{ e($short) }}"
                                class="section-link"
                                style="margin-top:0.5rem; background:none; border:none; cursor:pointer;">
                                Read more
                            </button>
                        @endif

                    </div>

                @endforeach

            </div>

            @if (count($descriptionSections) > 4)
                <button
                    onclick="toggleExtra('bio-extra', this)"
                    class="btn btn-ghost"
                    style="margin-top:1.5rem; width:100%; justify-content:center;">
                    Show {{ count($descriptionSections) - 4 }} more sections
                </button>
            @endif

        </div>

    @endif

</div>

@endsection

@push('scripts')
<script>
    function toggleExtra(group, btn, initialCount = 5) {
    const items = document.querySelectorAll(`[data-group="${group}"]`);

    // current state
    const expanded = btn.dataset.expanded === 'true';

    if (!expanded) {
        // SHOW ALL
        items.forEach(el => el.classList.remove('sl-hidden'));

        btn.dataset.expanded = 'true';
        btn.textContent = 'Show less';

    } else {
        // SHOW ONLY INITIAL COUNT
        items.forEach((el, index) => {
            if (index >= initialCount) {
                el.classList.add('sl-hidden');
            }
        });

        btn.dataset.expanded = 'false';

        const hiddenCount = Math.max(items.length - initialCount, 0);

        if (group === 'issue-extra') {
            btn.textContent = `Show ${hiddenCount} more issues`;
        } else if (group === 'bio-extra') {
            btn.textContent = `Show ${hiddenCount} more sections`;
        } else {
            btn.textContent = `Show ${hiddenCount} more`;
        }
    }
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
        } else {
            btn.textContent = 'Favourite';
            btn.className = 'btn btn-ghost';
        }
        btn.style.width = '100%';
        btn.style.justifyContent = 'center';
    }
</script>
@endpush