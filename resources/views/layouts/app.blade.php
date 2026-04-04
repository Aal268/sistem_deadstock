<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Deadstock & Kasir</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #343a40; color: white; }
        .sidebar .nav-link { color: #c2c7d0; padding: 12px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background-color: rgba(255,255,255,0.1); }
        .main-content { min-height: 100vh; padding-top: 20px; }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar d-flex flex-column">
            <div class="p-3 text-center border-bottom border-secondary mb-3">
                <i class="bi bi-box-seam display-4"></i>
                <h5 class="mt-2 text-white">Deadstock Sys</h5>
                <span class="badge bg-primary">{{ strtoupper(auth()->user()->role) }}</span>
            </div>
            
            <ul class="nav flex-column mb-auto">
                <li class="nav-item">
                    <a href="/" class="nav-link">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                
                @if(auth()->user()->role === 'administrator')
                <li class="nav-item">
                    <a href="/sales" class="nav-link">
                        <i class="bi bi-cart-check me-2"></i> Kasir Penjualan
                    </a>
                </li>
                @endif
                
                @if(auth()->user()->role === 'admin')
                <li class="nav-item">
                    <a href="/analysis" class="nav-link">
                        <i class="bi bi-graph-up-arrow me-2"></i> Analisis Restock
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/purchases" class="nav-link">
                        <i class="bi bi-box-arrow-in-down me-2"></i> Barang Masuk
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/users" class="nav-link">
                        <i class="bi bi-people me-2"></i> Kelola Admin
                    </a>
                </li>
                @endif
            </ul>
            
            <div class="p-3 border-top border-secondary">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <div>
                        <strong>{{ auth()->user()->name }}</strong>
                    </div>
                </div>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-box-arrow-left"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 main-content">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm mb-4 px-4 d-md-none">
                <span class="navbar-brand mb-0 h1">Deadstock Sys</span>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mobileMenu">
                    <!-- Mobile Menu (simplified for brevity) -->
                    <form action="/logout" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">Logout</button>
                    </form>
                </div>
            </nav>

            <div class="container px-4 pb-5">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
