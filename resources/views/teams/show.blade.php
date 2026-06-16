@extends('layouts.app')

@section('content')
<div class="page-header">
    <p class="page-header-eyebrow">Team</p>
    <h1 class="page-header-title">{{ $team->name }}</h1>
    @if($team->description)
        <p class="page-header-sub">{{ Str::limit(strip_tags($team->description), 200) }}</p>
    @endif
</div>

<div class="section-heading">
    <h2 class="section-title">Members</h2>
    <div class="section-rule"></div>
    <span class="badge badge-neutral">{{ $characters->count() }}</span>
</div>

<div class="grid-4">
    @foreach($characters as $character)
        <a href="{{ route('characters.show', $character->id) }}" class="char-card">
            @if($character->image)
                <img src="{{ $character->image }}" alt="{{ $character->name }}" class="char-avatar">
            @else
                <div class="char-avatar-placeholder"></div>
            @endif
            <div class="char-info">
                <p class="char-name">{{ $character->name }}</p>
                <p class="char-meta">{{ $character->gender_label }}</p>
            </div>
        </a>
    @endforeach
</div>

<div class="section-heading" style="margin-top:2.5rem;">
    <h2 class="section-title">Issues</h2>
    <div class="section-rule"></div>
    <span class="badge badge-neutral">{{ $issues->count() }}</span>
</div>

<div class="grid-5">
    @foreach($issues as $issue)
        <a href="{{ route('issues.show', $issue->id) }}" class="cover-card">
            @if($issue->image)
                <img src="{{ $issue->image }}" alt="{{ $issue->name }}" class="cover-card-img">
            @else
                <div class="cover-card-placeholder"></div>
            @endif
            <div class="cover-card-body">
                <p class="cover-card-title">{{ $issue->name }}</p>
                <p class="cover-card-meta">{{ $issue->volume->name ?? '' }} #{{ $issue->issue_number }}</p>
            </div>
        </a>
    @endforeach
</div>
@endsection