<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.meta', ['pageTitle' => trim($__env->yieldContent('title')) ?: null])
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @stack('styles')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="min-h-screen lg:flex">
        <aside class="border-r border-slate-800/20 bg-slate-900 text-slate-200 lg:fixed lg:inset-y-0 lg:w-72">
            <div class="flex h-full flex-col">
                <div class="border-b border-white/10 px-6 py-8 text-center">
                    <img src="{{ asset('img/logo-ums-tif.webp') }}" alt="logo-tif"
                        class="w-auto h-24 justify-center mx-auto">
                    <p class="text-info text-xl font-bold">Sistem Deadstock and Slow Moving Stock</p>
                </div>

                <nav class="flex-1 px-3 py-5">
                    <a href="/"
                        class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->is('/') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <i class="fa-solid fa-gauge text-secondary"></i>
                        Dashboard
                    </a>

                    @if (auth()->user()->role === 'administrator')
                        <a href="/sales"
                            class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                            <i class="fa-solid fa-cart-plus text-secondary"></i>
                            Kasir Penjualan
                        </a>
                        <a href="/histori-sales"
                            class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                            <i class="bi bi-clock-history text-secondary"></i>
                            Riwayat Penjualan
                        </a>
                    @endif

                    @if (in_array(auth()->user()->role, ['admin', 'gudang']))
                        <div x-data="{ open: {{ request()->is('products*', 'categories*', 'suppliers*', 'purchases*', 'gudang*') ? 'true' : 'false' }} }" class="mb-2">
                            <button @click="open = !open"
                                class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-database text-secondary"></i>
                                    <span>Manajemen Data</span>
                                </div>
                                <i class="bi transition-transform duration-200"
                                    :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                            </button>

                            <div x-show="open" x-collapse x-cloak class="mt-1 space-y-1 px-4">
                                <a href="/products"
                                    class="flex items-center gap-3 rounded-lg py-2 pl-9 text-sm font-medium transition {{ request()->is('products*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white' }}">
                                    <i class="fa-solid fa-box text-secondary"></i>
                                    Kelola Barang
                                </a>
                                <a href="/categories"
                                    class="flex items-center gap-3 rounded-lg py-2 pl-9 text-sm font-medium transition {{ request()->is('categories*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white' }}">
                                    <i class="fa-solid fa-tags text-secondary"></i>
                                    Kategori
                                </a>
                                <a href="/suppliers"
                                    class="flex items-center gap-3 rounded-lg py-2 pl-9 text-sm font-medium transition {{ request()->is('suppliers*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white' }}">
                                    <i class="fa-solid fa-truck text-secondary"></i>
                                    Supplier
                                </a>
                                <a href="/purchases"
                                    class="flex items-center gap-3 rounded-lg py-2 pl-9 text-sm font-medium transition {{ request()->is('purchases*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white' }}">
                                    <i class="fa-solid fa-inbox text-secondary"></i>
                                    Barang Masuk
                                </a>
                                <a href="/gudang"
                                    class="flex items-center gap-3 rounded-lg py-2 pl-9 text-sm font-medium transition {{ request()->is('gudang*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white' }}">
                                    <i class="fa-solid fa-archive text-secondary"></i>
                                    Gudang
                                </a>
                            </div>
                        </div>
                    @endif

                    @if (auth()->user()->role === 'admin')
                        <a href="/analysis"
                            class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->is('analysis*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                            <i class="fa-solid fa-chart-line text-secondary"></i>
                            Analisis Restock
                        </a>

                        <a href="/riwayat"
                            class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->is('riwayat*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                            <i class="fa-solid fa-clock-rotate-left text-secondary"></i>
                            Riwayat Penjualan
                        </a>

                        <a href="/users"
                            class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->is('users*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                            <i class="fa-solid fa-users-gear text-secondary"></i>
                            Manajemen Akun
                        </a>
                    @endif
                </nav>

                <div class="border-t border-white/10 p-4">
                    <div class="mb-4 flex items-center gap-3 rounded-xl bg-white/5 px-3 py-3">
                        <i class="bi bi-person-circle text-2xl text-primary"></i>
                        <div>
                            <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400">Pengguna aktif</p>
                        </div>
                    </div>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-red-500/60 px-4 py-2.5 text-sm font-semibold text-red-200 transition hover:bg-red-500/10 hover:text-white">
                            <i class="bi bi-box-arrow-left"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex min-h-screen flex-1 flex-col lg:pl-72">
            <header class="border-b border-slate-200 bg-white/80 backdrop-blur lg:hidden">
                <div class="flex items-center justify-between px-4 py-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Deadstock Sys</p>
                        <h2 class="text-lg font-bold text-slate-900">Menu</h2>
                    </div>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center rounded-lg border border-red-500 px-3 py-2 text-sm font-semibold text-red-600">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div class="mx-auto w-full max-w-7xl">
                    @if (session('success'))
                        <div
                            class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')
</body>

</html>
