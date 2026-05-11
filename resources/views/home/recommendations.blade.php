@auth
    @if ($recommendedIssues->isNotEmpty())
        <div>
            <div class="section-heading">
                <h2 class="section-title">Recommended for You</h2>
                <div class="section-rule"></div>
            </div>
            <p style="font-size:13px; color:var(--sl-muted); margin-top:-0.75rem; margin-bottom:1.25rem;">
                Based on your favourited issues and characters.
            </p>

            <div style="display:grid; grid-template-columns:repeat(6,1fr); gap:1.125rem;">
                @foreach ($recommendedIssues as $issue)
                    <a href="{{ route('issues.show', $issue->id) }}" class="cover-card">
                        <div class="cover-card-img">
                            @if ($issue->image)
                                <img src="{{ $issue->image }}" alt="{{ $issue->name ?? 'Issue #'.$issue->issue_number }}" loading="lazy">
                            @else
                                <div class="cover-card-placeholder">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:2rem; height:2rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.966 8.966 0 00-6 2.292m0-14.25v14.25"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="cover-card-body">
                            @if ($issue->volume)
                                <div style="font-size:10px; font-family:var(--font-display); font-weight:700; letter-spacing:0.06em; text-transform:uppercase; color:var(--sl-red); margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $issue->volume->name }}
                                </div>
                            @endif
                            <div class="cover-card-title">
                                @if ($issue->name && $issue->name !== 'TBD')
                                    {{ $issue->name }}
                                @else
                                    Issue #{{ $issue->issue_number }}
                                @endif
                            </div>
                            @if ($issue->issue_number)
                                <div class="cover-card-meta">#{{ $issue->issue_number }}</div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div style="
            background:var(--sl-raised);
            border:1px solid var(--sl-border);
            border-radius:var(--sl-radius-lg);
            padding:1.5rem;
        ">
            <div class="section-heading" style="margin-bottom:0.5rem;">
                <h2 class="section-title" style="font-size:1.1rem;">Recommended for You</h2>
            </div>
            <p style="font-size:13px; color:var(--sl-muted);">
                Favourite some issues or characters and we'll surface more of what you love.
            </p>
        </div>
    @endif
@endauth