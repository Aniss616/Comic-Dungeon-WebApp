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
@push('scripts')
<script>
    const apiBase = '/api';

    async function callApi(url, method = 'POST') {
        try {
            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            });
            return await res.json();
        } catch (e) {
            return { message: 'Request failed' };
        }
    }

    function setMsg(id, msg, ok = true) {
        const el = document.getElementById(id);
        el.textContent = msg;
        el.style.color = ok ? 'var(--sl-amber)' : 'var(--sl-red)';
    }

    async function importIssues() {
        const id = document.getElementById('volumeIdInput').value;
        if (!id) return setMsg('importIssuesMsg', 'Please enter a volume ID', false);
        setMsg('importIssuesMsg', 'Importing...', true);
        const data = await callApi(`${apiBase}/import/volumes/${id}/issues`);
        setMsg('importIssuesMsg', data.message ?? 'Done', !data.error);
    }

    async function importVolume() {
        const id = document.getElementById('singleVolumeInput').value;
        if (!id) return setMsg('importVolumeMsg', 'Please enter a volume ID', false);
        setMsg('importVolumeMsg', 'Importing...', true);
        const data = await callApi(`${apiBase}/import/volumes/${id}`);
        setMsg('importVolumeMsg', data.message ?? 'Done', !data.error);
    }

    async function importCharacters() {
        const limit  = document.getElementById('charLimit').value  || 10;
        const offset = document.getElementById('charOffset').value || 0;
        setMsg('importCharsMsg', 'Importing...', true);
        const data = await callApi(`${apiBase}/import/characters?limit=${limit}&offset=${offset}`);
        setMsg('importCharsMsg', data.message ?? 'Done', !data.error);
    }

    async function importPeople() {
        const limit  = document.getElementById('peopleLimit').value  || 10;
        const offset = document.getElementById('peopleOffset').value || 0;
        setMsg('importPeopleMsg', 'Importing...', true);
        const data = await callApi(`${apiBase}/import/persons?limit=${limit}&offset=${offset}`);
        setMsg('importPeopleMsg', data.message ?? 'Done', !data.error);
    }

    async function randomCharacter() {
        setMsg('randomMsg', 'Rolling...', true);
        const data = await callApi(`${apiBase}/characters/random`, 'GET');
        if (!data.character) {
            return setMsg('randomMsg', data.message ?? 'No character found', false);
        }
        const c = data.character;
        document.getElementById('randomImage').src        = c.image ?? '';
        document.getElementById('randomName').textContent = c.name ?? '';
        document.getElementById('randomDesc').textContent = c.description ?? '';
        const fa = data.first_appearance;
        document.getElementById('randomFirstAppearance').textContent = fa
            ? `${fa.name ?? 'Unknown'} #${fa.issue_number ?? '?'}` : 'Unknown';
        const bs = data.best_starting_issue;
        document.getElementById('randomBestStart').textContent = bs
            ? `${bs.name ?? 'Unknown'} #${bs.issue_number ?? '?'}` : 'Unknown';
        const path = data.reading_path ?? [];
        const pathEl = document.getElementById('readingPath');
        pathEl.innerHTML = path.map(issue => `
            <div class="char-card">
                <div class="char-info">
                    <div class="char-name">#${issue.issue_number ?? '?'} — ${issue.name ?? 'Untitled'}</div>
                    <div class="char-meta">${issue.cover_date ?? ''}</div>
                </div>
            </div>`).join('');
        document.getElementById('randomResult').classList.remove('hidden');
        setMsg('randomMsg', '', true);
    }

    // ── Comic Vine Search ──────────────────────────────────────────────

    document.getElementById('comicVineSearch').addEventListener('keydown', e => {
        if (e.key === 'Enter') searchComicVine();
    });

    async function searchComicVine() {
        const q = document.getElementById('comicVineSearch').value.trim();
        if (!q) return;
        setMsg('searchMsg', 'Searching...');
        document.getElementById('searchResults').innerHTML = '';
        const data = await callApi(`${apiBase}/search/comicvine?q=${encodeURIComponent(q)}`, 'GET');
        setMsg('searchMsg', '');
        const results = data.results ?? [];
        if (!results.length) { setMsg('searchMsg', 'No results found.'); return; }

        const typeLabel = { character: 'Character', volume: 'Volume', issue: 'Issue', publisher: 'Publisher' };
        const typeBadge = { character: 'badge-red', volume: 'badge-amber', issue: 'badge-neutral', publisher: 'badge-neutral' };

        document.getElementById('searchResults').innerHTML = results.map(r => {
            const img = r.image?.medium_url
                ? `<img class="char-avatar" src="${r.image.medium_url}" onerror="this.style.display='none'">`
                : '<div class="char-avatar-placeholder"></div>';

            const badge = `<span class="badge ${typeBadge[r.resource_type] ?? 'badge-neutral'}">${typeLabel[r.resource_type] ?? r.resource_type}</span>`;

            const action = (() => {
                if (r.imported === 'full') {
                    return `<span style="font-size:11px; color:var(--sl-muted); text-transform:uppercase; letter-spacing:0.06em;">Imported</span>`;
                }
                if (r.imported === 'stub' && r.resource_type === 'character') {
                    return `<button class="btn btn-primary" style="font-size:11px;padding:0.3rem 0.75rem;" onclick="importResult('character',${r.id},this)">Full Import</button>`;
                }
                if (!r.imported && (r.resource_type === 'character' || r.resource_type === 'volume')) {
                    return `<button class="btn btn-primary" style="font-size:11px;padding:0.3rem 0.75rem;" onclick="importResult('${r.resource_type}',${r.id},this)">Import</button>`;
                }
                return '';
            })();

            return `
                <div class="char-card" style="margin-bottom:0.5rem; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:0.75rem; min-width:0;">
                        ${img}
                        <div class="char-info">
                            <div class="char-name">${r.name ?? 'Unknown'} ${badge}</div>
                            <div class="char-meta">${r.deck ?? ''}</div>
                        </div>
                    </div>
                    <div style="flex-shrink:0; margin-left:1rem;">${action}</div>
                </div>`;
        }).join('');
    }

    async function importResult(type, id, btn) {
        btn.disabled = true;
        btn.textContent = 'Importing...';
        let url;
        if (type === 'character') url = `${apiBase}/import/characters/${id}`;
        if (type === 'volume')    url = `${apiBase}/import/volumes/${id}`;
        const data = await callApi(url, 'POST');
        if (data.error) {
            btn.textContent = 'Failed';
            btn.style.color = 'var(--sl-red)';
            btn.disabled = false;
        } else {
            btn.textContent = 'Imported';
            btn.style.opacity = '0.5';
        }
    }
</script>
@endpush
@endsection