@extends('layouts.app')

@section('title', $volume->name)

@section('content')

    <div class="space-y-8">

        {{-- BACK --}}
        <a href="{{ route('volumes.index') }}" class="text-zinc-500 hover:text-yellow-400 text-sm transition">
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
            <div class="flex-1 space-y-4">
                <div>
                    <h1 class="text-3xl font-black text-yellow-400 uppercase tracking-widest">{{ $volume->name }}</h1>
                    @if ($volume->publisher)
                        <a href="#" class="text-zinc-500 text-sm hover:text-yellow-400 transition mt-1 block">
                            {{ $volume->publisher->name }}
                        </a>
                    @endif
                </div>

                @if ($volume->description)
                    <p class="text-zinc-300 text-sm leading-relaxed">
                        {!! strip_tags($volume->description) !!}
                    </p>
                @endif

                <div class="flex items-center gap-6 pt-2">
                    <div class="bg-zinc-800 rounded-xl px-4 py-3 text-center">
                        <p class="text-yellow-400 font-black text-2xl">{{ $volume->issues->count() }}</p>
                        <p class="text-zinc-500 text-xs uppercase tracking-wider mt-0.5">Issues</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- ISSUES LIST --}}
        @if ($volume->issues->count() > 0)
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-zinc-100 uppercase tracking-widest mb-6">
                    📄 Issues <span class="text-zinc-600 font-normal text-sm">({{ $volume->issues->count() }})</span>
                </h3>

                <div class="space-y-2">
                    @foreach ($volume->issues as $issue)
                        <a href="{{ route('issues.show', $issue->id) }}"
                           class="flex items-center gap-4 bg-zinc-800 hover:bg-zinc-700 rounded-xl px-4 py-3 transition group">
                            @if ($issue->image)
                                <img
                                    src="{{ $issue->image }}"
                                    alt="{{ $issue->name }}"
                                    class="w-10 h-14 object-cover object-top rounded flex-shrink-0"
                                />
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
                                </p>
                            </div>
                            @auth
                                <span class="text-zinc-600 text-xs flex-shrink-0 hover:text-yellow-400 transition">
                                    Mark as read
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
                    <a href="{{ route('dashboard') }}" class="text-yellow-400 text-sm hover:underline mt-2 block">
                        Import issues from dashboard
                    </a>
                @endauth
            </div>
        @endif

    </div>

@endsection