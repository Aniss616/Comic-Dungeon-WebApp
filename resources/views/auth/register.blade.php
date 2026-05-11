{{-- REGISTER BLADE --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Comic Dungeon – Register</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
</head>

<body
    class="min-h-screen flex items-center justify-center px-6 py-10"
    style="
        background:#0D0D0D;
        color:#E8E4DC;
        font-family:'DM Sans',sans-serif;
    "
>

    <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
         style="background-image:linear-gradient(rgba(255,255,255,0.08) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.08) 1px, transparent 1px); background-size:40px 40px;">
    </div>

    <div class="w-full max-w-md relative z-10">

        {{-- LOGO --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-3 mb-4">
                <span class="w-2 h-2 rounded-full bg-[#C0392B]"></span>

                <h1
                    class="uppercase tracking-[0.18em] text-5xl"
                    style="
                        font-family:'Barlow Condensed',sans-serif;
                        font-weight:800;
                    "
                >
                    Comic Dungeon
                </h1>
            </div>

            <p class="text-sm text-[rgba(232,228,220,0.45)] tracking-wide">
                Create your reader account
            </p>
        </div>

        {{-- CARD --}}
        <div
            class="rounded-2xl p-8 border backdrop-blur-sm"
            style="
                background:#141418;
                border-color:rgba(255,255,255,0.06);
            "
        >

            {{-- ERRORS --}}
            @if ($errors->any())
                <div
                    class="text-sm rounded-lg px-4 py-3 mb-6 border"
                    style="
                        background:rgba(192,57,43,0.12);
                        border-color:rgba(192,57,43,0.25);
                        color:#f87171;
                    "
                >
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-5">
                @csrf

                {{-- USERNAME --}}
                <div>
                    <label
                        class="block text-xs uppercase tracking-[0.12em] mb-2"
                        style="
                            font-family:'Barlow Condensed',sans-serif;
                            color:rgba(232,228,220,0.45);
                        "
                    >
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        autofocus
                        placeholder="comicreader"
                        class="w-full rounded-lg px-4 py-3 outline-none transition"
                        style="
                            background:#1C1C22;
                            border:1px solid rgba(255,255,255,0.06);
                            color:#E8E4DC;
                        "
                        onfocus="this.style.borderColor='rgba(192,57,43,0.4)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.06)'"
                    />
                </div>

                {{-- EMAIL --}}
                <div>
                    <label
                        class="block text-xs uppercase tracking-[0.12em] mb-2"
                        style="
                            font-family:'Barlow Condensed',sans-serif;
                            color:rgba(232,228,220,0.45);
                        "
                    >
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="you@example.com"
                        class="w-full rounded-lg px-4 py-3 outline-none transition"
                        style="
                            background:#1C1C22;
                            border:1px solid rgba(255,255,255,0.06);
                            color:#E8E4DC;
                        "
                        onfocus="this.style.borderColor='rgba(192,57,43,0.4)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.06)'"
                    />
                </div>

                {{-- PASSWORD --}}
                <div>
                    <label
                        class="block text-xs uppercase tracking-[0.12em] mb-2"
                        style="
                            font-family:'Barlow Condensed',sans-serif;
                            color:rgba(232,228,220,0.45);
                        "
                    >
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        class="w-full rounded-lg px-4 py-3 outline-none transition"
                        style="
                            background:#1C1C22;
                            border:1px solid rgba(255,255,255,0.06);
                            color:#E8E4DC;
                        "
                        onfocus="this.style.borderColor='rgba(192,57,43,0.4)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.06)'"
                    />
                </div>

                {{-- CONFIRM PASSWORD --}}
                <div>
                    <label
                        class="block text-xs uppercase tracking-[0.12em] mb-2"
                        style="
                            font-family:'Barlow Condensed',sans-serif;
                            color:rgba(232,228,220,0.45);
                        "
                    >
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        required
                        placeholder="••••••••"
                        class="w-full rounded-lg px-4 py-3 outline-none transition"
                        style="
                            background:#1C1C22;
                            border:1px solid rgba(255,255,255,0.06);
                            color:#E8E4DC;
                        "
                        onfocus="this.style.borderColor='rgba(192,57,43,0.4)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.06)'"
                    />
                </div>

                {{-- SUBMIT --}}
                <button
                    type="submit"
                    class="w-full py-3 rounded-lg uppercase tracking-[0.14em] text-sm transition"
                    style="
                        background:#C0392B;
                        color:white;
                        font-family:'Barlow Condensed',sans-serif;
                        font-weight:700;
                    "
                    onmouseover="this.style.background='#d44030'"
                    onmouseout="this.style.background='#C0392B'"
                >
                    Create Account
                </button>

            </form>
        </div>

        {{-- LOGIN LINK --}}
        <p
            class="text-center text-sm mt-6"
            style="color:rgba(232,228,220,0.45);"
        >
            Already have an account?

            <a
                href="{{ route('login') }}"
                class="transition"
                style="
                    color:#C0392B;
                    font-weight:600;
                "
            >
                Sign In
            </a>
        </p>

    </div>

</body>
</html>