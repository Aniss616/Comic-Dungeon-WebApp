<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Comic Dungeon – Register</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md">

        {{-- LOGO --}}
        <div class="text-center mb-8">
            <h1 class="text-yellow-400 font-black text-4xl tracking-widest uppercase">⚡ Comic Dungeon</h1>
            <p class="text-zinc-500 text-sm mt-2">Create your account</p>
        </div>

        {{-- CARD --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-8 shadow-2xl">

            {{-- ERRORS --}}
            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-lg px-4 py-3 mb-6">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-5">
                @csrf

                {{-- USERNAME --}}
                <div>
                    <label class="block text-zinc-400 text-sm mb-1.5">Username</label>
                    <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        autofocus
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-yellow-400 transition"
                        placeholder="coolreader42"
                    />
                </div>

                {{-- EMAIL --}}
                <div>
                    <label class="block text-zinc-400 text-sm mb-1.5">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-yellow-400 transition"
                        placeholder="you@example.com"
                    />
                </div>

                {{-- PASSWORD --}}
                <div>
                    <label class="block text-zinc-400 text-sm mb-1.5">Password</label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-yellow-400 transition"
                        placeholder="••••••••"
                    />
                </div>

                {{-- CONFIRM PASSWORD --}}
                <div>
                    <label class="block text-zinc-400 text-sm mb-1.5">Confirm Password</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        required
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-yellow-400 transition"
                        placeholder="••••••••"
                    />
                </div>

                {{-- SUBMIT --}}
                <button
                    type="submit"
                    class="w-full bg-yellow-400 hover:bg-yellow-300 text-zinc-950 font-bold py-2.5 rounded-lg transition tracking-wide uppercase text-sm">
                    Create Account
                </button>

            </form>
        </div>

        {{-- LOGIN LINK --}}
        <p class="text-center text-zinc-600 text-sm mt-6">
            Already have an account?
            <a href="{{ route('login') }}" class="text-yellow-400 hover:underline">Login</a>
        </p>

    </div>

</body>
</html>