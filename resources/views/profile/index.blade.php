@extends('layouts.app')

@section('title', 'Profile')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header">
    <span class="page-header-eyebrow">User Profile</span>
    <h1 class="page-header-title">{{ $user->username }}</h1>
    <p class="page-header-sub">{{ $user->email }}</p>
</div>

<div class="grid-4" style="grid-template-columns: 300px 1fr; gap: 2rem; align-items: start;">

    {{-- ─────────────────────────────
        SIDEBAR
    ───────────────────────────── --}}
    <aside class="flex" style="flex-direction: column; gap: 1.25rem; position: sticky; top: 90px;">

        {{-- PROFILE CARD --}}
        <div class="card">
            <div style="padding: 1.5rem; text-align: center;">

                <div style="width: 90px; height: 90px; margin: 0 auto 1rem; border-radius: 50%; background: var(--sl-surface); border: 1px solid var(--sl-border-md); display:flex; align-items:center; justify-content:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 24 24" fill="currentColor" style="color: var(--sl-faint);">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                    </svg>
                </div>

                <div class="section-title" style="font-size: 1.2rem;">
                    {{ $user->username }}
                </div>

                <div class="text-muted" style="font-size: 12px; margin-top: 0.5rem;">
                    {{ $user->email }}
                </div>

            </div>
        </div>

        {{-- STATS --}}
        <div class="card">

            <div style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--sl-border);">
                <span class="section-link">Collection Stats</span>
            </div>

            <div>

                <div style="padding: 1rem; display:flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div class="stat-label">Read</div>
                    </div>
                    <div class="stat-number" style="font-size: 1.6rem;">
                        {{ $readIssues->count() }}
                    </div>
                </div>

                <div style="padding: 1rem; display:flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--sl-border);">
                    <div>
                        <div class="stat-label">Favorites</div>
                    </div>
                    <div class="stat-number" style="font-size: 1.6rem;">
                        {{ $favouriteIssues->count() }}
                    </div>
                </div>

                <div style="padding: 1rem; display:flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--sl-border);">
                    <div>
                        <div class="stat-label">Characters</div>
                    </div>
                    <div class="stat-number" style="font-size: 1.6rem;">
                        {{ $favouriteCharacters->count() }}
                    </div>
                </div>

            </div>
        </div>

    </aside>

    {{-- ─────────────────────────────
        MAIN CONTENT
    ───────────────────────────── --}}
    <section style="display:flex; flex-direction: column; gap: 3rem;">

        {{-- CHARACTERS --}}
        <div>

            <div class="section-heading">
                <h2 class="section-title">Favourite Characters</h2>
                <div class="section-rule"></div>
            </div>

            @if ($favouriteCharacters->isNotEmpty())
                <div class="grid-5">

                    @foreach ($favouriteCharacters as $character)
                        <a href="{{ route('characters.show', $character->id) }}" class="cover-card">

                            <div class="cover-card-img">
                                @if ($character->image)
                                    <img src="{{ $character->image }}" alt="{{ $character->name }}">
                                @else
                                    <div class="cover-card-placeholder">?</div>
                                @endif
                            </div>

                            <div class="cover-card-body">
                                <div class="cover-card-title">{{ $character->name }}</div>
                            </div>

                        </a>
                    @endforeach

                </div>
            @else
                <p class="text-muted">No favourite characters yet.</p>
            @endif

        </div>

        {{-- ISSUES --}}
        <div class="grid-2">

            {{-- FAVORITES --}}
            <div>

                <div class="section-heading">
                    <h2 class="section-title" style="font-size: 1.1rem;">Favourite Issues</h2>
                    <div class="section-rule"></div>
                </div>

                <div style="display:flex; flex-direction: column; gap: 0.75rem;">

                    @forelse ($favouriteIssues as $issue)
                        <a href="{{ route('issues.show', $issue->id) }}" class="char-card">

                            <img src="{{ $issue->image }}" class="char-avatar">

                            <div class="char-info">
                                <div class="char-name">{{ $issue->name ?? 'Untitled' }}</div>
                                <div class="char-meta">
                                    {{ $issue->volume->name ?? 'Unknown' }} • #{{ $issue->issue_number }}
                                </div>
                            </div>

                        </a>
                    @empty
                        <p class="text-muted">No favourite issues.</p>
                    @endforelse

                </div>

            </div>

            {{-- READ --}}
            <div>

                <div class="section-heading">
                    <h2 class="section-title" style="font-size: 1.1rem;">Issues Read</h2>
                    <div class="section-rule"></div>
                </div>

                <div style="display:flex; flex-direction: column; gap: 0.75rem;">

                    @forelse ($readIssues as $issue)
                        <a href="{{ route('issues.show', $issue->id) }}" class="char-card">

                            <img src="{{ $issue->image }}" class="char-avatar">

                            <div class="char-info">
                                <div class="char-name">{{ $issue->name ?? 'Untitled' }}</div>
                                <div class="char-meta">
                                    {{ $issue->volume->name ?? 'Unknown' }} • #{{ $issue->issue_number }}
                                </div>
                            </div>

                        </a>
                    @empty
                        <p class="text-muted">No read issues.</p>
                    @endforelse

                </div>

            </div>

        </div>

    </section>

</div>

@endsection