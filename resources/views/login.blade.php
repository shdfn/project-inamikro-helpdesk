<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - INAmikro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #eef1f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
        .login-card { width: 100%; max-width: 420px; padding: 40px; border-radius: 15px; background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .btn-inamikro { background-color: #d81b21; color: white; border: none; }
        .btn-inamikro:hover { background-color: #b0151a; color: white; }
        .form-control { background-color: #f5f5f5; border: none; padding: 12px 15px; }
        .form-control:focus { background-color: #f5f5f5; box-shadow: 0 0 0 2px rgba(216,27,33,0.2); }
        .input-group-text { background-color: #f5f5f5; border: none; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-1" style="color: #d81b21;">INAmikro</h2>
            <p class="text-muted small">Portal Layanan Bantuan & Manajemen Tiket Terpadu</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger small">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ url('login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label small fw-bold">Email atau Nama Pengguna</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="abc@gmail.com" required>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <label class="form-label small fw-bold">Kata Sandi</label>
                    <a href="#" class="text-decoration-none small" style="color: #d81b21;">Lupa Kata Sandi?</a>
                </div>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder=".........." required>
                </div>
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label small" for="remember">Ingat sesi saya di perangkat ini</label>
            </div>

            <button type="submit" class="btn btn-inamikro w-100 py-2 fw-bold">Masuk ke Portal</button>
        </form>

        <div class="text-center mt-3 small text-muted">
            <div>Demo: mentor@inamikro.com / password</div>
        </div>
    </div>
</body>
</html>