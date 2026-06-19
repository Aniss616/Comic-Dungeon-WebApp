<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Comic Dungeon – Login</title>
    <link rel="icon" type="image/png" href="{{ asset('images/CD-Logo.ico') }}">
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
            <div class="flex items-center justify-center gap-3 mb-4">
                <img
                    src="{{ asset('images/CD-SL-Logo.png') }}"
                    alt="Comic Dungeon Logo"
                    class="w-28 h-28 object-contain"
                />
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

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

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
                        autofocus
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

                {{-- REMEMBER --}}
                <div class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        name="remember"
                        id="remember"
                        class="accent-[#C0392B]"
                    />

                    <label
                        for="remember"
                        class="text-sm"
                        style="color:rgba(232,228,220,0.65);"
                    >
                        Remember me
                    </label>
                </div>

                {{-- SUBMIT --}}
                <button
                    type="submit"
                    class="w-full py-3 rounded-lg uppercase tracking-[0.14em] text-sm transition"
                    style="
                        background:#c9011d;
                        color:white;
                        font-family:'Barlow Condensed',sans-serif;
                        font-weight:700;
                        cursor:pointer;
                    "
                    onmouseover="this.style.background='#d44030'"
                    onmouseout="this.style.background='#c9011d'"
                >
                    Sign In
                </button>

            </form>
        </div>

        {{-- REGISTER LINK --}}
        <p
            class="text-center text-sm mt-6"
            style="color:rgba(232,228,220,0.45);"
        >
            Don’t have an account?

            <a
                href="{{ route('register') }}"
                class="transition"
                style="
                    color:#C0392B;
                    font-weight:600;
                "
            >
                Register
            </a>
        </p>

    </div>

</body>
</html>