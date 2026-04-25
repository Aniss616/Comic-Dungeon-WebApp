@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="space-y-8">

        {{-- HEADER --}}
        <div>
            <h2 class="text-2xl font-black text-yellow-400 uppercase tracking-widest">Dashboard</h2>
            <p class="text-zinc-500 text-sm mt-1">Import data and explore your Comic Dungeon</p>
        </div>

        {{-- STATS --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach ([
                ['label' => 'Characters', 'value' => $stats['characters'], 'icon' => '🦸'],
                ['label' => 'Volumes',    'value' => $stats['volumes'],    'icon' => '📚'],
                ['label' => 'Issues',     'value' => $stats['issues'],     'icon' => '📄'],
                ['label' => 'Publishers', 'value' => $stats['publishers'], 'icon' => '🏢'],
                ['label' => 'People',     'value' => $stats['people'],     'icon' => '✏️'],
            ] as $stat)
                <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 text-center">
                    <div class="text-2xl mb-1">{{ $stat['icon'] }}</div>
                    <div class="text-2xl font-black text-yellow-400">{{ $stat['value'] }}</div>
                    <div class="text-zinc-500 text-xs uppercase tracking-wider mt-1">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- IMPORT PANEL --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest mb-6">⚙️ Import Data</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Import Issues by Volume ID --}}
                <div class="bg-zinc-800 rounded-xl p-4 space-y-3">
                    <label class="text-zinc-300 text-sm font-semibold">Import Issues by Volume ID</label>
                    <input
                        type="number"
                        id="volumeIdInput"
                        placeholder="e.g. 4050"
                        class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-4 py-2 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-yellow-400 transition"
                    />
                    <button
                        onclick="importIssues()"
                        class="w-full bg-yellow-400 hover:bg-yellow-300 text-zinc-950 font-bold py-2 rounded-lg transition text-sm uppercase tracking-wide">
                        Import Issues
                    </button>
                    <p id="importIssuesMsg" class="text-xs text-zinc-500"></p>
                </div>

                {{-- Import Single Volume --}}
                <div class="bg-zinc-800 rounded-xl p-4 space-y-3">
                    <label class="text-zinc-300 text-sm font-semibold">Import Single Volume</label>
                    <input
                        type="number"
                        id="singleVolumeInput"
                        placeholder="e.g. 4050"
                        class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-4 py-2 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-yellow-400 transition"
                    />
                    <button
                        onclick="importVolume()"
                        class="w-full bg-yellow-400 hover:bg-yellow-300 text-zinc-950 font-bold py-2 rounded-lg transition text-sm uppercase tracking-wide">
                        Import Volume
                    </button>
                    <p id="importVolumeMsg" class="text-xs text-zinc-500"></p>
                </div>

                {{-- Import Characters --}}
                <div class="bg-zinc-800 rounded-xl p-4 space-y-3">
                    <label class="text-zinc-300 text-sm font-semibold">Import Characters</label>
                    <div class="flex gap-2">
                        <input
                            type="number"
                            id="charLimit"
                            placeholder="Limit (10)"
                            class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-4 py-2 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-yellow-400 transition"
                        />
                        <input
                            type="number"
                            id="charOffset"
                            placeholder="Offset (0)"
                            class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-4 py-2 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-yellow-400 transition"
                        />
                    </div>
                    <button
                        onclick="importCharacters()"
                        class="w-full bg-yellow-400 hover:bg-yellow-300 text-zinc-950 font-bold py-2 rounded-lg transition text-sm uppercase tracking-wide">
                        Import Characters
                    </button>
                    <p id="importCharsMsg" class="text-xs text-zinc-500"></p>
                </div>

                {{-- Import People --}}
                <div class="bg-zinc-800 rounded-xl p-4 space-y-3">
                    <label class="text-zinc-300 text-sm font-semibold">Import People</label>
                    <div class="flex gap-2">
                        <input
                            type="number"
                            id="peopleLimit"
                            placeholder="Limit (10)"
                            class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-4 py-2 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-yellow-400 transition"
                        />
                        <input
                            type="number"
                            id="peopleOffset"
                            placeholder="Offset (0)"
                            class="w-full bg-zinc-700 border border-zinc-600 rounded-lg px-4 py-2 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-yellow-400 transition"
                        />
                    </div>
                    <button
                        onclick="importPeople()"
                        class="w-full bg-yellow-400 hover:bg-yellow-300 text-zinc-950 font-bold py-2 rounded-lg transition text-sm uppercase tracking-wide">
                        Import People
                    </button>
                    <p id="importPeopleMsg" class="text-xs text-zinc-500"></p>
                </div>

            </div>
        </div>
        {{-- SEARCH & IMPORT FROM COMIC VINE --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest mb-6">🔍 Search & Import from Comic Vine</h3>

            {{-- Search Input --}}
            <div class="flex gap-3 mb-6">
                <input
                     type="text"
                     id="comicVineSearch"
                    placeholder="Search e.g. Moon Knight, Batman, X-Men..."
                    class="flex-1 bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-yellow-400 transition"
            />
            <button
                onclick="searchComicVine()"
                class="bg-yellow-400 hover:bg-yellow-300 text-zinc-950 font-bold px-6 py-2.5 rounded-lg transition text-sm uppercase tracking-wide">
            Search
            </button>
        </div>

        <p id="searchMsg" class="text-xs text-zinc-500 mb-4"></p>

        {{-- Results --}}
         <div id="searchResults" class="space-y-6"></div>
        </div>
        {{-- RANDOMIZER --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest mb-6">🎲 Random Character</h3>

            <button
                onclick="randomCharacter()"
                class="bg-yellow-400 hover:bg-yellow-300 text-zinc-950 font-bold py-2.5 px-8 rounded-lg transition text-sm uppercase tracking-wide">
                Roll Random Character
            </button>

            <div id="randomResult" class="mt-6 hidden">
                <div class="bg-zinc-800 rounded-xl p-6 flex flex-col md:flex-row gap-6">
                    <img id="randomImage" src="" alt="" class="w-32 h-32 object-cover object-top rounded-xl border border-zinc-700"/>
                    <div class="space-y-2 flex-1">
                        <h4 id="randomName" class="text-xl font-black text-yellow-400"></h4>
                        <p id="randomDesc" class="text-zinc-400 text-sm"></p>
                        <div>
                            <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">First Appearance</p>
                            <p id="randomFirstAppearance" class="text-zinc-300 text-sm"></p>
                        </div>
                        <div>
                            <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Best Starting Issue</p>
                            <p id="randomBestStart" class="text-zinc-300 text-sm"></p>
                        </div>
                    </div>
                </div>

                {{-- READING PATH --}}
                <div class="mt-4">
                    <p class="text-zinc-500 text-xs uppercase tracking-wider mb-3">Reading Path</p>
                    <div id="readingPath" class="space-y-2"></div>
                </div>
            </div>

            <p id="randomMsg" class="text-xs text-zinc-500 mt-3"></p>
        </div>

        {{-- RECENT DATA --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Recent Volumes --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest mb-4">📚 Recent Volumes</h3>
                @forelse ($recentVolumes as $volume)
                    <div class="flex items-center gap-4 py-3 border-b border-zinc-800 last:border-0">
                        @if ($volume->cover_image)
                            <img src="{{ $volume->cover_image }}" class="w-10 h-14 object-cover rounded" />
                        @else
                            <div class="w-10 h-14 bg-zinc-800 rounded flex items-center justify-center text-zinc-600 text-xs">N/A</div>
                        @endif
                        <div>
                            <p class="text-zinc-100 font-semibold text-sm">{{ $volume->name }}</p>
                            <p class="text-zinc-500 text-xs">{{ $volume->publisher->name ?? 'Unknown Publisher' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-zinc-600 text-sm">No volumes imported yet.</p>
                @endforelse
            </div>

            {{-- Recent Characters --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest mb-4">🦸 Recent Characters</h3>
                @forelse ($recentCharacters as $character)
                    <div class="flex items-center gap-4 py-3 border-b border-zinc-800 last:border-0">
                        @if ($character->image)
                            <img src="{{ $character->image }}" class="w-10 h-10 object-cover object-top rounded-full border border-zinc-700" />
                        @else
                            <div class="w-10 h-10 bg-zinc-800 rounded-full flex items-center justify-center text-zinc-600 text-xs">?</div>
                        @endif
                        <div>
                            <p class="text-zinc-100 font-semibold text-sm">{{ $character->name }}</p>
                            <p class="text-zinc-500 text-xs">{{ $character->description ?? 'No description' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-zinc-600 text-sm">No characters imported yet.</p>
                @endforelse
            </div>

        </div>

    </div>

@endsection

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
        el.className = `text-xs mt-1 ${ok ? 'text-green-400' : 'text-red-400'}`;
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
        document.getElementById('randomImage').src         = c.image ?? '';
        document.getElementById('randomName').textContent  = c.name ?? '';
        document.getElementById('randomDesc').textContent  = c.description ?? '';

        const fa = data.first_appearance;
        document.getElementById('randomFirstAppearance').textContent = fa
            ? `${fa.name ?? 'Unknown'} #${fa.issue_number ?? '?'}`
            : 'Unknown';

        const bs = data.best_starting_issue;
        document.getElementById('randomBestStart').textContent = bs
            ? `${bs.name ?? 'Unknown'} #${bs.issue_number ?? '?'}`
            : 'Unknown';

        // Reading path
        const path = data.reading_path ?? [];
        const pathEl = document.getElementById('readingPath');
        pathEl.innerHTML = path.map(issue => `
            <div class="flex items-center gap-3 bg-zinc-800 rounded-lg px-4 py-2">
                <span class="text-yellow-400 font-bold text-sm">#${issue.issue_number ?? '?'}</span>
                <span class="text-zinc-300 text-sm">${issue.name ?? 'Untitled'}</span>
                <span class="text-zinc-600 text-xs ml-auto">${issue.cover_date ?? ''}</span>
            </div>
        `).join('');

        document.getElementById('randomResult').classList.remove('hidden');
        setMsg('randomMsg', '', true);
    }
    async function searchComicVine() {
    const q = document.getElementById('comicVineSearch').value.trim();
    if (!q) return setMsg('searchMsg', 'Please enter a search term', false);

    setMsg('searchMsg', 'Searching Comic Vine...', true);
    document.getElementById('searchResults').innerHTML = '';

    const data = await callApi(`${apiBase}/search/comicvine?q=${encodeURIComponent(q)}`, 'GET');

    if (!data.results || data.results.length === 0) {
        return setMsg('searchMsg', 'No results found', false);
    }

    setMsg('searchMsg', `Found ${data.results.length} results`, true);

    const characters = data.results.filter(r => r.resource_type === 'character');
    const volumes    = data.results.filter(r => r.resource_type === 'volume');
    const container  = document.getElementById('searchResults');

    if (characters.length > 0) {
        container.innerHTML += `
            <div>
                <p class="text-zinc-500 text-xs uppercase tracking-wider mb-3">
                    Characters (${characters.length})
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    ${characters.map(c => `
                        <div class="bg-zinc-800 rounded-xl p-4 flex items-center gap-4">
                            <img
                                src="${c.image?.small_url ?? ''}"
                                class="w-12 h-12 object-cover object-top rounded-full border border-zinc-700 flex-shrink-0"
                                onerror="this.style.display='none'"
                            />
                            <div class="flex-1 min-w-0">
                                <p class="text-zinc-100 font-semibold text-sm truncate">${c.name ?? 'Unknown'}</p>
                                <p class="text-zinc-500 text-xs truncate">${c.deck ?? 'No description'}</p>
                            </div>
                            <button
                                onclick="importCharacterById(${c.id}, this)"
                                class="flex-shrink-0 bg-yellow-400 hover:bg-yellow-300 text-zinc-950 font-bold px-3 py-1.5 rounded-lg transition text-xs uppercase">
                                Import
                            </button>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    if (volumes.length > 0) {
        container.innerHTML += `
            <div>
                <p class="text-zinc-500 text-xs uppercase tracking-wider mb-3">
                    Volumes (${volumes.length})
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    ${volumes.map(v => `
                        <div class="bg-zinc-800 rounded-xl p-4 flex items-center gap-4">
                            <img
                                src="${v.image?.small_url ?? ''}"
                                class="w-12 h-16 object-cover object-top rounded border border-zinc-700 flex-shrink-0"
                                onerror="this.style.display='none'"
                            />
                            <div class="flex-1 min-w-0">
                                <p class="text-zinc-100 font-semibold text-sm truncate">${v.name ?? 'Unknown'}</p>
                                <p class="text-zinc-500 text-xs truncate">${v.deck ?? 'No description'}</p>
                            </div>
                            <button
                                onclick="importVolumeWithIssues(${v.id}, this)"
                                class="flex-shrink-0 bg-yellow-400 hover:bg-yellow-300 text-zinc-950 font-bold px-3 py-1.5 rounded-lg transition text-xs uppercase">
                                Import
                            </button>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }
}

async function importCharacterById(id, btn) {
    btn.textContent = '...';
    btn.disabled = true;
    const data = await callApi(`${apiBase}/import/characters/${id}`);
    btn.textContent = data.data ? '✓ Done' : '✗ Failed';
    btn.classList.replace('bg-yellow-400', data.data ? 'bg-green-500' : 'bg-red-500');
}

async function importVolumeWithIssues(id, btn) {
    btn.textContent = '...';
    btn.disabled = true;
    const data = await callApi(`${apiBase}/import/volumes/${id}/issues`);
    btn.textContent = data.message ? '✓ Done' : '✗ Failed';
    btn.classList.replace('bg-yellow-400', data.message ? 'bg-green-500' : 'bg-red-500');
}
document.getElementById('comicVineSearch').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') searchComicVine();
});
</script>
@endpush