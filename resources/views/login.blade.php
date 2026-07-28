<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk | Manajemen Surat & Pengepul</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/surat-app.css') }}">
</head>
<body class="auth-wrapper">
    <main class="w-100 d-flex justify-content-center">
        <div class="auth-card">
            <div class="text-center mb-4">
                <div class="brand-mark mx-auto mb-3" style="width: 54px; height: 54px; background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
                    <i class="fa-solid fa-recycle text-white fs-4"></i>
                </div>
                <h1 class="h4 fw-extrabold text-dark mb-1">Masuk ke Akun Anda</h1>
                <p class="text-muted small mb-0">Manajemen Surat & Pengepul Digital</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 px-3 mb-3 d-flex align-items-center gap-2 small">
                    <i class="fa-solid fa-circle-exclamation text-danger"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small" for="email">Alamat Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: var(--radius-md) 0 0 var(--radius-md);">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control border-start-0" placeholder="nama@email.com" required autofocus style="border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary small" for="password">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: var(--radius-md) 0 0 var(--radius-md);">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input id="password" type="password" name="password" class="form-control border-start-0 border-end-0" placeholder="••••••••" required>
                        <button class="btn btn-outline-secondary border-start-0 bg-light text-muted px-3" type="button" onclick="togglePasswordVisibility('password', 'toggleEyeIcon')" style="border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                            <i class="fa-solid fa-eye" id="toggleEyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button class="btn btn-primary w-100 btn-lg shadow fs-6 py-2.5 mb-3">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk Sekarang
                </button>
            </form>

            <div class="text-center pt-3 border-top mt-3">
                <p class="small text-muted mb-2">Belum memiliki akun? <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-bold">Daftar di sini</a></p>
                
                <div class="p-3 bg-light rounded-3 border text-start mt-3">
                    <div class="fw-bold text-dark mb-1 small d-flex align-items-center gap-1">
                        <i class="fa-solid fa-key text-warning"></i> Akun Demo & Password:
                    </div>
                    <div class="row g-1 small" style="font-size: 0.76rem;">
                        <div class="col-6"><strong>Admin:</strong> admin@gmail.com (pass: <code>admin</code>)</div>
                        <div class="col-6"><strong>Staff:</strong> staff@gmail.com (pass: <code>staff</code>)</div>
                        <div class="col-6"><strong>Warga:</strong> customer@gmail.com (pass: <code>customer</code>)</div>
                        <div class="col-6"><strong>Driver:</strong> driver1@gmail.com (pass: <code>driver</code>)</div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function togglePasswordVisibility(inputId, eyeIconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(eyeIconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
