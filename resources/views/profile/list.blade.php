@extends('layouts.app')

@section('title', $list->name . ' — ' . $user->display_name)

@push('styles')
<style>
.list-page-wrap {
    max-width: 860px;
    margin: 0 auto;
    padding: 40px 24px 80px;
}

.list-back {
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
    margin-bottom: 28px;
    transition: color .15s;
}
.list-back:hover { color: var(--sl-red); }

.list-page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 32px;
}

.list-page-title {
    font-family: var(--font-display);
    font-size: 36px;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: var(--sl-text);
    line-height: 1.1;
}

.list-page-meta {
    font-size: 13px;
    color: var(--sl-muted);
    margin-top: 6px;
}

.list-delete-btn {
    background: transparent;
    border: 1px solid var(--sl-border-md);
    color: var(--sl-muted);
    font-family: var(--font-display);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    padding: 7px 14px;
    border-radius: 4px;
    cursor: pointer;
    flex-shrink: 0;
    transition: border-color .15s, color .15s;
}
.list-delete-btn:hover { border-color: var(--sl-red); color: var(--sl-red); }

/* ── Search to add ───────────────────────────────────────── */
.add-section {
    background: var(--sl-raised);
    border: 1px solid var(--sl-border);
    border-radius: 6px;
    padding: 16px 18px;
    margin-bottom: 24px;
}

.add-section-label {
    font-family: var(--font-display);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: var(--sl-muted);
    margin-bottom: 10px;
}

.issue-search-wrap { position: relative; }

.issue-search-input {
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
.issue-search-input:focus { border-color: var(--sl-red); }
.issue-search-input::placeholder { color: var(--sl-faint); }

.issue-search-results {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: var(--sl-raised);
    border: 1px solid var(--sl-border-md);
    border-radius: 4px;
    z-index: 50;
    max-height: 280px;
    overflow-y: auto;
}
.issue-search-results.open { display: block; }

.issue-result {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    cursor: pointer;
    border-bottom: 1px solid var(--sl-border);
    transition: background .1s;
}
.issue-result:last-child { border-bottom: none; }
.issue-result:hover { background: var(--sl-surface); }

.issue-result-thumb {
    width: 28px;
    height: 40px;
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
    border: 1px solid var(--sl-border);
}
.issue-result-thumb img { width: 100%; height: 100%; object-fit: cover; }

.issue-result-info { flex: 1; min-width: 0; }

.issue-result-name {
    font-size: 13px;
    color: var(--sl-text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.issue-result-vol {
    font-size: 11px;
    color: var(--sl-muted);
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.issue-result-added {
    font-family: var(--font-display);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .05em;
    color: var(--sl-amber);
    flex-shrink: 0;
}

/* ── Issues list ─────────────────────────────────────────── */
.list-issues-section-label {
    font-family: var(--font-display);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--sl-muted);
    margin-bottom: 10px;
}

.list-issues {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.list-issue-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    background: var(--sl-raised);
    border: 1px solid var(--sl-border);
    border-radius: 4px;
    transition: border-color .15s;
}
.list-issue-item:hover { border-color: rgba(192,57,43,0.25); }

.list-issue-thumb {
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
    border: 1px solid var(--sl-border);
}
.list-issue-thumb img { width: 100%; height: 100%; object-fit: cover; }

.list-issue-info { flex: 1; min-width: 0; }

.list-issue-name {
    font-size: 13px;
    color: var(--sl-text);
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-decoration: none;
    display: block;
}
.list-issue-name:hover { color: var(--sl-red); }

.list-issue-vol {
    font-size: 11px;
    color: var(--sl-muted);
    margin-top: 2px;
}

.list-issue-remove {
    background: transparent;
    border: none;
    color: var(--sl-faint);
    cursor: pointer;
    padding: 6px;
    font-size: 15px;
    line-height: 1;
    display: flex;
    align-items: center;
    transition: color .15s;
    flex-shrink: 0;
}
.list-issue-remove:hover { color: var(--sl-red); }

.list-empty {
    padding: 48px 0;
    text-align: center;
    color: var(--sl-muted);
    font-size: 13px;
}
.list-empty strong {
    display: block;
    font-family: var(--font-display);
    font-size: 16px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: var(--sl-text);
    margin-bottom: 6px;
}
</style>
@endpush

@section('content')
<div class="list-page-wrap">

    <a href="{{ route('profile') }}#lists" class="list-back">&#8592; Back to Profile</a>

    <div class="list-page-header">
        <div>
            <div class="list-page-title">{{ $list->name }}</div>
            <div class="list-page-meta" id="list-count-label">
                {{ $list->issues->count() }} {{ Str::plural('issue', $list->issues->count()) }}
            </div>
        </div>
        <button class="list-delete-btn" onclick="deleteList()">Delete List</button>
    </div>

    {{-- ── Add issues ──────────────────────────────────────── --}}
    <div class="add-section">
        <div class="add-section-label">Add Issues</div>
        <div class="issue-search-wrap">
            <input
                type="text"
                class="issue-search-input"
                id="issue-search"
                placeholder="Search by issue title…"
                autocomplete="off"
            >
            <div class="issue-search-results" id="issue-results"></div>
        </div>
    </div>

    {{-- ── Issues in list ──────────────────────────────────── --}}
    <div class="list-issues-section-label">Issues in this list</div>

    <div class="list-issues" id="list-issues">
        @forelse($list->issues as $issue)
        <div class="list-issue-item" id="list-issue-{{ $issue->id }}">
            <div class="list-issue-thumb">
                @if($issue->image)
                    <img src="{{ $issue->image }}" alt="{{ $issue->name }}">
                @else
                    {{ strtoupper(substr($issue->name ?? '??', 0, 2)) }}
                @endif
            </div>
            <div class="list-issue-info">
                <a href="{{ route('issues.show', $issue->id) }}" class="list-issue-name">{{ $issue->name }}</a>
                <div class="list-issue-vol">{{ $issue->volume->name ?? '' }}</div>
            </div>
            <button
                class="list-issue-remove"
                onclick="removeIssue({{ $issue->id }})"
                aria-label="Remove from list"
            >&#10005;</button>
        </div>
        @empty
        <div class="list-empty" id="list-empty-state">
            <strong>No issues yet</strong>
            Search above to add issues to this list.
        </div>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
<script>
const LIST_ID   = {{ $list->id }};
const CSRF      = document.querySelector('meta[name="csrf-token"]').content;

// Track issue IDs already in the list for instant UI feedback in search results
const inList = new Set([
    @foreach($list->issues as $issue) {{ $issue->id }}, @endforeach
]);

// ── Issue search ────────────────────────────────────────────────────
const issueSearch  = document.getElementById('issue-search');
const issueResults = document.getElementById('issue-results');
let searchTimer = null;

issueSearch.addEventListener('input', () => {
    clearTimeout(searchTimer);
    const q = issueSearch.value.trim();
    if (q.length < 2) { issueResults.classList.remove('open'); return; }
    searchTimer = setTimeout(() => fetchIssues(q), 250);
});

document.addEventListener('click', (e) => {
    if (!e.target.closest('.issue-search-wrap')) issueResults.classList.remove('open');
});

function fetchIssues(q) {
    fetch(`/profile/issues/search?q=${encodeURIComponent(q)}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(issues => {
        issueResults.innerHTML = '';
        if (!issues.length) {
            issueResults.innerHTML = '<div style="padding:12px;font-size:12px;color:var(--sl-faint);">No issues found.</div>';
        } else {
            issues.forEach(issue => {
                const already = inList.has(issue.id);
                const row = document.createElement('div');
                row.className = 'issue-result';
                row.innerHTML = `
                    <div class="issue-result-thumb">
                        ${issue.image
                            ? `<img src="${issue.image}" alt="${issue.name}">`
                            : issue.name.substring(0, 2).toUpperCase()}
                    </div>
                    <div class="issue-result-info">
                        <div class="issue-result-name">${issue.name}</div>
                        <div class="issue-result-vol">${issue.vol_name}</div>
                    </div>
                    ${already ? '<span class="issue-result-added">Added</span>' : ''}`;

                if (!already) {
                    row.addEventListener('click', () => addIssue(issue, row));
                } else {
                    row.style.opacity = '0.5';
                    row.style.cursor  = 'default';
                }

                issueResults.appendChild(row);
            });
        }
        issueResults.classList.add('open');
    });
}

// ── Add issue ───────────────────────────────────────────────────────
function addIssue(issue, row) {
    fetch(`/profile/lists/${LIST_ID}/issues`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ issue_id: issue.id }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'added' || data.status === 'exists') {
            inList.add(issue.id);

            // Mark row as added
            row.style.opacity = '0.5';
            row.style.cursor  = 'default';
            row.removeEventListener('click', () => {});
            const badge = document.createElement('span');
            badge.className = 'issue-result-added';
            badge.textContent = 'Added';
            row.appendChild(badge);

            if (data.status === 'added') {
                // Remove empty state if present
                const empty = document.getElementById('list-empty-state');
                if (empty) empty.remove();

                // Prepend to list
                prependIssueRow(data.issue);
                updateCountLabel(1);
            }
        }
    });
}

function prependIssueRow(issue) {
    const list = document.getElementById('list-issues');
    const el   = document.createElement('div');
    el.className = 'list-issue-item';
    el.id        = 'list-issue-' + issue.id;
    el.innerHTML = `
        <div class="list-issue-thumb">
            ${issue.image
                ? `<img src="${issue.image}" alt="${issue.name}">`
                : issue.name.substring(0, 2).toUpperCase()}
        </div>
        <div class="list-issue-info">
            <a href="/issues/${issue.id}" class="list-issue-name">${issue.name}</a>
            <div class="list-issue-vol">${issue.vol_name}</div>
        </div>
        <button class="list-issue-remove" onclick="removeIssue(${issue.id})" aria-label="Remove from list">&#10005;</button>`;
    list.prepend(el);
}

// ── Remove issue ────────────────────────────────────────────────────
function removeIssue(issueId) {
    fetch(`/profile/lists/${LIST_ID}/issues/${issueId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'removed') {
            inList.delete(issueId);
            const el = document.getElementById('list-issue-' + issueId);
            if (el) {
                el.style.transition = 'opacity .2s';
                el.style.opacity    = '0';
                setTimeout(() => {
                    el.remove();
                    updateCountLabel(-1);
                    // Show empty state if list is now empty
                    if (document.getElementById('list-issues').children.length === 0) {
                        document.getElementById('list-issues').innerHTML =
                            `<div class="list-empty" id="list-empty-state">
                                <strong>No issues yet</strong>
                                Search above to add issues to this list.
                            </div>`;
                    }
                }, 200);
            }
        }
    });
}

// ── Delete list ─────────────────────────────────────────────────────
function deleteList() {
    if (!confirm('Delete this list? This cannot be undone.')) return;

    fetch(`/profile/lists/${LIST_ID}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'deleted') {
            window.location.href = '{{ route("profile") }}';
        }
    });
}

// ── Count label ─────────────────────────────────────────────────────
let currentCount = {{ $list->issues->count() }};
function updateCountLabel(delta) {
    currentCount += delta;
    document.getElementById('list-count-label').textContent =
        currentCount + ' ' + (currentCount === 1 ? 'issue' : 'issues');
}
</script>
@endpush