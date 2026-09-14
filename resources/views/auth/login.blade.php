<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Koriro POS</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #0f0b09; /* Warna gelap khas tema Koriro */
            min-height: 100vh;
            display: flex;
            align-items: center;
            box-sizing: border-box;
        }
        .login-card {
            background: rgba(35, 28, 23, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f5ede6;
        }
        .brand-color {
            color: #c97d20;
        }
        .btn-brand {
            background-color: #c97d20;
            color: #fff;
            border: none;
        }
        .btn-brand:hover {
            background-color: #a6620f;
            color: #fff;
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: #c97d20;
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(201, 125, 32, 0.25);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-brand text-white mb-3" style="width: 60px; height: 60px; background-color: #c97d20;">
                    <i class="bi bi-cup-hot-fill fs-2"></i>
                </div>
                <h3 class="fw-bold mb-0 text-white">Koriro POS</h3>
                <p class="text-muted small">Sistem Manajemen & Peramalan WMA</p>
            </div>

            <div class="card login-card shadow-lg rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h5 class="fw-bold mb-4 text-center">Login Admin</h5>

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 small py-2">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-medium">Alamat Email</label>
                            <input type="email" name="email" class="form-control form-control-lg rounded-3" value="{{ old('email') }}" required autofocus placeholder="admin@koriro.com">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-medium">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg rounded-3" required placeholder="••••••••">
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" style="border-color: rgba(255,255,255,0.2);">
                            <label class="form-check-label text-muted small" for="remember">Ingat saya</label>
                        </div>

                        <button type="submit" class="btn btn-brand btn-lg w-100 rounded-3 fw-bold shadow-sm">
                            Masuk ke Dashboard <i class="bi bi-arrow-right-short ms-1"></i>
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="/" class="text-muted small text-decoration-none hover-white">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke PWA Pelanggan
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5 text-muted small">
                &copy; {{ date('Y') }} Koriro Coffee Tondo. All rights reserved.
            </div>
        </div>
    </div>
</div>

</body>
</html>
