@extends('layouts.app')

@section('title', $volume->name)

@section('content')

<div class="space-y-8">

    {{-- BACK --}}
    <a href="{{ route('explore') }}?tab=volumes" class="text-zinc-500 hover:text-yellow-400 text-sm transition">
        ← Back to Volumes
    </a>

    {{-- HERO --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 flex flex-col md:flex-row gap-8">

        {{-- COVER --}}
        <div class="flex-shrink-0">
            @if ($volume->cover_image)
                <img
                    src="{{ $volume->cover_image }}"
                    alt="{{ $volume->name }}"
                    class="w-48 object-cover rounded-xl border border-zinc-700"
                />
            @else
                <div class="w-48 h-64 bg-zinc-800 rounded-xl flex items-center justify-center text-zinc-600 text-5xl">📚</div>
            @endif
        </div>

        {{-- INFO --}}
        <div class="flex-1 space-y-5">

            {{-- TITLE --}}
            <div>
                <p class="text-zinc-500 text-xs uppercase tracking-widest mb-1">Volume</p>
                <h1 class="text-4xl font-black text-yellow-400 uppercase tracking-widest leading-tight">
                    {{ $volume->name }}
                </h1>
                @if ($volume->publisher)
                    <p class="text-zinc-400 text-sm mt-1">
                        Published by
                        <span class="text-zinc-200 font-semibold">{{ $volume->publisher->name }}</span>
                        @if ($volume->publisher->location_city)
                            · {{ $volume->publisher->location_city }}
                            @if ($volume->publisher->location_country)
                                , {{ $volume->publisher->location_country }}
                            @endif
                        @endif
                    </p>
                @endif
            </div>

            {{-- STATS --}}
            <div class="flex flex-wrap gap-3">
                <div class="bg-zinc-800 border border-zinc-700 rounded-xl px-5 py-3 text-center">
                    <p class="text-yellow-400 font-black text-2xl">
                        {{ $volume->count_of_issues ?? $volume->issues->count() }}
                    </p>
                    <p class="text-zinc-500 text-xs uppercase tracking-wider mt-0.5">Total Issues</p>
                </div>
                <div class="bg-zinc-800 border border-zinc-700 rounded-xl px-5 py-3 text-center">
                    <p class="text-yellow-400 font-black text-2xl">
                        {{ $volume->issues->count() }}
                    </p>
                    <p class="text-zinc-500 text-xs uppercase tracking-wider mt-0.5">Imported</p>
                </div>
                @if ($volume->first_issue)
                    <div class="bg-zinc-800 border border-zinc-700 rounded-xl px-5 py-3 text-center">
                        <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">First Issue</p>
                        <p class="text-zinc-100 font-black text-lg">#{{ $volume->first_issue['issue_number'] ?? '?' }}</p>
                    </div>
                @endif
                @if ($volume->last_issue)
                    <div class="bg-zinc-800 border border-zinc-700 rounded-xl px-5 py-3 text-center">
                        <p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Last Issue</p>
                        <p class="text-zinc-100 font-black text-lg">#{{ $volume->last_issue['issue_number'] ?? '?' }}</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- DESCRIPTION SECTIONS --}}
    @if (count($descriptionSections) > 0)
        <div class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest">📋 About</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($descriptionSections as $index => $section)
                    @php
                        $accents = [
                            'border-yellow-400/40 bg-yellow-400/5',
                            'border-blue-400/40 bg-blue-400/5',
                            'border-purple-400/40 bg-purple-400/5',
                            'border-green-400/40 bg-green-400/5',
                            'border-red-400/40 bg-red-400/5',
                            'border-orange-400/40 bg-orange-400/5',
                            'border-pink-400/40 bg-pink-400/5',
                            'border-cyan-400/40 bg-cyan-400/5',
                        ];
                        $titleColors = [
                            'text-yellow-400',
                            'text-blue-400',
                            'text-purple-400',
                            'text-green-400',
                            'text-red-400',
                            'text-orange-400',
                            'text-pink-400',
                            'text-cyan-400',
                        ];
                        $accent     = $accents[$index % count($accents)];
                        $titleColor = $titleColors[$index % count($titleColors)];
                    @endphp
                    <div class="border {{ $accent }} rounded-2xl p-5 relative overflow-hidden hover:scale-[1.01] transition duration-200">
                        <div class="absolute top-0 right-0 w-16 h-16 opacity-10 rounded-bl-full {{ str_replace(['border-', '/40'], ['bg-', ''], explode(' ', $accent)[0]) }}"></div>
                        @if ($section['title'])
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-1 h-4 rounded-full {{ str_replace('text-', 'bg-', $titleColor) }}"></div>
                                <p class="{{ $titleColor }} text-xs font-black uppercase tracking-widest">
                                    {{ $section['title'] }}
                                </p>
                            </div>
                        @endif
                        <p class="text-zinc-300 text-sm leading-relaxed">
                            {{ Str::limit($section['content'], 300) }}
                        </p>
                        @if (strlen($section['content']) > 300)
                            <button
                                onclick="toggleSection(this)"
                                data-full="{{ e($section['content']) }}"
                                data-short="{{ e(Str::limit($section['content'], 300)) }}"
                                class="{{ $titleColor }} text-xs hover:underline mt-2 block font-semibold">
                                Read more
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ISSUES LIST --}}
    @if ($volume->issues->count() > 0)
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest mb-6">
                📄 Issues
                <span class="text-zinc-600 font-normal text-sm ml-2">({{ $volume->issues->count() }} imported)</span>
            </h3>
            <div class="space-y-2">
                @foreach ($volume->issues as $issue)
                    <a href="{{ route('issues.show', $issue->id) }}"
                       class="flex items-center gap-4 bg-zinc-800 hover:bg-zinc-700 rounded-xl px-4 py-3 transition group">
                        @if ($issue->image)
                            <img src="{{ $issue->image }}" alt="{{ $issue->name }}"
                                 class="w-10 h-14 object-cover object-top rounded flex-shrink-0"/>
                        @else
                            <div class="w-10 h-14 bg-zinc-700 rounded flex items-center justify-center text-zinc-500 text-xs flex-shrink-0">N/A</div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-zinc-100 font-semibold text-sm group-hover:text-yellow-400 transition truncate">
                                {{ $issue->name ?? 'Untitled' }}
                            </p>
                            <p class="text-zinc-500 text-xs mt-0.5">
                                #{{ $issue->issue_number ?? '?' }}
                                @if ($issue->cover_date) · {{ $issue->cover_date }} @endif
                                @if ($issue->store_date) · On Sale: {{ $issue->store_date }} @endif
                            </p>
                        </div>
                        @auth
                            @php $isRead = Auth::user()->reads()->where('issue_id', $issue->id)->exists(); @endphp
                            <span class="text-xs flex-shrink-0 {{ $isRead ? 'text-green-400' : 'text-zinc-600' }}">
                                {{ $isRead ? '✅ Read' : '○ Unread' }}
                            </span>
                        @endauth
                        <span class="text-zinc-700 group-hover:text-yellow-400 transition">→</span>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 text-center">
            <p class="text-zinc-600">No issues imported for this volume yet.</p>
            @auth
                @if(Auth::user()->is_admin)
                    <a href="{{ route('dashboard') }}" class="text-yellow-400 text-sm hover:underline mt-2 block">
                        Import issues from dashboard
                    </a>
                @endif
            @endauth
        </div>
    @endif

</div>

@endsection

@push('scripts')
<script>
    function toggleSection(btn) {
        const p      = btn.previousElementSibling;
        const isFull = btn.textContent.trim() === 'Read less';
        p.textContent = isFull ? btn.dataset.short : btn.dataset.full;
        btn.textContent = isFull ? 'Read more' : 'Read less';
    }
</script>
@endpush