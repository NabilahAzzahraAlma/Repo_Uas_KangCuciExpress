<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Kang Cuci Express</title>

    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/logo-laundry.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo-laundry.png') }}">
    <link rel="stylesheet" href="{{ asset('css/argon-dashboard-tailwind.css?v=1.0.1') }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="leading-default bg-white text-slate-500 antialiased">
    <main class="mt-0 transition-all duration-200 ease-in-out">
        <section>
            <div class="relative flex min-h-screen items-center overflow-hidden bg-cover bg-center">
                <div class="z-1 container">
                    <div class="-mx-3 flex flex-wrap">
                        <div class="mx-auto w-full max-w-md px-3">
                            <div class="relative flex flex-col rounded-2xl bg-white shadow-lg">
                                <div class="p-6 pb-0">
                                    <h4 class="font-bold text-lg">Login</h4>
                                    <p class="mb-0 text-sm">Masukkan email dan password untuk login</p>
                                </div>
                                <div class="flex-auto p-6">
                                    @if ($errors->any())
                                        <div class="mb-3 text-red-600 text-sm">
                                            {{ $errors->first() }}
                                        </div>
                                    @endif

                                    <form action="{{ route('pelanggan.login') }}" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label for="email" class="block text-sm font-semibold">Email</label>
                                            <input type="email" name="email" id="email"
                                                class="mt-1 w-full rounded-lg border p-2 focus:outline-none focus:ring focus:ring-blue-300"
                                                required>
                                        </div>

                                        <div class="mb-4">
                                            <label for="password" class="block text-sm font-semibold">Password</label>
                                            <input type="password" name="password" id="password"
                                                class="mt-1 w-full rounded-lg border p-2 focus:outline-none focus:ring focus:ring-blue-300"
                                                required>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit"
                                                class="rounded-lg bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">
                                                Login
                                            </button>
                                        </div>
                                    </form>

                                    <p class="mt-4 text-center text-sm">
                                        Belum punya akun?
                                        <a href="{{ route('pelanggan.register') }}"
                                            class="text-blue-600 font-semibold hover:underline">
                                            Daftar di sini
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>

</html>
