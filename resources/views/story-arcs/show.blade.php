@extends('layouts.app')

@section('title', $storyArc->name . ' — Story Arc')

@section('content')
<div class="page-main">

    <header class="page-header">
        <p class="page-header-eyebrow">Story Arc</p>
        <h1 class="page-header-title">{{ $storyArc->name }}</h1>
        <p class="page-header-sub">
            {{ $issues->count() }} {{ Str::plural('issue', $issues->count()) }}
        </p>
    </header>

    <div class="divider"></div>

    @if($issues->isEmpty())
        <p style="color:var(--sl-muted); font-family:var(--font-body);">
            No issues found for this story arc.
        </p>
    @else
        <div class="grid-5">
            @foreach($issues as $issue)
                <a href="{{ route('issues.show', $issue) }}" class="cover-card">

                    <div class="cover-card-img">
                        @if($issue->image)
                            <img src="{{ $issue->image }}" alt="{{ $issue->name }}" loading="lazy">
                        @else
                            <div class="cover-card-placeholder">No Image</div>
                        @endif
                    </div>

                    <div class="cover-card-body" style="padding:0.6rem 0.75rem;">
                        <div class="cover-card-title" style="font-size:0.78rem;">
                            {{ $issue->name ?? 'Untitled' }}
                        </div>
                        <div class="cover-card-meta" style="font-size:0.7rem;">
                            @if($issue->volume)
                                {{ $issue->volume->name }} &middot;
                            @endif
                            #{{ $issue->issue_number }}
                            @if($issue->cover_date)
                                &middot; {{ \Carbon\Carbon::parse($issue->cover_date)->format('M Y') }}
                            @endif
                        </div>
                    </div>

                </a>
            @endforeach
        </div>
    @endif

</div>
@endsection