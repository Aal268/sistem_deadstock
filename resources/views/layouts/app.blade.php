<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Deadstock & Kasir</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
<div class="min-h-screen lg:flex">
    <aside class="border-r border-slate-800/20 bg-slate-900 text-slate-200 lg:fixed lg:inset-y-0 lg:w-72">
        <div class="flex h-full flex-col">
            <div class="border-b border-white/10 px-6 py-8 text-center">
                <div class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-white">
                    <i class="bi bi-box-seam text-3xl"></i>
                </div>
                <h1 class="mt-4 text-xl font-bold text-white">Deadstock Sys</h1>
                <span class="mt-2 inline-flex rounded-full bg-primary/15 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-primary">
                    {{ strtoupper(auth()->user()->role) }}
                </span>
            </div>

            <nav class="flex-1 px-3 py-5">
                <a href="/" class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                    <i class="bi bi-speedometer2 text-base"></i>
                    Dashboard
                </a>

                @if(auth()->user()->role === 'administrator')
                <a href="/sales" class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                    <i class="bi bi-cart-check text-base"></i>
                    Kasir Penjualan
                </a>
                @endif

                @if(auth()->user()->role === 'admin')
                <a href="/analysis" class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                    <i class="bi bi-graph-up-arrow text-base"></i>
                    Analisis Restock
                </a>
                <a href="/products" class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                    <i class="bi bi-boxes text-base"></i>
                    Kelola Master Barang
                </a>
                <a href="/categories" class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                    <i class="bi bi-tags text-base"></i>
                    Kelola Kategori
                </a>
                <a href="/suppliers" class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                    <i class="bi bi-truck text-base"></i>
                    Kelola Supplier
                </a>
                <a href="/purchases" class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                    <i class="bi bi-box-arrow-in-down text-base"></i>
                    Trx Barang Masuk
                </a>
                <a href="/users" class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                    <i class="bi bi-people text-base"></i>
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
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-red-500/60 px-4 py-2.5 text-sm font-semibold text-red-200 transition hover:bg-red-500/10 hover:text-white">
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
                    <button type="submit" class="inline-flex items-center rounded-lg border border-red-500 px-3 py-2 text-sm font-semibold text-red-600">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <div class="mx-auto w-full max-w-7xl">
                @if(session('success'))
                    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
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
