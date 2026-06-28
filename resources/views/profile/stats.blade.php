@extends('layouts.app')

@section('title', $user->username . ' — Full Stats')

@push('styles')
<style>
.stats-page-wrap {
    max-width: 860px;
    margin: 0 auto;
    padding: 40px 24px;
}
.stats-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: var(--font-display);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--sl-muted);
    text-decoration: none;
    margin-bottom: 32px;
    transition: color .15s;
}
.stats-back:hover { color: var(--sl-red); }
.stats-page-title {
    font-family: var(--font-display);
    font-size: 32px;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: var(--sl-text);
    margin-bottom: 32px;
}
.stats-section-title {
    font-family: var(--font-display);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--sl-muted);
    margin-bottom: 10px;
}
.stats-full-section { margin-bottom: 40px; }
.stats-list  { display:flex; flex-direction:column; gap:2px; }
.stats-item  { display:flex; align-items:center; gap:10px; padding:9px 12px; background:var(--sl-raised); border:1px solid var(--sl-border); border-radius:4px; }
.stats-rank  { font-family:var(--font-display); font-size:12px; font-weight:700; color:var(--sl-faint); width:24px; text-align:right; flex-shrink:0; }
.stats-thumb { width:28px; height:28px; border-radius:50%; background:var(--sl-surface); border:1px solid var(--sl-border-md); flex-shrink:0; overflow:hidden; display:flex; align-items:center; justify-content:center; font-family:var(--font-display); font-size:10px; font-weight:700; color:var(--sl-faint); }
.stats-thumb.square { border-radius:3px; width:24px; height:34px; }
.stats-thumb img { width:100%; height:100%; object-fit:cover; }
.stats-name  { flex:1; font-size:13px; color:var(--sl-text); min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.stats-name a { color:inherit; text-decoration:none; }
.stats-name a:hover { color:var(--sl-red); }
.stats-bar-wrap { width:100px; height:4px; background:rgba(192,57,43,0.22); border-radius:2px; overflow:hidden; flex-shrink:0; }
.stats-bar-fill { height:100%; background:#C0392B; border-radius:2px; transition: width .3s ease; }
.stats-count { font-size:12px; color:var(--sl-muted); width:44px; text-align:right; flex-shrink:0; font-family:var(--font-display); font-weight:600; }
.stats-heart { font-size:13px; color:var(--sl-amber); flex-shrink:0; width:16px; }

.stats-hidden { display: none; }

.show-more-btn {
    background: transparent;
    border: 1px solid var(--sl-border-md);
    color: var(--sl-muted);
    font-family: var(--font-display);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    padding: 8px 16px;
    cursor: pointer;
    border-radius: 4px;
    margin-top: 8px;
    width: 100%;
    transition: border-color .15s, color .15s;
}
.show-more-btn:hover { border-color: var(--sl-red); color: var(--sl-text); }
</style>
@endpush

@section('content')
<div class="stats-page-wrap">

    <a href="{{ route('profile') }}" class="stats-back">&#8592; Back to Profile</a>

    <div class="stats-page-title">{{ $user->display_name }} — Full Stats</div>

    {{-- Characters --}}
    <div class="stats-full-section">
        <div class="stats-section-title">All Characters — by issues read</div>
        <div class="stats-list" id="chars-list">
            @foreach($allCharacters as $i => $char)
            <div class="stats-item {{ $i >= 5 ? 'stats-hidden chars-extra' : '' }}">
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
        @if($allCharacters->count() > 5)
        <button class="show-more-btn" id="chars-more-btn" onclick="showMore('chars')">
            Show {{ $allCharacters->count() - 5 }} more characters
        </button>
        @endif
    </div>

    {{-- Volumes --}}
    <div class="stats-full-section">
        <div class="stats-section-title">All Volumes — by issues read</div>
        <div class="stats-list" id="vols-list">
            @foreach($allVolumes as $i => $vol)
            @php
                $total   = $vol->count_of_issues ?: $vol->read_count;
                $fillPct = $total > 0 ? min(round($vol->read_count / $total * 100), 100) : 0;
            @endphp
            <div class="stats-item {{ $i >= 5 ? 'stats-hidden vols-extra' : '' }}">
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
        @if($allVolumes->count() > 5)
        <button class="show-more-btn" id="vols-more-btn" onclick="showMore('vols')">
            Show {{ $allVolumes->count() - 5 }} more volumes
        </button>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function showMore(type) {
    document.querySelectorAll('.' + type + '-extra').forEach(el => {
        el.classList.remove('stats-hidden');
    });
    document.getElementById(type + '-more-btn').style.display = 'none';
}
</script>
@endpush