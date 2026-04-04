<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Deadstock</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; }
        .login-card { border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="d-flex align-items-center py-4 bg-light" style="height: 100vh;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5 col-lg-4">
            <div class="text-center mb-4">
                <h2><i class="bi bi-box-seam text-primary"></i> Sistem Deadstock</h2>
            </div>
            
            <div class="card login-card p-4">
                <div class="card-body">
                    <h5 class="card-title text-center mb-4">Login</h5>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="/login">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-3">Masuk Sistem</button>
                    </form>
                    
                    <div class="mt-4 text-center text-muted small">
                        <p class="mb-0">Akses Demo:</p>
                        <ul class="list-unstyled">
                            <li>Admin: admin@test.com / password</li>
                            <li>Kasir: kasir@test.com / password</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
