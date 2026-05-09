{{--
    Recommended Issues partial
    Include in home/index.blade.php with:
        @include('home._recommendations', ['recommendedIssues' => $recommendedIssues])
    Or paste the block directly inside home/index.blade.php.
--}}

@auth
    @if ($recommendedIssues->isNotEmpty())
        <section class="py-12 px-4 max-w-7xl mx-auto">

            <h2 class="text-2xl font-bold mb-1">Recommended for You</h2>
            <p class="text-sm text-gray-400 mb-6">
                Based on your favourited issues and characters
            </p>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach ($recommendedIssues as $issue)
                    <a href="{{ route('issues.show', $issue->id) }}"
                       class="group relative flex flex-col rounded-lg overflow-hidden
                              bg-gray-800 hover:bg-gray-700 transition-colors shadow-md">

                        {{-- Cover image --}}
                        <div class="aspect-[2/3] overflow-hidden bg-gray-900">
                            @if ($issue->image)
                                <img src="{{ $issue->image }}"
                                     alt="{{ $issue->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105
                                            transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.966 8.966 0 00-6 2.292m0-14.25v14.25"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="p-2 flex flex-col gap-0.5">
                            {{-- Volume name --}}
                            @if ($issue->volume)
                                <span class="text-xs text-indigo-400 font-medium truncate">
                                    {{ $issue->volume->name }}
                                </span>
                            @endif

                            {{-- Issue name / number --}}
                            <span class="text-xs text-white font-semibold truncate leading-tight">
                                @if ($issue->name && $issue->name !== 'TBD')
                                    {{ $issue->name }}
                                @else
                                    Issue #{{ $issue->issue_number }}
                                @endif
                            </span>

                            @if ($issue->issue_number)
                                <span class="text-xs text-gray-400">#{{ $issue->issue_number }}</span>
                            @endif
                        </div>

                        {{--
                            Score badge (optional debug aid — remove for production).
                            Shows how many favourited signals matched this issue.
                        --}}
                        {{-- <span class="absolute top-1 right-1 bg-indigo-600 text-white
                                         text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                            {{ $issue->recommendation_score }}
                        </span> --}}

                    </a>
                @endforeach
            </div>

        </section>
    @else
        {{--
            Placeholder shown to logged-in users who haven't favourited anything yet.
            Remove this block if you'd rather show nothing.
        --}}
        <section class="py-12 px-4 max-w-7xl mx-auto">
            <h2 class="text-2xl font-bold mb-1">Recommended for You</h2>
            <p class="text-sm text-gray-400">
                Favourite some issues or characters and we'll surface more of what you love.
            </p>
        </section>
    @endif
@endauth