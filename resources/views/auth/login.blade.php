<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Koriro POS</title>

    @include('assets.style')

    <style>
        :root {
            --koriro-espresso: #1c140f;
            --koriro-cream: #f4efe7;
            --koriro-rust: #b5502e;
            --koriro-rust-dark: #98411f;
            --koriro-ink: #201812;
            --koriro-muted: #8a7f74;
        }

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--koriro-ink);
            background-color: var(--koriro-cream);
        }

        h1, h2, h3, h4, h5, .display-serif {
            font-family: 'Fraunces', serif;
        }

        .auth-shell {
            min-height: 100vh;
            display: flex;
        }

        /* Left panel — photo */
        .auth-visual {
            position: relative;
            flex: 1 1 52%;
            min-height: 320px;
            background: linear-gradient(180deg, rgba(15, 10, 7, 0.15) 0%, rgba(15, 10, 7, 0.65) 78%, rgba(15, 10, 7, 0.9) 100%),
                        url('https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=1400&q=80') center/cover no-repeat;
            padding: 2.25rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #f4efe7;
        }

        .auth-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
        }

        .auth-brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--koriro-cream);
            color: var(--koriro-espresso);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        .auth-brand-text {
            line-height: 1.15;
        }

        .auth-brand-eyebrow {
            font-size: 0.68rem;
            letter-spacing: 0.14em;
            font-weight: 600;
            opacity: 0.75;
        }

        .auth-brand-name {
            font-family: 'Fraunces', serif;
            font-weight: 700;
            font-size: 1.05rem;
        }

        .auth-visual-copy .staff-tag {
            font-size: 0.7rem;
            letter-spacing: 0.14em;
            font-weight: 600;
            color: #e8b190;
        }

        .auth-visual-copy h2 {
            font-weight: 600;
            font-size: clamp(1.6rem, 3.2vw, 2.35rem);
            line-height: 1.18;
            max-width: 22ch;
            margin: 0.5rem 0 0;
        }

        /* Right panel — form */
        .auth-form-panel {
            flex: 1 1 48%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            background-color: var(--koriro-cream);
        }

        .auth-form-inner {
            width: 100%;
            max-width: 380px;
        }

        .auth-eyebrow {
            font-size: 0.7rem;
            letter-spacing: 0.14em;
            font-weight: 600;
            color: var(--koriro-rust);
        }

        .auth-heading {
            font-weight: 600;
            font-size: 2rem;
            margin: 0.35rem 0 0.4rem;
            color: var(--koriro-ink);
        }

        .auth-subtext {
            color: var(--koriro-muted);
            font-size: 0.92rem;
            margin-bottom: 1.9rem;
        }

        .auth-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--koriro-ink);
            margin-bottom: 0.4rem;
        }

        .auth-input {
            background-color: #fff;
            border: 1px solid rgba(28, 20, 15, 0.12);
            color: var(--koriro-ink);
            padding: 0.75rem 1rem;
            border-radius: 0.7rem;
        }

        .auth-input:focus {
            border-color: var(--koriro-rust);
            box-shadow: 0 0 0 0.2rem rgba(181, 80, 46, 0.15);
            background-color: #fff;
        }

        .auth-input::placeholder {
            color: rgba(28, 20, 15, 0.35);
        }

        .btn-koriro {
            background-color: var(--koriro-rust);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.85rem 1rem;
            border-radius: 0.7rem;
            transition: background-color 0.15s ease;
        }

        .btn-koriro:hover {
            background-color: var(--koriro-rust-dark);
            color: #fff;
        }

        .auth-back-link {
            display: block;
            text-align: center;
            margin-top: 1.4rem;
            font-size: 0.88rem;
            color: var(--koriro-muted);
            text-decoration: none;
        }

        .auth-back-link:hover {
            color: var(--koriro-ink);
        }

        .form-check-input:checked {
            background-color: var(--koriro-rust);
            border-color: var(--koriro-rust);
        }

        @media (max-width: 991.98px) {
            .auth-shell { flex-direction: column; }
            .auth-visual { flex: 0 0 260px; }
            .auth-form-panel { padding: 2.5rem 1.5rem 3rem; }
        }
    </style>
</head>
<body>

<div class="auth-shell">

    <!-- Panel kiri — visual -->
    <div class="auth-visual">
        <div class="auth-brand">
            <span class="auth-brand-mark"><i class="bi bi-cup-hot-fill"></i></span>
            <div class="auth-brand-text">
                <div class="auth-brand-eyebrow">KORIRO COFFEE</div>
                <div class="auth-brand-name">Tondo POS</div>
            </div>
        </div>

        <div class="auth-visual-copy">
            <div class="staff-tag">STAFF ONLY</div>
            <h2>Kelola dapur, kasir, dan peramalan bahan baku dalam satu papan.</h2>
        </div>
    </div>

    <!-- Panel kanan — form -->
    <div class="auth-form-panel">
        <div class="auth-form-inner">
            <div class="auth-eyebrow">MASUK</div>
            <h1 class="auth-heading">Admin / Kasir</h1>
            <p class="auth-subtext">Gunakan akun yang telah dibuat admin.</p>

            @if ($errors->any())
                <div class="alert alert-danger border-0 rounded-3 small py-2">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="auth-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control auth-input"
                           value="{{ old('email') }}" required autofocus placeholder="admin@koriro.coffee">
                </div>

                <div class="mb-4">
                    <label class="auth-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control auth-input"
                           required placeholder="••••••••">
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label text-muted small" for="remember">Ingat saya</label>
                </div>

                <button type="submit" class="btn btn-koriro w-100">
                    Masuk
                </button>
            </form>

            <a href="/" class="auth-back-link">&larr; Kembali ke menu pelanggan</a>
        </div>
    </div>

</div>

</body>
</html>