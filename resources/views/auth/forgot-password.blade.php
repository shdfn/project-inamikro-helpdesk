<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Kata Sandi - INAmikro</title>
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
            <img src="{{ asset('images/logo-inamikro.png') }}" alt="INAmikro" style="height: 50px;" class="mb-2">
            <p class="text-muted small mb-0">Reset Kata Sandi Akun Anda</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger small py-2">
                <i class="bi bi-exclamation-circle"></i> {{ $errors->first() }}
            </div>
        @endif

        <p class="text-muted small">Masukkan email terdaftar Anda. Sistem akan membuat tautan reset kata sandi.</p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold">Email Terdaftar</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control" placeholder="Masukkan email" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn btn-inamikro w-100 py-2 fw-bold">Kirim Link Reset</button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left"></i> Kembali ke Login
            </a>
        </div>
    </div>
</body>
</html>