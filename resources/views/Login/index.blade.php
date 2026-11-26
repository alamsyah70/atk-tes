
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ATK MDI</title>

    <style>
        /* ===== LOGIN LOADING OVERLAY ===== */
        .login-loading {
            position: fixed;
            inset: 0;
            z-index: 999999;
            display: none;
            align-items: center;
            justify-content: center;

            /* Background transparan */
            background: transparent;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
        }

        .login-loading.active {
            display: flex;
        }

        .login-loading-card {
            width: 230px;
            padding: 28px 22px;
            background: #fff;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 15px 45px rgba(0, 0, 0, .15);
            animation: loadingAppear .25s ease;
        }

        .login-spinner {
            width: 45px;
            height: 45px;
            margin: 0 auto 17px;
            border: 4px solid #e5edf7;
            border-top-color: #4778b9;
            border-radius: 50%;
            animation: loginSpin .8s linear infinite;
        }

        .login-loading-card h6 {
            margin: 0 0 6px;
            color: #294568;
            font-size: 14px;
            font-weight: 700;
        }

        .login-loading-card p {
            margin: 0;
            color: #8997a9;
            font-size: 11px;
        }

        @keyframes loginSpin {
            to { transform: rotate(360deg); }
        }

        @keyframes loadingAppear {
            from {
                opacity: 0;
                transform: translateY(8px) scale(.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #071B2B;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 350px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 45px rgba(0,0,0,0.1);
        }

        .login-right {
            width: 100%;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-right h2 {
        color: #1e293b;
        font-size: 28px;
        margin-bottom: 10px;
        text-align: center;
        }

        .subtitle {
        color: #64748b;
        font-size: 14px;
        margin-bottom: 30px;
        text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 14px;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 14px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: #0877bd;
            box-shadow: 0 0 0 3px rgba(8,119,189,0.1);
        }

        .login-button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 9px;
            background: #0877bd;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-button:hover {
            background: #075b91;
            transform: translateY(-2px);
        }

        .error-message {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }

        @media(max-width: 480px) {
            .login-right {
                padding: 40px 25px;
            }
        }
    </style>
</head>
<div class="login-loading" id="loginLoading">
    <div class="login-loading-card">
        <div class="login-spinner"></div>

        <h6>Memproses Login</h6>
        <p>Mohon tunggu, sedang menghubungkan ke Dashboard...</p>
    </div>
</div>
<body>

<div class="login-container">

    <div class="login-right">

        <h2>APLIKASI ATK MDI</h2>

        <p class="subtitle">
            Silakan login untuk mengakses sistem.
        </p>

        @if(session()->has('loginError'))
            <div class="error-message">
                {{ session('loginError') }}
            </div>
        @endif

        @if($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Username</label>
                <input
                    type="username"
                    name="username"
                    placeholder="Masukkan Username"
                    value="{{ old('username') }}"
                    required
                >
            </div>

            <div class="form-group">
                <label>Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >
            </div>

            <button type="submit" class="login-button">
                Login
            </button>
        </form>

        <div class="footer">
            &copy; 2026 ATK MDI. All Rights Reserved.
        </div>

    </div>

</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const loginForm = document.querySelector('form');
        const loginButton = loginForm
            ? loginForm.querySelector('button[type="submit"], input[type="submit"]')
            : null;

        const loading = document.getElementById('loginLoading');

        if (!loginForm || !loginButton || !loading) return;

        let isSubmitting = false;

        loginForm.addEventListener('submit', function (event) {

            if (isSubmitting) {
                event.preventDefault();
                return false;
            }

            isSubmitting = true;

            loading.classList.add('active');

            loginButton.disabled = true;
            loginButton.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2"></span>
                Memproses...
            `;

        });

    });
</script>
</body>
</html>
