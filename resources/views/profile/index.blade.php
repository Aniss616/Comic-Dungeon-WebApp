@extends('layouts.app')

@section('title', $user->display_name . ' — Profile')

@push('styles')
<style>
/* ── Layout ─────────────────────────────────────────────────────── */
.profile-wrap {
    display: flex;
    min-height: calc(100vh - 60px);
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
    gap: 0;
}

/* ── Sidebar ─────────────────────────────────────────────────────── */
.profile-sidebar {
    width: 220px;
    flex-shrink: 0;
    padding: 32px 0 32px 0;
    padding-right: 24px;
    border-right: 1px solid var(--sl-border);
    display: flex;
    flex-direction: column;
    gap: 20px;
    position: sticky;
    top: 60px;
    height: fit-content;
}

.profile-avatar {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: var(--sl-raised);
    border: 2px solid var(--sl-border-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-display);
    font-size: 30px;
    font-weight: 800;
    color: var(--sl-red);
    letter-spacing: 0.02em;
    overflow: hidden;
    flex-shrink: 0;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-name {
    font-family: var(--font-display);
    font-size: 22px;
    font-weight: 800;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    color: var(--sl-text);
    line-height: 1.1;
}

.profile-username {
    font-size: 13px;
    color: var(--sl-muted);
    margin-top: 3px;
}

.profile-edit-btn {
    background: transparent;
    border: 1px solid var(--sl-border-md);
    color: var(--sl-muted);
    font-family: var(--font-body);
    font-size: 12px;
    padding: 7px 12px;
    cursor: pointer;
    border-radius: 4px;
    text-align: center;
    transition: border-color 0.15s, color 0.15s;
    width: 100%;
    text-decoration: none;
    display: block;
}
.profile-edit-btn:hover {
    border-color: var(--sl-red);
    color: var(--sl-text);
}

.sidebar-divider {
    border: none;
    border-top: 1px solid var(--sl-border);
    margin: 0;
}

.sidebar-stats {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.sidebar-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.sidebar-stat-label {
    font-size: 11px;
    color: var(--sl-muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-family: var(--font-display);
    font-weight: 600;
}

.sidebar-stat-val {
    font-size: 15px;
    font-weight: 500;
    color: var(--sl-text);
}

/* ── Main area ───────────────────────────────────────────────────── */
.profile-main {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}

/* ── Tab bar ─────────────────────────────────────────────────────── */
.profile-tabs {
    display: flex;
    border-bottom: 1px solid var(--sl-border);
    padding: 0 28px;
    gap: 2px;
    position: sticky;
    top: 58px;
    margin-top: -1px;
    padding-top: 1px;
    background: var(--sl-black);
    z-index: 10;
}

.profile-tab {
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    color: var(--sl-muted);
    font-family: var(--font-display);
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 15px 14px 13px;
    cursor: pointer;
    transition: color 0.15s, border-color 0.15s;
    margin-bottom: -1px;
}
.profile-tab:hover { color: var(--sl-text); }
.profile-tab.active { color: var(--sl-red); border-bottom-color: var(--sl-red); }

/* ── Tab panels ──────────────────────────────────────────────────── */
.profile-panels {
    padding: 28px;
}

.tab-panel { display: none; }
.tab-panel.active { display: block; }

/* ── Section label ───────────────────────────────────────────────── */
.p-section-label {
    font-family: var(--font-display);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--sl-muted);
    margin-bottom: 12px;
}

.p-section-gap { margin-bottom: 32px; }

/* ── Pinned volumes grid ─────────────────────────────────────────── */
.pinned-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}

/* ── Heatmap ─────────────────────────────────────────────────────── */
.heatmap-outer {
    display: flex;
    gap: 14px;
    align-items: flex-start;
}

.heatmap-main { flex: 1; min-width: 0; }

.heatmap-months {
    display: flex;
    margin-bottom: 5px;
}

.heatmap-month-label {
    font-size: 10px;
    color: var(--sl-faint);
    font-family: var(--font-display);
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    flex: 1;
}

.heatmap-grid {
    display: flex;
    gap: 3px;
}

.heatmap-col {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.heatmap-cell {
    width: 12px;
    height: 12px;
    background: var(--sl-raised);
    border: 1px solid var(--sl-border);
    border-radius: 2px;
    flex-shrink: 0;
    position: relative;
    cursor: default;
}

.heatmap-cell[data-v="1"] { background: rgba(192,57,43,0.22); border-color: rgba(192,57,43,0.28); }
.heatmap-cell[data-v="2"] { background: rgba(192,57,43,0.44); border-color: rgba(192,57,43,0.5); }
.heatmap-cell[data-v="3"] { background: rgba(192,57,43,0.68); border-color: rgba(192,57,43,0.72); }
.heatmap-cell[data-v="4"] { background: #C0392B; border-color: #C0392B; }

.heatmap-cell[data-v]:hover {
    outline: 1px solid var(--sl-amber);
    outline-offset: 1px;
    z-index: 5;
}

/* Tooltip */
.hm-tooltip {
    display: none;
    position: absolute;
    bottom: calc(100% + 7px);
    left: 50%;
    transform: translateX(-50%);
    background: #0D0D0D;
    border: 1px solid var(--sl-border-md);
    border-radius: 4px;
    padding: 8px 11px;
    white-space: nowrap;
    z-index: 100;
    pointer-events: none;
}

.heatmap-cell[data-v]:hover .hm-tooltip { display: block; }

.hm-tooltip-date {
    font-family: var(--font-display);
    font-size: 10px;
    font-weight: 700;
    color: var(--sl-muted);
    letter-spacing: 0.07em;
    text-transform: uppercase;
    margin-bottom: 5px;
}

.hm-tooltip-issue {
    font-size: 12px;
    color: var(--sl-text);
    padding: 1px 0;
}

/* Year selector */
.heatmap-years {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding-top: 16px;
    flex-shrink: 0;
}

.heatmap-year-btn {
    background: transparent;
    border: none;
    font-family: var(--font-display);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.05em;
    color: var(--sl-faint);
    cursor: pointer;
    padding: 4px 9px;
    border-radius: 3px;
    text-align: right;
    transition: color 0.15s, background 0.15s;
}
.heatmap-year-btn:hover { color: var(--sl-text); }
.heatmap-year-btn.active { color: var(--sl-red); background: var(--sl-red-dim); }

/* ── Activity feed ───────────────────────────────────────────────── */
.activity-feed {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 10px 12px;
    background: var(--sl-raised);
    border: 1px solid var(--sl-border);
    border-radius: 4px;
}

.activity-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
}
.dot-read { background: var(--sl-red); }
.dot-fav  { background: var(--sl-amber); }

.activity-text {
    flex: 1;
    font-size: 13px;
    color: var(--sl-text);
    min-width: 0;
}
.activity-text strong { font-weight: 500; }
.activity-text a { color: var(--sl-text); text-decoration: none; }
.activity-text a:hover { color: var(--sl-red); }
.activity-vol { color: var(--sl-muted); }

.activity-time {
    font-size: 11px;
    color: var(--sl-faint);
    font-family: var(--font-display);
    letter-spacing: 0.04em;
    flex-shrink: 0;
}

.more-btn {
    background: transparent;
    border: 1px solid var(--sl-border-md);
    color: var(--sl-muted);
    font-family: var(--font-display);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 8px 16px;
    cursor: pointer;
    border-radius: 4px;
    margin-top: 12px;
    width: 100%;
    transition: border-color 0.15s, color 0.15s;
}
.more-btn:hover { border-color: var(--sl-red); color: var(--sl-text); }

/* ── Stats rows ──────────────────────────────────────────────────── */
.stats-list {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.stats-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    background: var(--sl-raised);
    border: 1px solid var(--sl-border);
    border-radius: 4px;
}

.stats-rank {
    font-family: var(--font-display);
    font-size: 12px;
    font-weight: 700;
    color: var(--sl-faint);
    width: 18px;
    text-align: right;
    flex-shrink: 0;
}

.stats-thumb {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--sl-surface);
    border: 1px solid var(--sl-border-md);
    flex-shrink: 0;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-display);
    font-size: 10px;
    font-weight: 700;
    color: var(--sl-faint);
}

.stats-thumb.square {
    border-radius: 3px;
    width: 24px;
    height: 34px;
}

.stats-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.stats-name {
    flex: 1;
    font-size: 13px;
    color: var(--sl-text);
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.stats-bar-wrap {
    width: 100px;
    height: 4px;
    background: rgba(192,57,43,0.15); 
    border-radius: 2px;
    overflow: hidden;
    flex-shrink: 0;
}
.stats-bar-fill {
    height: 100%;
    background: var(--sl-red);
    border-radius: 2px;
    transition: width 0.3s ease;
}

.stats-count {
    font-size: 12px;
    color: var(--sl-muted);
    width: 38px;
    text-align: right;
    flex-shrink: 0;
    font-family: var(--font-display);
    font-weight: 600;
}

.stats-heart {
    font-size: 13px;
    color: var(--sl-amber);
    flex-shrink: 0;
    width: 16px;
}

/* ── Wishlist ─────────────────────────────────────────────────────── */
.wishlist-list {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.wishlist-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    background: var(--sl-raised);
    border: 1px solid var(--sl-border);
    border-radius: 4px;
}

.wish-thumb {
    width: 32px;
    height: 44px;
    background: var(--sl-surface);
    border-radius: 2px;
    flex-shrink: 0;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-display);
    font-size: 9px;
    font-weight: 700;
    color: var(--sl-faint);
    text-transform: uppercase;
    text-align: center;
    line-height: 1.3;
}

.wish-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.wish-info { flex: 1; min-width: 0; }

.wish-title {
    font-size: 13px;
    color: var(--sl-text);
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-decoration: none;
    display: block;
}
.wish-title:hover { color: var(--sl-red); }

.wish-vol {
    font-size: 11px;
    color: var(--sl-muted);
    margin-top: 2px;
}

.wish-remove {
    background: transparent;
    border: none;
    color: var(--sl-faint);
    cursor: pointer;
    padding: 6px;
    transition: color 0.15s;
    flex-shrink: 0;
    font-size: 16px;
    line-height: 1;
    display: flex;
    align-items: center;
}
.wish-remove:hover { color: var(--sl-red); }

.wishlist-empty {
    padding: 40px 0;
    text-align: center;
    color: var(--sl-muted);
    font-size: 13px;
}

/* ── Lists ───────────────────────────────────────────────────────── */
.lists-wrap {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.list-card {
    background: var(--sl-raised);
    border: 1px solid var(--sl-border);
    border-radius: 4px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 14px;
    cursor: pointer;
    transition: border-color 0.15s;
    text-decoration: none;
}
.list-card:hover { border-color: rgba(192,57,43,0.3); }

.list-covers {
    display: flex;
}

.list-cover-thumb {
    width: 28px;
    height: 40px;
    background: var(--sl-surface);
    border-radius: 2px;
    border: 1px solid var(--sl-border-md);
    margin-left: -5px;
    flex-shrink: 0;
    overflow: hidden;
}
.list-cover-thumb:first-child { margin-left: 0; }
.list-cover-thumb img { width: 100%; height: 100%; object-fit: cover; }

.list-info { flex: 1; min-width: 0; }

.list-name {
    font-family: var(--font-display);
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--sl-text);
}

.list-meta {
    font-size: 11px;
    color: var(--sl-muted);
    margin-top: 2px;
}

.new-list-btn {
    background: transparent;
    border: 1px dashed var(--sl-border-md);
    color: var(--sl-muted);
    font-family: var(--font-display);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 13px 16px;
    cursor: pointer;
    border-radius: 4px;
    width: 100%;
    text-align: left;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: border-color 0.15s, color 0.15s;
}
.new-list-btn:hover { border-color: var(--sl-red); color: var(--sl-text); }

/* ── New list modal (inline, no position:fixed) ──────────────────── */
.new-list-form {
    background: var(--sl-raised);
    border: 1px solid var(--sl-border-md);
    border-radius: 4px;
    padding: 16px;
    display: none;
    gap: 10px;
    align-items: center;
}
.new-list-form.open { display: flex; }

.new-list-input {
    flex: 1;
    background: var(--sl-surface);
    border: 1px solid var(--sl-border-md);
    border-radius: 4px;
    color: var(--sl-text);
    font-family: var(--font-body);
    font-size: 13px;
    padding: 8px 12px;
    outline: none;
}
.new-list-input:focus { border-color: var(--sl-red); }
.new-list-input::placeholder { color: var(--sl-faint); }

.new-list-save {
    background: var(--sl-red);
    border: none;
    color: var(--sl-text);
    font-family: var(--font-display);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: opacity 0.15s;
}
.new-list-save:hover { opacity: 0.85; }

.new-list-cancel {
    background: transparent;
    border: none;
    color: var(--sl-muted);
    font-size: 13px;
    cursor: pointer;
    padding: 8px;
    transition: color 0.15s;
}
.new-list-cancel:hover { color: var(--sl-text); }

/* ── Empty state ─────────────────────────────────────────────────── */
.empty-state {
    padding: 48px 0;
    text-align: center;
    color: var(--sl-muted);
    font-size: 13px;
}
.empty-state strong {
    display: block;
    font-family: var(--font-display);
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--sl-text);
    margin-bottom: 6px;
}
</style>
@endpush

@section('content')
<div class="profile-wrap">

    {{-- ── Sidebar ────────────────────────────────────────────────── --}}
    <aside class="profile-sidebar">
        <div class="profile-avatar">
            @if($user->avatar ?? false)
                <img src="{{ $user->avatar }}" alt="{{ $user->display_name }}">
            @else
                {{ strtoupper(substr($user->username, 0, 2)) }}
            @endif
        </div>

        <div>
            <div class="profile-name">{{ $user->display_name }}</div>
            <div class="profile-username">&#64;{{ $user->username }}</div>
        </div>

        <a href="#" class="profile-edit-btn">Edit Profile</a>

        <hr class="sidebar-divider">

        <div class="sidebar-stats">
            <div class="sidebar-stat">
                <span class="sidebar-stat-label">Issues Read</span>
                <span class="sidebar-stat-val">{{ number_format($readsCount) }}</span>
            </div>
            <div class="sidebar-stat">
                <span class="sidebar-stat-label">Fav. Issues</span>
                <span class="sidebar-stat-val">{{ number_format($favsCount) }}</span>
            </div>
            <div class="sidebar-stat">
                <span class="sidebar-stat-label">Fav. Characters</span>
                <span class="sidebar-stat-val">{{ number_format($favCharsCount) }}</span>
            </div>
        </div>

        <hr class="sidebar-divider">

        <div class="sidebar-stats">
            <div class="sidebar-stat">
                <span class="sidebar-stat-label">Wishlist</span>
                <span class="sidebar-stat-val">{{ number_format($wishlistCount) }}</span>
            </div>
            <div class="sidebar-stat">
                <span class="sidebar-stat-label">Lists</span>
                <span class="sidebar-stat-val">{{ number_format($listsCount) }}</span>
            </div>
        </div>
    </aside>

    {{-- ── Main ───────────────────────────────────────────────────── --}}
    <div class="profile-main">

        {{-- Tab bar --}}
        <nav class="profile-tabs" role="tablist">
            <button class="profile-tab active" data-tab="overview"  role="tab" aria-selected="true">Overview</button>
            <button class="profile-tab"         data-tab="activity" role="tab" aria-selected="false">Activity</button>
            <button class="profile-tab"         data-tab="stats"    role="tab" aria-selected="false">Stats</button>
            <button class="profile-tab"         data-tab="wishlist" role="tab" aria-selected="false">Wishlist</button>
            <button class="profile-tab"         data-tab="lists"    role="tab" aria-selected="false">Lists</button>
        </nav>

        <div class="profile-panels">

            {{-- ══════════════════════════════════════════════════════
                 OVERVIEW TAB
            ══════════════════════════════════════════════════════ --}}
            <div class="tab-panel active" id="panel-overview">

                {{-- Pinned volumes --}}
                <div class="p-section-gap">
                    <div class="p-section-label">Pinned Volumes</div>
                    <div class="pinned-grid">
                        {{-- Placeholder slots — replace with real user setting when implemented --}}
                        @for($i = 0; $i < 4; $i++)
                        <div class="cover-card" style="opacity:0.4; cursor:default;">
                            <div class="cover-card-img cover-card-placeholder">
                                <span style="font-family:var(--font-display);font-size:11px;letter-spacing:.05em;text-transform:uppercase;color:var(--sl-faint);">Pin a volume</span>
                            </div>
                            <div class="cover-card-body">
                                <div class="cover-card-title" style="color:var(--sl-faint);">—</div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>

                {{-- Heatmap --}}
                <div class="p-section-gap">
                    <div class="p-section-label">Reading Activity</div>
                    <div class="heatmap-outer">
                        <div class="heatmap-main">
                            <div class="heatmap-months" id="hm-month-labels"></div>
                            <div class="heatmap-grid"   id="hm-grid"></div>
                        </div>
                        <div class="heatmap-years" id="hm-years"></div>
                    </div>
                </div>

                {{-- Recent activity (first 8) --}}
                <div class="p-section-gap">
                    <div class="p-section-label">Recent Activity</div>
                    @if($activity->isEmpty())
                        <div class="empty-state">
                            <strong>No activity yet</strong>
                            Start reading and favouriting issues to build your history.
                        </div>
                    @else
                        <div class="activity-feed" id="recent-feed">
                            @foreach($activity->take(8) as $item)
                            <div class="activity-item">
                                <div class="activity-dot {{ $item->type === 'read' ? 'dot-read' : 'dot-fav' }}"></div>
                                <div class="activity-text">
                                    {{ $item->type === 'read' ? 'Read' : 'Favourited' }}
                                    <strong>
                                        <a href="{{ route('issues.show', $item->issue_id) }}">{{ $item->issue_name }}</a>
                                    </strong>
                                    <span class="activity-vol">— {{ $item->vol_name }}</span>
                                </div>
                                <div class="activity-time">{{ \Carbon\Carbon::parse($item->event_at)->diffForHumans() }}</div>
                            </div>
                            @endforeach
                        </div>
                        @if($activity->count() > 8)
                        <button class="more-btn" onclick="switchTab('activity')">View Full Activity</button>
                        @endif
                    @endif
                </div>

            </div>{{-- /overview --}}


            {{-- ══════════════════════════════════════════════════════
                 ACTIVITY TAB
            ══════════════════════════════════════════════════════ --}}
            <div class="tab-panel" id="panel-activity">
                <div class="p-section-label">All Activity</div>
                @if($activity->isEmpty())
                    <div class="empty-state">
                        <strong>No activity yet</strong>
                        Mark issues as read or favourite them to see your history here.
                    </div>
                @else
                    <div class="activity-feed">
                        @foreach($activity as $item)
                        <div class="activity-item">
                            <div class="activity-dot {{ $item->type === 'read' ? 'dot-read' : 'dot-fav' }}"></div>
                            <div class="activity-text">
                                {{ $item->type === 'read' ? 'Read' : 'Favourited' }}
                                <strong>
                                    <a href="{{ route('issues.show', $item->issue_id) }}">{{ $item->issue_name }}</a>
                                </strong>
                                <span class="activity-vol">— {{ $item->vol_name }}</span>
                            </div>
                            <div class="activity-time">{{ \Carbon\Carbon::parse($item->event_at)->diffForHumans() }}</div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>{{-- /activity --}}


            {{-- ══════════════════════════════════════════════════════
                 STATS TAB
            ══════════════════════════════════════════════════════ --}}
            <div class="tab-panel" id="panel-stats">

                {{-- Characters --}}
                <div class="p-section-gap">
                    <div class="p-section-label">Characters — by issues read</div>
                    @if($statCharacters->isEmpty())
                        <div class="empty-state">
                            <strong>No data yet</strong>
                            Read some issues to see your top characters.
                        </div>
                    @else
                        <div class="stats-list">
                            @foreach($statCharacters->take(5) as $i => $char)
                            <div class="stats-item">
                                <span class="stats-rank">{{ $i + 1 }}</span>
                                <div class="stats-thumb">
                                    @if($char->image)
                                        <img src="{{ $char->image }}" alt="{{ $char->name }}">
                                    @else
                                        {{ strtoupper(substr($char->name, 0, 2)) }}
                                    @endif
                                </div>
                                <span class="stats-name">
                                    <a href="{{ route('characters.show', $char->id) }}" style="color:inherit;text-decoration:none;">{{ $char->name }}</a>
                                </span>
                                <span class="stats-count">{{ $char->read_count }}</span>
                                <span class="stats-heart">
                                    @if($char->is_fav)&#9829;@else&nbsp;@endif
                                </span>
                            </div>
                            @endforeach
                            @if($statCharacters->count() > 5)
                                <a href="{{ route('profile.stats') }}" class="more-btn" style="text-decoration:none;display:block;text-align:center;margin-top:10px;">
                                    View all {{ $statCharacters->count() }} characters
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Volumes --}}
                <div class="p-section-gap">
                    <div class="p-section-label">Volumes — by issues read</div>
                    @if($statVolumes->isEmpty())
                        <div class="empty-state">
                            <strong>No data yet</strong>
                            Read some issues to see your top volumes.
                        </div>
                    @else
                        @php $maxVolCount = $statVolumes->first()->read_count; @endphp
                        <div class="stats-list">
                            @foreach($statVolumes->take(5) as $i => $vol)
                            <div class="stats-item">
                                <span class="stats-rank">{{ $i + 1 }}</span>
                                <div class="stats-thumb square">
                                    @if($vol->cover_image)
                                        <img src="{{ $vol->cover_image }}" alt="{{ $vol->name }}">
                                    @else
                                        {{ strtoupper(substr($vol->name, 0, 2)) }}
                                    @endif
                                </div>
                                <span class="stats-name">
                                    <a href="{{ route('volumes.show', $vol->id) }}" style="color:inherit;text-decoration:none;">{{ $vol->name }}</a>
                                </span>
                                <div class="stats-bar-wrap">
                                    <div class="stats-bar-fill" style="width:{{ $maxVolCount > 0 ? round($vol->read_count / $maxVolCount * 100) : 0 }}%"></div>
                                </div>
                                <span class="stats-count">{{ $vol->read_count }}</span>
                                <span class="stats-heart">
                                    @if($vol->is_fav)&#9829;@else&nbsp;@endif
                                </span>
                            </div>
                            @endforeach
                            @if($statVolumes->count() > 5)
                                <a href="{{ route('profile.stats') }}" class="more-btn" style="text-decoration:none;display:block;text-align:center;margin-top:10px;">
                                    View all {{ $statVolumes->count() }} volumes
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

            </div>{{-- /stats --}}


            {{-- ══════════════════════════════════════════════════════
                 WISHLIST TAB
            ══════════════════════════════════════════════════════ --}}
            <div class="tab-panel" id="panel-wishlist">
                <div class="p-section-label">Wishlist</div>
                @if($wishlist->isEmpty())
                    <div class="empty-state">
                        <strong>Your wishlist is empty</strong>
                        Add issues you want to read by hitting the wishlist button on any issue page.
                    </div>
                @else
                    <div class="wishlist-list" id="wishlist-list">
                        @foreach($wishlist as $issue)
                        <div class="wishlist-item" data-issue-id="{{ $issue->id }}">
                            <div class="wish-thumb">
                                @if($issue->image)
                                    <img src="{{ $issue->image }}" alt="{{ $issue->name }}">
                                @else
                                    {{ strtoupper(substr($issue->name ?? '??', 0, 2)) }}
                                @endif
                            </div>
                            <div class="wish-info">
                                <a href="{{ route('issues.show', $issue->id) }}" class="wish-title">{{ $issue->name }}</a>
                                <div class="wish-vol">{{ $issue->volume->name ?? '' }}</div>
                            </div>
                            <button
                                class="wish-remove"
                                aria-label="Remove from wishlist"
                                data-issue-id="{{ $issue->id }}"
                                onclick="removeFromWishlist(this, {{ $issue->id }})"
                            >&#10005;</button>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>{{-- /wishlist --}}


            {{-- ══════════════════════════════════════════════════════
                 LISTS TAB
            ══════════════════════════════════════════════════════ --}}
            <div class="tab-panel" id="panel-lists">
                <div class="p-section-label">Custom Lists</div>

                <div class="lists-wrap" id="lists-wrap">
                    @forelse($userLists as $list)
                    <div class="list-card">
                        <div class="list-covers">
                            @forelse($list->issues->take(3) as $li)
                                <div class="list-cover-thumb">
                                    @if($li->image)
                                        <img src="{{ $li->image }}" alt="{{ $li->name }}">
                                    @endif
                                </div>
                            @empty
                                <div class="list-cover-thumb"></div>
                                <div class="list-cover-thumb" style="margin-left:-5px;opacity:.6;"></div>
                                <div class="list-cover-thumb" style="margin-left:-5px;opacity:.3;"></div>
                            @endforelse
                        </div>
                        <div class="list-info">
                            <div class="list-name">{{ $list->name }}</div>
                            <div class="list-meta">{{ $list->issues_count }} {{ Str::plural('issue', $list->issues_count) }}</div>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="color:var(--sl-faint);flex-shrink:0;">
                            <path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    @empty
                        <div class="empty-state" id="lists-empty">
                            <strong>No lists yet</strong>
                            Create your first list to organise your reading.
                        </div>
                    @endforelse
                </div>

                {{-- New list form --}}
                <div class="new-list-form" id="new-list-form">
                    <input
                        type="text"
                        class="new-list-input"
                        id="new-list-input"
                        placeholder="List name…"
                        maxlength="100"
                    >
                    <button class="new-list-save" onclick="saveList()">Save</button>
                    <button class="new-list-cancel" onclick="closeNewList()">Cancel</button>
                </div>

                <button class="new-list-btn" id="new-list-btn" onclick="openNewList()" style="margin-top:8px;">
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                        <path d="M6.5 1v11M1 6.5h11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    New List
                </button>

            </div>{{-- /lists --}}

        </div>{{-- /profile-panels --}}
    </div>{{-- /profile-main --}}
</div>{{-- /profile-wrap --}}
@endsection


@push('scripts')
<script>
// ── Heatmap data from PHP ───────────────────────────────────────────
// Year selector navigates via query string — server renders the chosen year's data.
const ACTIVE_YEAR = {{ $year }};
const AVAILABLE_YEARS = @json($availableYears);

// heatmapRaw: { 'YYYY-MM-DD': count }
const heatmapRaw  = @json($heatmapRaw);
// tooltipRaw: { 'YYYY-MM-DD': [{issue_name, vol_name}, ...] }
const tooltipRaw  = @json(
    $tooltipRaw->map(fn($rows) => $rows->map(fn($r) => [
        'issue' => $r->issue_name,
        'vol'   => $r->vol_name,
    ]))
);

// ── Tab switching ───────────────────────────────────────────────────
function switchTab(name) {
    document.querySelectorAll('.profile-tab').forEach(t => {
        const on = t.dataset.tab === name;
        t.classList.toggle('active', on);
        t.setAttribute('aria-selected', on);
    });
    document.querySelectorAll('.tab-panel').forEach(p => {
        p.classList.toggle('active', p.id === 'panel-' + name);
    });
}

document.querySelectorAll('.profile-tab').forEach(t => {
    t.addEventListener('click', () => switchTab(t.dataset.tab));
});

// ── Heatmap ─────────────────────────────────────────────────────────
const MONTH_LABELS = ['Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar','Apr','May','Jun'];

function buildHeatmap(heatmapRaw, tooltipRaw, year) {
    const monthRow = document.getElementById('hm-month-labels');
    const grid     = document.getElementById('hm-grid');
    monthRow.innerHTML = '';
    grid.innerHTML     = '';

    // Build 53 columns × 7 rows anchored so TODAY is always rightmost
    // Start = the Sunday on or before (today - 364 days)
    const today     = new Date();
    today.setHours(0, 0, 0, 0);
    const endDate   = new Date(today);
    const startDate = new Date(today);
    startDate.setDate(startDate.getDate() - 364);
    // Rewind to previous Sunday
    startDate.setDate(startDate.getDate() - startDate.getDay());

    // Convert heatmapRaw ('YYYY-MM-DD' → count) into a fast lookup
    const countMap   = {};
    const tooltipMap = {};
    Object.entries(heatmapRaw).forEach(([d, n]) => { countMap[d] = n; });
    Object.entries(tooltipRaw).forEach(([d, issues]) => { tooltipMap[d] = issues; });

    // Level thresholds (1–4 intensity buckets)
    const level = (n) => {
        if (n === 0) return 0;
        if (n === 1) return 1;
        if (n <= 3)  return 2;
        if (n <= 6)  return 3;
        return 4;
    };

    // Month labels — collect which column each month first appears in
    const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const monthCols  = {};

    // Total columns
    const totalDays = Math.ceil((endDate - startDate) / 86400000) + 1;
    const totalCols = Math.ceil(totalDays / 7);

    let colDate = new Date(startDate);

    for (let w = 0; w < totalCols; w++) {
        const col = document.createElement('div');
        col.className = 'heatmap-col';

        for (let d = 0; d < 7; d++) {
            const cell = document.createElement('div');
            cell.className = 'heatmap-cell';

            const cellDate = new Date(colDate);
            cellDate.setDate(cellDate.getDate() + d);

            // Track first column of each month for labels
            const mo = cellDate.getMonth();
            if (!monthCols[mo] || monthCols[mo] > w) monthCols[mo] = w;

            // Only render cells up to today
            if (cellDate > today || cellDate < startDate) {
                cell.style.opacity = '0';
                col.appendChild(cell);
                continue;
            }

            const key = cellDate.toISOString().slice(0, 10);
            const cnt = countMap[key] || 0;
            const lv  = level(cnt);

            if (lv > 0) {
                cell.setAttribute('data-v', lv);

                const tt    = document.createElement('div');
                tt.className = 'hm-tooltip';
                const label  = cellDate.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
                const issues = (tooltipMap[key] || []).slice(0, 3);
                tt.innerHTML = `<div class="hm-tooltip-date">${label} — ${cnt} issue${cnt !== 1 ? 's' : ''} read</div>`
                    + issues.map(i => `<div class="hm-tooltip-issue">${i.issue}</div>`).join('');
                cell.appendChild(tt);
            }

            col.appendChild(cell);
        }

        grid.appendChild(col);
        colDate.setDate(colDate.getDate() + 7);
    }

    // Day-of-week labels (Sun Mon hidden, Mon/Wed/Fri shown like GitHub)
    // We render these as a fixed sidebar to the left — add via CSS grid overlay
    // For simplicity, inject them as a separate column before the grid
    const dayLabels = ['', 'Mon', '', 'Wed', '', 'Fri', ''];
    const dayCol = document.createElement('div');
    dayCol.className = 'heatmap-col';
    dayCol.style.cssText = 'margin-right:6px;';
    dayLabels.forEach(label => {
        const span = document.createElement('div');
        span.style.cssText = 'height:12px;font-family:var(--font-display);font-size:9px;font-weight:600;color:var(--sl-faint);letter-spacing:.05em;text-transform:uppercase;line-height:12px;text-align:right;width:24px;';
        span.textContent = label;
        dayCol.appendChild(span);
    });
    grid.prepend(dayCol);

    // Month labels — render above the grid
    const totalWidth = totalCols * (12 + 3); // cell + gap
    const monthRow2  = document.createElement('div');
    monthRow2.style.cssText = 'display:flex;position:relative;height:16px;margin-left:30px;'; // offset for day labels
    Object.entries(monthCols).sort((a, b) => a[1] - b[1]).forEach(([mo, col]) => {
        const span = document.createElement('span');
        span.className = 'heatmap-month-label';
        span.style.cssText = `position:absolute;left:${col * 15}px;`;
        span.textContent = monthNames[mo];
        monthRow2.appendChild(span);
    });
    monthRow.parentNode.replaceChild(monthRow2, monthRow);
    monthRow2.id = 'hm-month-labels';
}

function buildYearSelector() {
    const container = document.getElementById('hm-years');
    container.innerHTML = '';
    AVAILABLE_YEARS.forEach(yr => {
        const btn       = document.createElement('button');
        btn.className   = 'heatmap-year-btn' + (yr == ACTIVE_YEAR ? ' active' : '');
        btn.textContent = yr;
        btn.addEventListener('click', () => {
            window.location.href = '{{ route("profile") }}?year=' + yr;
        });
        container.appendChild(btn);
    });
}

// Initial render
buildHeatmap(heatmapRaw, tooltipRaw, ACTIVE_YEAR);
buildYearSelector();

// ── Wishlist remove ──────────────────────────────────────────────────
function removeFromWishlist(btn, issueId) {
    btn.disabled = true;
    fetch(`/profile/wishlist/${issueId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'removed') {
            const item = btn.closest('.wishlist-item');
            item.style.transition = 'opacity 0.2s';
            item.style.opacity = '0';
            setTimeout(() => item.remove(), 200);
        }
    })
    .catch(() => { btn.disabled = false; });
}

// ── New list ────────────────────────────────────────────────────────
function openNewList() {
    document.getElementById('new-list-form').classList.add('open');
    document.getElementById('new-list-btn').style.display = 'none';
    document.getElementById('new-list-input').focus();
}

function closeNewList() {
    document.getElementById('new-list-form').classList.remove('open');
    document.getElementById('new-list-btn').style.display = 'flex';
    document.getElementById('new-list-input').value = '';
}

function saveList() {
    const input = document.getElementById('new-list-input');
    const name  = input.value.trim();
    if (!name) { input.focus(); return; }

    fetch('{{ route("profile.lists.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ name }),
    })
    .then(r => r.json())
    .then(data => {
        // Remove empty state if present
        const empty = document.getElementById('lists-empty');
        if (empty) empty.remove();

        // Prepend new list card
        const wrap = document.getElementById('lists-wrap');
        const card = document.createElement('div');
        card.className = 'list-card';
        card.innerHTML = `
            <div class="list-covers">
                <div class="list-cover-thumb"></div>
                <div class="list-cover-thumb" style="margin-left:-5px;opacity:.6;"></div>
                <div class="list-cover-thumb" style="margin-left:-5px;opacity:.3;"></div>
            </div>
            <div class="list-info">
                <div class="list-name">${data.name}</div>
                <div class="list-meta">0 issues</div>
            </div>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="color:var(--sl-faint);flex-shrink:0;">
                <path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>`;
        wrap.prepend(card);
        closeNewList();
    })
    .catch(() => closeNewList());
}

// Allow Enter key in list name input
document.getElementById('new-list-input').addEventListener('keydown', e => {
    if (e.key === 'Enter') saveList();
    if (e.key === 'Escape') closeNewList();
});
</script>
@endpush