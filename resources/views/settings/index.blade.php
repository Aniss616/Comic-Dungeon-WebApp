@extends('layouts.app')

@section('title', 'Settings')

@push('styles')
<style>
.settings-wrap {
    max-width: 760px;
    margin: 0 auto;
    padding: 40px 24px 80px;
}

.settings-back {
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
.settings-back:hover { color: var(--sl-red); }

.settings-title {
    font-family: var(--font-display);
    font-size: 32px;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: var(--sl-text);
    margin-bottom: 40px;
}

/* ── Section blocks ──────────────────────────────────────── */
.settings-section {
    background: var(--sl-raised);
    border: 1px solid var(--sl-border);
    border-radius: 6px;
    margin-bottom: 24px;
    overflow: hidden;
}

.settings-section-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--sl-border);
}

.settings-section-title {
    font-family: var(--font-display);
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--sl-text);
}

.settings-section-sub {
    font-size: 12px;
    color: var(--sl-muted);
    margin-top: 3px;
}

.settings-section-body {
    padding: 20px;
}

/* ── Form fields ─────────────────────────────────────────── */
.settings-field {
    margin-bottom: 18px;
}
.settings-field:last-child { margin-bottom: 0; }

.settings-label {
    display: block;
    font-family: var(--font-display);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--sl-muted);
    margin-bottom: 7px;
}

.settings-input {
    width: 100%;
    background: var(--sl-surface);
    border: 1px solid var(--sl-border-md);
    border-radius: 4px;
    color: var(--sl-text);
    font-family: var(--font-body);
    font-size: 14px;
    padding: 9px 12px;
    outline: none;
    transition: border-color .15s;
}
.settings-input:focus { border-color: var(--sl-red); }
.settings-input::placeholder { color: var(--sl-faint); }
.settings-input:disabled {
    opacity: .45;
    cursor: not-allowed;
}

.settings-hint {
    font-size: 11px;
    color: var(--sl-faint);
    margin-top: 5px;
}

.settings-save {
    background: var(--sl-red);
    border: none;
    color: var(--sl-text);
    font-family: var(--font-display);
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    padding: 10px 24px;
    border-radius: 4px;
    cursor: pointer;
    transition: opacity .15s;
    margin-top: 4px;
}
.settings-save:hover { opacity: .85; }

/* ── Flash ───────────────────────────────────────────────── */
.settings-flash {
    padding: 12px 16px;
    border-radius: 4px;
    font-size: 13px;
    margin-bottom: 20px;
}
.settings-flash.success {
    background: rgba(46,160,67,0.12);
    border: 1px solid rgba(46,160,67,0.3);
    color: #3fb950;
}

/* ── Pinned volumes ──────────────────────────────────────── */
.pin-slots {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 20px;
}

.pin-slot {
    background: var(--sl-surface);
    border: 1px dashed var(--sl-border-md);
    border-radius: 4px;
    overflow: hidden;
    position: relative;
    cursor: pointer;
    transition: border-color .15s;
    aspect-ratio: 2/3;
    display: flex;
    flex-direction: column;
}
.pin-slot:hover { border-color: rgba(192,57,43,0.4); }
.pin-slot.filled { border-style: solid; border-color: var(--sl-border-md); cursor: default; }

.pin-slot-cover {
    flex: 1;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-display);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: var(--sl-faint);
    padding: 8px;
    text-align: center;
    line-height: 1.3;
}
.pin-slot-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.pin-slot-name {
    font-family: var(--font-display);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: var(--sl-muted);
    padding: 5px 7px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    border-top: 1px solid var(--sl-border);
    background: var(--sl-raised);
}

.pin-slot-remove {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 20px;
    height: 20px;
    background: rgba(13,13,13,0.75);
    border: 1px solid var(--sl-border-md);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--sl-muted);
    font-size: 11px;
    line-height: 1;
    transition: color .15s, background .15s;
    text-decoration: none;
}
.pin-slot-remove:hover {
    background: var(--sl-red);
    color: var(--sl-text);
    border-color: var(--sl-red);
}

/* ── Volume search dropdown ──────────────────────────────── */
.pin-search-wrap {
    position: relative;
}

.pin-search-input {
    width: 100%;
    background: var(--sl-surface);
    border: 1px solid var(--sl-border-md);
    border-radius: 4px;
    color: var(--sl-text);
    font-family: var(--font-body);
    font-size: 13px;
    padding: 9px 12px;
    outline: none;
    transition: border-color .15s;
}
.pin-search-input:focus { border-color: var(--sl-red); }
.pin-search-input::placeholder { color: var(--sl-faint); }

.pin-search-results {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: var(--sl-raised);
    border: 1px solid var(--sl-border-md);
    border-radius: 4px;
    z-index: 50;
    max-height: 260px;
    overflow-y: auto;
}
.pin-search-results.open { display: block; }

.pin-result {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    cursor: pointer;
    transition: background .1s;
    border-bottom: 1px solid var(--sl-border);
}
.pin-result:last-child { border-bottom: none; }
.pin-result:hover { background: var(--sl-surface); }

.pin-result-thumb {
    width: 24px;
    height: 34px;
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
}
.pin-result-thumb img { width: 100%; height: 100%; object-fit: cover; }

.pin-result-name {
    flex: 1;
    font-size: 13px;
    color: var(--sl-text);
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.pin-search-hint {
    font-size: 11px;
    color: var(--sl-faint);
    margin-top: 8px;
}

.pin-slot-empty-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}
.pin-plus {
    font-size: 20px;
    color: var(--sl-faint);
    line-height: 1;
}
.pin-empty-text {
    font-family: var(--font-display);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: var(--sl-faint);
}
</style>
@endpush

@section('content')
<div class="settings-wrap">

    <a href="{{ route('profile') }}" class="settings-back">&#8592; Back to Profile</a>

    <div class="settings-title">Settings</div>

    @if(session('success'))
        <div class="settings-flash success">{{ session('success') }}</div>
    @endif

    {{-- ── Profile details ────────────────────────────────── --}}
    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="settings-section">
            <div class="settings-section-header">
                <div class="settings-section-title">Profile</div>
                <div class="settings-section-sub">How you appear across Comic Dungeon</div>
            </div>
            <div class="settings-section-body">

                <div class="settings-field">
                    <label class="settings-label" for="display_name">Display Name</label>
                    <input
                        type="text"
                        id="display_name"
                        name="display_name"
                        class="settings-input"
                        value="{{ old('display_name', $user->display_name !== $user->username ? $user->display_name : '') }}"
                        placeholder="{{ $user->username }}"
                        maxlength="60"
                    >
                    <div class="settings-hint">Leave blank to use your username.</div>
                </div>

                <div class="settings-field">
                    <label class="settings-label">Username</label>
                    <input
                        type="text"
                        class="settings-input"
                        value="{{ $user->username }}"
                        disabled
                    >
                    <div class="settings-hint">Username cannot be changed.</div>
                </div>

                <div class="settings-field">
                    <label class="settings-label">Email</label>
                    <input
                        type="text"
                        class="settings-input"
                        value="{{ $user->email }}"
                        disabled
                    >
                </div>

                <button type="submit" class="settings-save">Save Changes</button>

            </div>
        </div>
    </form>

    {{-- ── Pinned volumes ──────────────────────────────────── --}}
    <div class="settings-section">
        <div class="settings-section-header">
            <div class="settings-section-title">Pinned Volumes</div>
            <div class="settings-section-sub">Choose up to 4 volumes to display on your profile overview</div>
        </div>
        <div class="settings-section-body">

            <div class="pin-slots" id="pin-slots">
                @for($pos = 0; $pos < 4; $pos++)
                    @php $pinned = $pinnedVolumes->firstWhere('position', $pos); @endphp
                    <div class="pin-slot {{ $pinned ? 'filled' : '' }}" data-position="{{ $pos }}" id="pin-slot-{{ $pos }}">
                        @if($pinned)
                            <div class="pin-slot-cover">
                                @if($pinned->cover_image)
                                    <img src="{{ $pinned->cover_image }}" alt="{{ $pinned->name }}">
                                @else
                                    {{ strtoupper(substr($pinned->name, 0, 2)) }}
                                @endif
                            </div>
                            <div class="pin-slot-name">{{ $pinned->name }}</div>
                            <button
                                class="pin-slot-remove"
                                onclick="unpinVolume({{ $pos }}, event)"
                                aria-label="Remove"
                                title="Remove"
                            >&#10005;</button>
                        @else
                            <div class="pin-slot-cover">
                                <div class="pin-slot-empty-label" onclick="focusPinSearch()">
                                    <span class="pin-plus">+</span>
                                    <span class="pin-empty-text">Pin a volume</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endfor
            </div>

            <div class="pin-search-wrap">
                <input
                    type="text"
                    class="pin-search-input"
                    id="pin-search"
                    placeholder="Search volumes to pin…"
                    autocomplete="off"
                >
                <div class="pin-search-results" id="pin-results"></div>
            </div>
            <div class="pin-search-hint">Click a result to pin it to the next available slot.</div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ── Pin search ──────────────────────────────────────────────────────
const pinSearch  = document.getElementById('pin-search');
const pinResults = document.getElementById('pin-results');
let searchTimer  = null;

function focusPinSearch() {
    pinSearch.focus();
    pinSearch.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

pinSearch.addEventListener('input', () => {
    clearTimeout(searchTimer);
    const q = pinSearch.value.trim();
    if (q.length < 2) { pinResults.classList.remove('open'); return; }
    searchTimer = setTimeout(() => fetchVolumes(q), 250);
});

pinSearch.addEventListener('focus', () => {
    if (pinSearch.value.trim().length >= 2) pinResults.classList.add('open');
});

document.addEventListener('click', (e) => {
    if (!e.target.closest('.pin-search-wrap')) pinResults.classList.remove('open');
});

function fetchVolumes(q) {
    fetch(`/settings/volumes/search?q=${encodeURIComponent(q)}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(volumes => {
        pinResults.innerHTML = '';
        if (!volumes.length) {
            pinResults.innerHTML = '<div style="padding:12px;font-size:12px;color:var(--sl-faint);">No volumes found.</div>';
        } else {
            volumes.forEach(vol => {
                const row = document.createElement('div');
                row.className = 'pin-result';
                row.innerHTML = `
                    <div class="pin-result-thumb">
                        ${vol.cover_image
                            ? `<img src="${vol.cover_image}" alt="${vol.name}">`
                            : vol.name.substring(0, 2).toUpperCase()}
                    </div>
                    <span class="pin-result-name">${vol.name}</span>`;
                row.addEventListener('click', () => pinVolume(vol));
                pinResults.appendChild(row);
            });
        }
        pinResults.classList.add('open');
    });
}

// ── Pin / unpin ─────────────────────────────────────────────────────
function nextEmptySlot() {
    for (let i = 0; i < 4; i++) {
        const slot = document.getElementById('pin-slot-' + i);
        if (!slot.classList.contains('filled')) return i;
    }
    return -1;
}

function pinVolume(vol) {
    const pos = nextEmptySlot();
    if (pos === -1) {
        alert('All 4 slots are filled. Remove one first.');
        return;
    }

    fetch('/settings/volumes/pin', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ volume_id: vol.id, position: pos }),
    })
    .then(r => r.json())
    .then(() => {
        fillSlot(pos, vol);
        pinResults.classList.remove('open');
        pinSearch.value = '';
    });
}

function unpinVolume(pos, e) {
    e.stopPropagation();

    fetch('/settings/volumes/unpin', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ position: pos }),
    })
    .then(r => r.json())
    .then(() => clearSlot(pos));
}

function fillSlot(pos, vol) {
    const slot = document.getElementById('pin-slot-' + pos);
    slot.classList.add('filled');
    slot.innerHTML = `
        <div class="pin-slot-cover">
            ${vol.cover_image
                ? `<img src="${vol.cover_image}" alt="${vol.name}">`
                : `<span style="font-family:var(--font-display);font-size:16px;font-weight:800;color:var(--sl-text);">${vol.name.substring(0, 2).toUpperCase()}</span>`}
        </div>
        <div class="pin-slot-name">${vol.name}</div>
        <button class="pin-slot-remove" onclick="unpinVolume(${pos}, event)" aria-label="Remove" title="Remove">&#10005;</button>`;
}

function clearSlot(pos) {
    const slot = document.getElementById('pin-slot-' + pos);
    slot.classList.remove('filled');
    slot.innerHTML = `
        <div class="pin-slot-cover">
            <div class="pin-slot-empty-label" onclick="focusPinSearch()">
                <span class="pin-plus">+</span>
                <span class="pin-empty-text">Pin a volume</span>
            </div>
        </div>`;
}
</script>
@endpush