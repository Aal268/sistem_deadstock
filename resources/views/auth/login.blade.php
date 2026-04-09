<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Deadstock</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-white/100 text-slate-900">
    <main class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-0 bg-info/40"></div>
        <div class="relative mx-auto flex min-h-screen w-full max-w-5xl items-center px-4 py-6 sm:px-6 lg:px-8">
            <section
                class="grid w-full overflow-hidden rounded-[2rem] bg-white shadow-2xl ring-1 ring-info/80 xl:grid-cols-[1.02fr_0.98fr]">
                <div class="relative overflow-hidden bg-primary px-6 py-10 text-white sm:px-10 lg:px-12 lg:py-12">
                    <div class="relative flex h-full flex-col justify-between gap-10">
                        <div class="space-y-5">
                            <div class="max-w-2xl space-y-4">
                                <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                                    Selamat Datang di <br>Sistem Deadstock
                                </h1>

                                <p class="text-base text-info sm:text-lg">
                                    Pantau persediaan, transaksi, dan pergerakan stok secara terpusat dengan cepat dan
                                    lebih rapi.
                                </p>
                            </div>
                        </div>

                        <div class="relative flex items-center justify-between gap-4 pt-6 text-sm text-info">
                            <div>
                                <p class="font-medium text-white">Project Capstone - Kelas D</p>
                                <p>©2026 Sistem Deadstock</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white px-6 py-10 sm:px-10 lg:px-12 lg:py-12">
                    <div class="mx-auto flex h-full max-w-xl flex-col justify-center">
                        <div class="mb-10 space-y-3">
                            <p class="text-2xl font-semibold text-primary">Login</p>
                            <h2 class="text-lg font-medium text-slate-900 sm:text-xl">Masukkan kredensial Anda</h2>
                        </div>

                        @if ($errors->any())
                            <div
                                class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                <ul class="list-disc space-y-1 pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="/login" class="space-y-5">
                            @csrf

                            <div>
                                <label for="email"
                                    class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                                <div
                                    class="group flex items-center gap-3 rounded-2xl border border-info bg-white px-4 py-4 shadow-sm transition focus-within:border-secondary focus-within:ring-4 focus-within:ring-secondary/20">
                                    <i class="fa-solid fa-envelope"></i>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                                        placeholder="nama@gmail.com" required autofocus
                                        class="w-full border-0 bg-transparent p-0 text-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0">
                                </div>
                            </div>

                            <div>
                                <label for="password"
                                    class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                                <div
                                    class="group flex items-center gap-3 rounded-2xl border border-info bg-white px-4 py-4 shadow-sm transition focus-within:border-secondary focus-within:ring-4 focus-within:ring-secondary/20">
                                    <i class="fa-solid fa-lock"></i>
                                    <input id="password" type="password" name="password" placeholder="••••••••"
                                        required
                                        class="w-full border-0 bg-transparent p-0 text-lg text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0">
                                </div>
                            </div>

                            <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-3 rounded-2xl bg-secondary px-6 py-4 text-lg font-bold text-white shadow-lg transition duration-200 hover:-translate-y-0.5 hover:bg-tertiary focus:outline-none focus:ring-4 focus:ring-secondary/30">
                                Masuk
                                {{-- class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true --}}
                                <i class="fa-solid fa-person-walking-arrow-right"></i>
                            </button>
                        </form>

                        {{-- <div class="mt-8 rounded-2xl border border-dashed border-slate-200 bg-white px-4 py-4 text-sm text-slate-500">
                            <p class="font-semibold text-slate-700">Akses demo</p>
                            <div class="mt-2 space-y-1">
                                <p>Admin: admin@test.com / password</p>
                                <p>Kasir: kasir@test.com / password</p>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>

</html>
