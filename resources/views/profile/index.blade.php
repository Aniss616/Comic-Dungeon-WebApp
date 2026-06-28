@extends('layouts.app')

@section('title', $user->display_name . ' — Profile')

@push('styles')
<style>
/* ── Layout ─────────────────────────────────────────────────────── */
.profile-wrap {
    display: flex;
    min-height: calc(100vh - 58px);
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
}

/* ── Sidebar ─────────────────────────────────────────────────────── */
.profile-sidebar {
    width: 220px;
    flex-shrink: 0;
    padding: 32px 24px 32px 0;
    border-right: 1px solid var(--sl-border);
    display: flex;
    flex-direction: column;
    gap: 20px;
    position: sticky;
    top: 58px;
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
    overflow: hidden;
    flex-shrink: 0;
}
.profile-avatar img { width: 100%; height: 100%; object-fit: cover; }

.profile-name {
    font-family: var(--font-display);
    font-size: 22px;
    font-weight: 800;
    letter-spacing: .03em;
    text-transform: uppercase;
    color: var(--sl-text);
    line-height: 1.1;
}
.profile-username { font-size: 13px; color: var(--sl-muted); margin-top: 3px; }

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
    transition: border-color .15s, color .15s;
    width: 100%;
    text-decoration: none;
    display: block;
}
.profile-edit-btn:hover { border-color: var(--sl-red); color: var(--sl-text); }

.sidebar-divider { border: none; border-top: 1px solid var(--sl-border); margin: 0; }

.sidebar-stats { display: flex; flex-direction: column; gap: 10px; }
.sidebar-stat  { display: flex; justify-content: space-between; align-items: center; }
.sidebar-stat-label {
    font-size: 11px; color: var(--sl-muted); text-transform: uppercase;
    letter-spacing: .08em; font-family: var(--font-display); font-weight: 600;
}
.sidebar-stat-val { font-size: 15px; font-weight: 500; color: var(--sl-text); }

/* ── Main ────────────────────────────────────────────────────────── */
.profile-main { flex: 1; min-width: 0; display: flex; flex-direction: column; }

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
    letter-spacing: .06em;
    text-transform: uppercase;
    padding: 15px 14px 13px;
    cursor: pointer;
    transition: color .15s, border-color .15s;
    margin-bottom: -1px;
}
.profile-tab:hover { color: var(--sl-text); }
.profile-tab.active { color: var(--sl-red); border-bottom-color: var(--sl-red); }

/* ── Panels ──────────────────────────────────────────────────────── */
.profile-panels { padding: 28px; }

.tab-panel         { display: none; }
.tab-panel.active  { display: block; }

/* ── Section label ───────────────────────────────────────────────── */
.p-section-label {
    font-family: var(--font-display);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--sl-muted);
    margin-bottom: 12px;
}
.p-section-gap { margin-bottom: 32px; }

/* ── Pinned grid ─────────────────────────────────────────────────── */
.pinned-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}

/* ── Heatmap ─────────────────────────────────────────────────────── */
.heatmap-outer  { display: flex; gap: 14px; align-items: flex-start; }
.heatmap-main   { flex: 1; min-width: 0; }
.heatmap-grid   { display: flex; gap: 3px; align-items: flex-start; }
.heatmap-col    { display: flex; flex-direction: column; gap: 3px; }

.heatmap-cell {
    width: 12px; height: 12px;
    background: rgba(255,255,255,0.04);  /* was var(--sl-raised) */
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 2px;
    flex-shrink: 0;
    position: relative;
    cursor: default;
}

.heatmap-cell[data-v="1"] { background: rgba(192,57,43,.35); border-color: rgba(192,57,43,.45); }
.heatmap-cell[data-v="2"] { background: rgba(192,57,43,.58); border-color: rgba(192,57,43,.65); }
.heatmap-cell[data-v="3"] { background: rgba(192,57,43,.82); border-color: rgba(192,57,43,.88); box-shadow: 0 0 4px rgba(192,57,43,.4); }
.heatmap-cell[data-v="4"] { background: #C0392B; border-color: #e04535; box-shadow: 0 0 6px rgba(192,57,43,.7); }
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
    font-family: var(--font-display); font-size: 10px; font-weight: 700;
    color: var(--sl-muted); letter-spacing: .07em; text-transform: uppercase; margin-bottom: 5px;
}
.hm-tooltip-issue { font-size: 12px; color: var(--sl-text); padding: 1px 0; }

.heatmap-years  { display: flex; flex-direction: column; gap: 4px; padding-top: 16px; flex-shrink: 0; }
.heatmap-year-btn {
    background: transparent; border: none;
    font-family: var(--font-display); font-size: 12px; font-weight: 700; letter-spacing: .05em;
    color: var(--sl-faint); cursor: pointer; padding: 4px 9px; border-radius: 3px;
    text-align: right; transition: color .15s, background .15s;
}
.heatmap-year-btn:hover { color: var(--sl-text); }
.heatmap-year-btn.active { color: var(--sl-red); background: var(--sl-red-dim); }

/* ── Activity ────────────────────────────────────────────────────── */
.activity-feed { display: flex; flex-direction: column; gap: 2px; }
.activity-item {
    display: flex; align-items: center; gap: 11px;
    padding: 10px 12px;
    background: var(--sl-raised); border: 1px solid var(--sl-border); border-radius: 4px;
}
.activity-dot  { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.dot-read      { background: var(--sl-red); }
.dot-fav       { background: var(--sl-amber); }
.activity-text { flex: 1; font-size: 13px; color: var(--sl-text); min-width: 0; }
.activity-text strong { font-weight: 500; }
.activity-text a { color: var(--sl-text); text-decoration: none; }
.activity-text a:hover { color: var(--sl-red); }
.activity-vol  { color: var(--sl-muted); }
.activity-time { font-size: 11px; color: var(--sl-faint); font-family: var(--font-display); letter-spacing: .04em; flex-shrink: 0; }

.more-btn {
    background: transparent; border: 1px solid var(--sl-border-md); color: var(--sl-muted);
    font-family: var(--font-display); font-size: 12px; font-weight: 700; letter-spacing: .08em;
    text-transform: uppercase; padding: 8px 16px; cursor: pointer; border-radius: 4px;
    margin-top: 12px; width: 100%; transition: border-color .15s, color .15s;
}
.more-btn:hover { border-color: var(--sl-red); color: var(--sl-text); }

/* ── Stats ───────────────────────────────────────────────────────── */
.stats-list { display: flex; flex-direction: column; gap: 2px; }
.stats-item {
    display: flex; align-items: center; gap: 10px; padding: 9px 12px;
    background: var(--sl-raised); border: 1px solid var(--sl-border); border-radius: 4px;
}
.stats-rank  { font-family: var(--font-display); font-size: 12px; font-weight: 700; color: var(--sl-faint); width: 18px; text-align: right; flex-shrink: 0; }
.stats-thumb {
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--sl-surface); border: 1px solid var(--sl-border-md);
    flex-shrink: 0; overflow: hidden; display: flex; align-items: center; justify-content: center;
    font-family: var(--font-display); font-size: 10px; font-weight: 700; color: var(--sl-faint);
}
.stats-thumb.square { border-radius: 3px; width: 24px; height: 34px; }
.stats-thumb img    { width: 100%; height: 100%; object-fit: cover; }
.stats-name  { flex: 1; font-size: 13px; color: var(--sl-text); min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.stats-name a { color: inherit; text-decoration: none; }
.stats-name a:hover { color: var(--sl-red); }
.stats-bar-wrap {
    width: 100px;
    height: 4px;
    background: rgba(192,57,43,0.22);
    border-radius: 2px;
    overflow: hidden;
    flex-shrink: 0;
}
.stats-bar-fill {
    height: 100%;
    background: #C0392B;
    border-radius: 2px;
    transition: width 0.3s ease;
}
.stats-count { font-size: 12px; color: var(--sl-muted); width: 38px; text-align: right; flex-shrink: 0; font-family: var(--font-display); font-weight: 600; }
.stats-heart { font-size: 13px; color: var(--sl-amber); flex-shrink: 0; width: 16px; }

/* ── Wishlist ────────────────────────────────────────────────────── */
.wishlist-list { display: flex; flex-direction: column; gap: 2px; }
.wishlist-item {
    display: flex; align-items: center; gap: 12px; padding: 10px 12px;
    background: var(--sl-raised); border: 1px solid var(--sl-border); border-radius: 4px;
}
.wish-thumb {
    width: 32px; height: 44px; background: var(--sl-surface); border-radius: 2px;
    flex-shrink: 0; overflow: hidden; display: flex; align-items: center; justify-content: center;
    font-family: var(--font-display); font-size: 9px; font-weight: 700; color: var(--sl-faint);
    text-transform: uppercase; text-align: center; line-height: 1.3;
}
.wish-thumb img { width: 100%; height: 100%; object-fit: cover; }
.wish-info      { flex: 1; min-width: 0; }
.wish-title {
    font-size: 13px; color: var(--sl-text); font-weight: 500;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    text-decoration: none; display: block;
}
.wish-title:hover { color: var(--sl-red); }
.wish-vol    { font-size: 11px; color: var(--sl-muted); margin-top: 2px; }
.wish-remove {
    background: transparent; border: none; color: var(--sl-faint); cursor: pointer;
    padding: 6px; transition: color .15s; flex-shrink: 0; font-size: 16px;
    line-height: 1; display: flex; align-items: center;
}
.wish-remove:hover { color: var(--sl-red); }

/* ── Lists ───────────────────────────────────────────────────────── */
.lists-wrap { display: flex; flex-direction: column; gap: 8px; }
.list-card {
    background: var(--sl-raised); border: 1px solid var(--sl-border); border-radius: 4px;
    padding: 14px 16px; display: flex; align-items: center; gap: 14px;
    cursor: pointer; transition: border-color .15s; text-decoration: none;
}
.list-card:hover { border-color: rgba(192,57,43,.3); }
.list-covers    { display: flex; }
.list-cover-thumb {
    width: 28px; height: 40px; background: var(--sl-surface);
    border-radius: 2px; border: 1px solid var(--sl-border-md);
    margin-left: -5px; flex-shrink: 0; overflow: hidden;
}
.list-cover-thumb:first-child { margin-left: 0; }
.list-cover-thumb img { width: 100%; height: 100%; object-fit: cover; }
.list-info  { flex: 1; min-width: 0; }
.list-name  { font-family: var(--font-display); font-size: 15px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: var(--sl-text); }
.list-meta  { font-size: 11px; color: var(--sl-muted); margin-top: 2px; }

.new-list-btn {
    background: transparent; border: 1px dashed var(--sl-border-md); color: var(--sl-muted);
    font-family: var(--font-display); font-size: 12px; font-weight: 700; letter-spacing: .08em;
    text-transform: uppercase; padding: 13px 16px; cursor: pointer; border-radius: 4px;
    width: 100%; text-align: left; display: flex; align-items: center; gap: 8px;
    transition: border-color .15s, color .15s;
}
.new-list-btn:hover { border-color: var(--sl-red); color: var(--sl-text); }

.new-list-form {
    background: var(--sl-raised); border: 1px solid var(--sl-border-md); border-radius: 4px;
    padding: 16px; display: none; gap: 10px; align-items: center;
}
.new-list-form.open { display: flex; }
.new-list-input {
    flex: 1; background: var(--sl-surface); border: 1px solid var(--sl-border-md);
    border-radius: 4px; color: var(--sl-text); font-family: var(--font-body);
    font-size: 13px; padding: 8px 12px; outline: none;
}
.new-list-input:focus { border-color: var(--sl-red); }
.new-list-input::placeholder { color: var(--sl-faint); }
.new-list-save {
    background: var(--sl-red); border: none; color: var(--sl-text);
    font-family: var(--font-display); font-size: 12px; font-weight: 700;
    letter-spacing: .06em; text-transform: uppercase; padding: 8px 16px;
    border-radius: 4px; cursor: pointer; transition: opacity .15s;
}
.new-list-save:hover { opacity: .85; }
.new-list-cancel {
    background: transparent; border: none; color: var(--sl-muted);
    font-size: 13px; cursor: pointer; padding: 8px; transition: color .15s;
}
.new-list-cancel:hover { color: var(--sl-text); }

/* ── Empty state ─────────────────────────────────────────────────── */
.empty-state { padding: 48px 0; text-align: center; color: var(--sl-muted); font-size: 13px; }
.empty-state strong {
    display: block; font-family: var(--font-display); font-size: 16px; font-weight: 700;
    letter-spacing: .04em; text-transform: uppercase; color: var(--sl-text); margin-bottom: 6px;
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

        <a href="{{ route('settings') }}" class="profile-edit-btn">Edit Profile</a>

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

        <nav class="profile-tabs" role="tablist">
            <button class="profile-tab active" data-tab="overview"  role="tab">Overview</button>
            <button class="profile-tab"         data-tab="activity" role="tab">Activity</button>
            <button class="profile-tab"         data-tab="stats"    role="tab">Stats</button>
            <button class="profile-tab"         data-tab="wishlist" role="tab">Wishlist</button>
            <button class="profile-tab"         data-tab="lists"    role="tab">Lists</button>
        </nav>

        <div class="profile-panels">

            {{-- ══════════════════════════════════════════ OVERVIEW ══ --}}
            <div class="tab-panel active" id="panel-overview">

                {{-- Pinned Volumes --}}
                <div class="p-section-gap">
                    <div class="p-section-label">Pinned Volumes</div>
                    <div class="pinned-grid">
                        @for($pos = 0; $pos < 4; $pos++)
                            @php $vol = $pinnedVolumes->firstWhere('position', $pos); @endphp
                            @if($vol)
                                <a href="{{ route('volumes.show', $vol->id) }}" class="cover-card">
                                    <div class="cover-card-img">
                                        @if($vol->cover_image)
                                            <img src="{{ $vol->cover_image }}" alt="{{ $vol->name }}" style="width:100%;height:100%;object-fit:cover;">
                                        @else
                                            <div class="cover-card-placeholder">{{ strtoupper(substr($vol->name, 0, 2)) }}</div>
                                        @endif
                                    </div>
                                    <div class="cover-card-body">
                                        <div class="cover-card-title">{{ $vol->name }}</div>
                                    </div>
                                </a>
                            @else
                                <a href="{{ route('settings') }}" class="cover-card" style="opacity:.35;">
                                    <div class="cover-card-img cover-card-placeholder">
                                        <span style="font-family:var(--font-display);font-size:11px;letter-spacing:.05em;text-transform:uppercase;color:var(--sl-faint);">Pin a volume</span>
                                    </div>
                                    <div class="cover-card-body">
                                        <div class="cover-card-title" style="color:var(--sl-faint);">—</div>
                                    </div>
                                </a>
                            @endif
                        @endfor
                    </div>
                </div>

                {{-- Heatmap --}}
                <div class="p-section-gap">
                    <div class="p-section-label">Reading Activity</div>
                    <div class="heatmap-outer">
                        <div class="heatmap-main">
                            <div id="hm-month-labels"></div>
                            <div class="heatmap-grid" id="hm-grid"></div>
                        </div>
                        <div class="heatmap-years" id="hm-years"></div>
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="p-section-gap">
                    <div class="p-section-label">Recent Activity</div>
                    @if($activity->isEmpty())
                        <div class="empty-state">
                            <strong>No activity yet</strong>
                            Start reading and favouriting issues to build your history.
                        </div>
                    @else
                        <div class="activity-feed">
                            @foreach($activity->take(8) as $item)
                            <div class="activity-item">
                                <div class="activity-dot {{ $item->type === 'read' ? 'dot-read' : 'dot-fav' }}"></div>
                                <div class="activity-text">
                                    {{ $item->type === 'read' ? 'Read' : 'Favourited' }}
                                    <strong><a href="{{ route('issues.show', $item->issue_id) }}">{{ $item->issue_name }}</a></strong>
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

            </div>{{-- /panel-overview --}}

            {{-- ══════════════════════════════════════════ ACTIVITY ══ --}}
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
                                <strong><a href="{{ route('issues.show', $item->issue_id) }}">{{ $item->issue_name }}</a></strong>
                                <span class="activity-vol">— {{ $item->vol_name }}</span>
                            </div>
                            <div class="activity-time">{{ \Carbon\Carbon::parse($item->event_at)->diffForHumans() }}</div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>{{-- /panel-activity --}}

            {{-- ══════════════════════════════════════════ STATS ═════ --}}
            <div class="tab-panel" id="panel-stats">

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
                                    <a href="{{ route('characters.show', $char->id) }}">{{ $char->name }}</a>
                                </span>
                                <span class="stats-count">{{ $char->read_count }}</span>
                                <span class="stats-heart">@if($char->is_fav)&#9829;@else&nbsp;@endif</span>
                            </div>
                            @endforeach
                        </div>
                        @if($statCharacters->count() > 5)
                            <a href="{{ route('profile.stats') }}" class="more-btn" style="text-decoration:none;display:block;text-align:center;margin-top:10px;">
                                View all {{ $statCharacters->count() }} characters
                            </a>
                        @endif
                    @endif
                </div>

                <div class="p-section-gap">
                    <div class="p-section-label">Volumes — by issues read</div>
                    @if($statVolumes->isEmpty())
                        <div class="empty-state">
                            <strong>No data yet</strong>
                            Read some issues to see your top volumes.
                        </div>
                    @else
                        <div class="stats-list">
                            @foreach($statVolumes->take(5) as $i => $vol)
                            @php
                            $total   = $vol->count_of_issues ?: $vol->read_count;
                            $fillPct = $total > 0 ? min(round($vol->read_count / $total * 100), 100) : 0;
                            @endphp
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
                                <a href="{{ route('volumes.show', $vol->id) }}">{{ $vol->name }}</a>
                                </span>
                                <div class="stats-bar-wrap">
                                <div class="stats-bar-fill" style="width:{{ $fillPct }}%"></div>
                                </div>
                                <span class="stats-count">{{ $vol->read_count }}/{{ $total }}</span>
                                <span class="stats-heart">@if($vol->is_fav)&#9829;@else&nbsp;@endif</span>
                            </div>
                            @endforeach
                        </div>
                            @if($statVolumes->count() > 5)
                                <a href="{{ route('profile.stats') }}" class="more-btn" style="text-decoration:none;display:block;text-align:center;margin-top:10px;">
                                    View all {{ $statVolumes->count() }} volumes
                                </a>
                            @endif
                    @endif
                </div>

            </div>{{-- /panel-stats --}}

            {{-- ══════════════════════════════════════════ WISHLIST ══ --}}
            <div class="tab-panel" id="panel-wishlist">
                <div class="p-section-label">Wishlist</div>
                @if($wishlist->isEmpty())
                    <div class="empty-state">
                        <strong>Your wishlist is empty</strong>
                        Add issues you want to read from any issue page.
                    </div>
                @else
                    <div class="wishlist-list" id="wishlist-list">
                        @foreach($wishlist as $issue)
                        <div class="wishlist-item" id="wish-{{ $issue->id }}">
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
                            <button class="wish-remove" onclick="removeFromWishlist(this, {{ $issue->id }})" aria-label="Remove">&#10005;</button>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>{{-- /panel-wishlist --}}

            {{-- ══════════════════════════════════════════ LISTS ═════ --}}
            <div class="tab-panel" id="panel-lists">
                <div class="p-section-label">Custom Lists</div>

                <div class="lists-wrap" id="lists-wrap">
                    @forelse($userLists as $list)
                    <a href="{{ route('profile.lists.show', $list->id) }}" class="list-card">
                        <div class="list-covers">
                            @forelse($list->issues->take(3) as $li)
                                <div class="list-cover-thumb">
                                    @if($li->image)<img src="{{ $li->image }}" alt="{{ $li->name }}">@endif
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
                    </a>
                    @empty
                        <div class="empty-state" id="lists-empty">
                            <strong>No lists yet</strong>
                            Create your first list to organise your reading.
                        </div>
                    @endforelse
                </div>

                <div class="new-list-form" id="new-list-form">
                    <input type="text" class="new-list-input" id="new-list-input" placeholder="List name…" maxlength="100">
                    <button class="new-list-save" onclick="saveList()">Save</button>
                    <button class="new-list-cancel" onclick="closeNewList()">Cancel</button>
                </div>

                <button class="new-list-btn" id="new-list-btn" onclick="openNewList()" style="margin-top:8px;">
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                        <path d="M6.5 1v11M1 6.5h11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    New List
                </button>

            </div>{{-- /panel-lists --}}

        </div>{{-- /profile-panels --}}
    </div>{{-- /profile-main --}}
</div>{{-- /profile-wrap --}}
@endsection

@push('scripts')
<script>
const ACTIVE_YEAR     = {{ $year }};
const AVAILABLE_YEARS = @json($availableYears);
const heatmapRaw      = @json($heatmapRaw);
const tooltipRaw      = @json(
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
        t.setAttribute('aria-selected', String(on));
    });
    document.querySelectorAll('.tab-panel').forEach(p => {
        p.classList.toggle('active', p.id === 'panel-' + name);
    });
}
document.querySelectorAll('.profile-tab').forEach(t => {
    t.addEventListener('click', () => switchTab(t.dataset.tab));
});

// ── Heatmap ─────────────────────────────────────────────────────────
function buildHeatmap(countData, ttData) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const startDate = new Date(today);
    startDate.setDate(startDate.getDate() - 364);
    startDate.setDate(startDate.getDate() - startDate.getDay()); // rewind to Sunday

    const countMap = {};
    const tipMap   = {};
    Object.entries(countData).forEach(([d, n])       => { countMap[d] = parseInt(n); });
    Object.entries(ttData).forEach(([d, issues])     => { tipMap[d]   = issues; });

    const level = n => n <= 0 ? 0 : n === 1 ? 1 : n <= 3 ? 2 : n <= 6 ? 3 : 4;

    const monthNames    = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const monthFirstCol = {};

    const grid = document.getElementById('hm-grid');
    grid.innerHTML = '';

    // Day label column
    const dayCol = document.createElement('div');
    dayCol.className  = 'heatmap-col';
    dayCol.style.marginRight = '6px';
    ['','Mon','','Wed','','Fri',''].forEach(label => {
        const el = document.createElement('div');
        el.style.cssText = 'height:12px;line-height:12px;font-family:var(--font-display);font-size:9px;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:var(--sl-faint);text-align:right;width:22px;white-space:nowrap;';
        el.textContent = label;
        dayCol.appendChild(el);
    });
    grid.appendChild(dayCol);

    // Data columns
    const totalDays = Math.ceil((today - startDate) / 86400000) + 1;
    const totalCols = Math.ceil(totalDays / 7);
    let colStart = new Date(startDate);

    for (let w = 0; w < totalCols; w++) {
        const col = document.createElement('div');
        col.className = 'heatmap-col';

        for (let d = 0; d < 7; d++) {
            const cellDate = new Date(colStart);
            cellDate.setDate(cellDate.getDate() + d);

            const cell = document.createElement('div');
            cell.className = 'heatmap-cell';

            // Track month label
            if (cellDate.getDate() <= 7) {
                const mo = cellDate.getMonth();
                if (monthFirstCol[mo] === undefined) monthFirstCol[mo] = w;
            }

            if (cellDate > today || cellDate < startDate) {
                cell.style.visibility = 'hidden';
                col.appendChild(cell);
                continue;
            }

            const key = cellDate.toLocaleDateString('en-CA'); // YYYY-MM-DD
            const cnt = countMap[key] || 0;
            const lv  = level(cnt);

            if (lv > 0) {
                cell.setAttribute('data-v', lv);
                const tt    = document.createElement('div');
                tt.className = 'hm-tooltip';
                const label  = cellDate.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
                const issues = (tipMap[key] || []).slice(0, 3);
                tt.innerHTML = `<div class="hm-tooltip-date">${label} — ${cnt} issue${cnt !== 1 ? 's' : ''} read</div>`
                    + issues.map(i => `<div class="hm-tooltip-issue">${i.issue}</div>`).join('');
                cell.appendChild(tt);
            }
            col.appendChild(cell);
        }

        grid.appendChild(col);
        colStart.setDate(colStart.getDate() + 7);
    }

    // Month labels
    const monthWrap = document.getElementById('hm-month-labels');
    monthWrap.style.cssText = 'position:relative;height:16px;margin-left:28px;margin-bottom:4px;';
    monthWrap.innerHTML = '';
    Object.entries(monthFirstCol)
        .sort((a, b) => a[1] - b[1])
        .forEach(([mo, col]) => {
            const span = document.createElement('span');
            span.style.cssText = `position:absolute;left:${col * 15}px;font-size:10px;color:var(--sl-faint);font-family:var(--font-display);font-weight:600;letter-spacing:.06em;text-transform:uppercase;`;
            span.textContent = monthNames[parseInt(mo)];
            monthWrap.appendChild(span);
        });
}

function buildYearSelector() {
    const container = document.getElementById('hm-years');
    container.innerHTML = '';
    AVAILABLE_YEARS.forEach(yr => {
        const btn = document.createElement('button');
        btn.className   = 'heatmap-year-btn' + (yr == ACTIVE_YEAR ? ' active' : '');
        btn.textContent = yr;
        btn.addEventListener('click', () => {
            window.location.href = '{{ route("profile") }}?year=' + yr;
        });
        container.appendChild(btn);
    });
}

buildHeatmap(heatmapRaw, tooltipRaw);
buildYearSelector();

// ── Wishlist remove ─────────────────────────────────────────────────
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
            const item = document.getElementById('wish-' + issueId);
            item.style.transition = 'opacity .2s';
            item.style.opacity    = '0';
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
        const empty = document.getElementById('lists-empty');
        if (empty) empty.remove();

        const wrap = document.getElementById('lists-wrap');
        const card = document.createElement('a');
        card.href      = '/profile/lists/' + data.id;
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
document.getElementById('new-list-input').addEventListener('keydown', e => {
    if (e.key === 'Enter')  saveList();
    if (e.key === 'Escape') closeNewList();
});
</script>
@endpush