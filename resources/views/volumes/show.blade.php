@extends('layouts.app')

@section('title', $volume->name)

@section('content')

<div class="space-y-8">

    {{-- BACK --}}
    <a href="{{ route('explore') }}?tab=volumes"
       class="section-link">
        Back to Volumes
    </a>

    {{-- HERO --}}
    <div class="card" style="padding:2rem;">
        <div class="flex flex-wrap gap-2" style="gap:2rem; align-items:flex-start;">

            {{-- COVER --}}
            <div style="width:220px; flex-shrink:0;">

                @if ($volume->cover_image)

                    <img
                        src="{{ $volume->cover_image }}"
                        alt="{{ $volume->name }}"
                        style="
                            width:100%;
                            border-radius:var(--sl-radius-lg);
                            border:1px solid var(--sl-border-md);
                            background:var(--sl-surface);
                            object-fit:cover;
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
                        VOLUME
                    </div>

                @endif

            </div>

            {{-- INFO --}}
            <div style="flex:1; min-width:300px;">

                <span class="page-header-eyebrow">
                    Volume
                </span>

                <h1 class="page-header-title"
                    style="font-size:clamp(2.5rem,5vw,4rem);">
                    {{ $volume->name }}
                </h1>

                @if ($volume->publisher)
                    <p class="page-header-sub">
                        Published by
                        <span style="color:var(--sl-text); font-weight:600;">
                            {{ $volume->publisher->name }}
                        </span>

                        @if ($volume->publisher->location_city)
                            · {{ $volume->publisher->location_city }}

                            @if ($volume->publisher->location_country)
                                , {{ $volume->publisher->location_country }}
                            @endif
                        @endif
                    </p>
                @endif

                {{-- STATS --}}
                <div class="flex flex-wrap gap-2 mt-4">

                    <div class="card"
                         style="padding:1rem 1.4rem; min-width:140px; text-align:center;">
                        <div class="stat-number">
                            {{ $volume->count_of_issues ?? $volume->issues->count() }}
                        </div>
                        <div class="stat-label">
                            Total Issues
                        </div>
                    </div>

                    <div class="card"
                         style="padding:1rem 1.4rem; min-width:140px; text-align:center;">
                        <div class="stat-number">
                            {{ $volume->issues->count() }}
                        </div>
                        <div class="stat-label">
                            Imported
                        </div>
                    </div>

                    @if ($volume->first_issue)
                        <div class="card"
                             style="padding:1rem 1.4rem; min-width:140px; text-align:center;">
                            <div class="stat-label mb-1">
                                First Issue
                            </div>

                            <div style="
                                font-family:var(--font-display);
                                font-size:1.8rem;
                                font-weight:800;
                                color:var(--sl-text);
                                line-height:1;
                            ">
                                #{{ $volume->first_issue['issue_number'] ?? '?' }}
                            </div>
                        </div>
                    @endif

                    @if ($volume->last_issue)
                        <div class="card"
                             style="padding:1rem 1.4rem; min-width:140px; text-align:center;">
                            <div class="stat-label mb-1">
                                Last Issue
                            </div>

                            <div style="
                                font-family:var(--font-display);
                                font-size:1.8rem;
                                font-weight:800;
                                color:var(--sl-text);
                                line-height:1;
                            ">
                                #{{ $volume->last_issue['issue_number'] ?? '?' }}
                            </div>
                        </div>
                    @endif

                </div>

            </div>

        </div>
    </div>

    {{-- DESCRIPTION --}}
    @if (count($descriptionSections) > 0)

        <div>

            <div class="section-heading">
                <h2 class="section-title">About</h2>
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

    {{-- ISSUES --}}
    @if ($volume->issues->count() > 0)

        <div>

            <div class="section-heading">
                <h2 class="section-title">
                    Issues
                </h2>

                <div class="section-rule"></div>

                <span class="text-faint"
                      style="
                        font-family:var(--font-display);
                        font-size:0.8rem;
                        letter-spacing:0.08em;
                        text-transform:uppercase;
                      ">
                    {{ $volume->issues->count() }} Imported
                </span>
            </div>

            <div class="card"
                 style="padding:1rem;">

                <div class="space-y-2">

                    @foreach ($volume->issues as $issue)

                        <a href="{{ route('issues.show', $issue->id) }}"
                           class="char-card"
                           style="
                                border-radius:var(--sl-radius);
                                padding:0.85rem 1rem;
                           ">

                            {{-- COVER --}}
                            @if ($issue->image)

                                <img
                                    src="{{ $issue->image }}"
                                    alt="{{ $issue->name }}"
                                    style="
                                        width:42px;
                                        height:60px;
                                        object-fit:cover;
                                        object-position:top;
                                        border-radius:4px;
                                        border:1px solid var(--sl-border);
                                        flex-shrink:0;
                                    "
                                />

                            @else

                                <div style="
                                    width:42px;
                                    height:60px;
                                    border-radius:4px;
                                    background:var(--sl-surface);
                                    border:1px solid var(--sl-border);
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    font-size:10px;
                                    color:var(--sl-faint);
                                    flex-shrink:0;
                                ">
                                    ISSUE
                                </div>

                            @endif

                            {{-- INFO --}}
                            <div class="char-info">

                                <div class="char-name">
                                    {{ $issue->name ?? 'Untitled' }}
                                </div>

                                <div class="char-meta">
                                    #{{ $issue->issue_number ?? '?' }}

                                    @if ($issue->cover_date)
                                        · {{ $issue->cover_date }}
                                    @endif

                                    @if ($issue->store_date)
                                        · On Sale: {{ $issue->store_date }}
                                    @endif
                                </div>

                            </div>

                            {{-- STATUS --}}
                            @auth

                                @php
                                    $isRead = Auth::user()
                                        ->reads()
                                        ->where('issue_id', $issue->id)
                                        ->exists();
                                @endphp

                                <span class="badge {{ $isRead ? 'badge-amber' : 'badge-neutral' }}">
                                    {{ $isRead ? 'Read' : 'Unread' }}
                                </span>

                            @endauth

                        </a>

                    @endforeach

                </div>

            </div>

        </div>

    @else

        <div class="card"
             style="padding:2rem; text-align:center;">

            <p class="text-muted">
                No issues imported for this volume yet.
            </p>

            @auth
                @if(Auth::user()->is_admin)

                    <a href="{{ route('dashboard') }}"
                       class="section-link"
                       style="display:inline-block; margin-top:1rem;">
                        Import issues from dashboard
                    </a>

                @endif
            @endauth

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
</script>
@endpush