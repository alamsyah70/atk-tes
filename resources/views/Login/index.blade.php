<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ATK MDI</title>

    <style>
        /* =====================================
           GLOBAL
        ===================================== */
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
            padding: 25px;
            background: #071B2B;
        }

        /* =====================================
           LOGIN WRAPPER
        ===================================== */
        .login-wrapper {
            width: 100%;
            max-width: 720px;
            display: flex;
            align-items: stretch;
            justify-content: center;
            gap: 18px;
        }

        /* =====================================
           LOGIN CARD
        ===================================== */
        .login-container {
            flex: 1;
            min-width: 0;
            max-width: 350px;
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 15px 45px rgba(0, 0, 0, .15);
        }

        .login-content {
            width: 100%;
            height: 100%;
            padding: 45px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-content h2 {
            color: #1e293b;
            font-size: 25px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: 800;
        }

        .subtitle {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 28px;
            text-align: center;
        }

        /* =====================================
           FORM
        ===================================== */
        .form-group {
            margin-bottom: 19px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 13px;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 13px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            font-size: 13px;
            outline: none;
            transition: .25s;
        }

        .form-group input:focus {
            border-color: #0877bd;
            box-shadow: 0 0 0 3px rgba(8, 119, 189, .1);
        }

        .login-button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 9px;
            background: #0877bd;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: .25s;
        }

        .login-button:hover {
            background: #075b91;
            transform: translateY(-2px);
        }

        .login-button:disabled {
            opacity: .75;
            cursor: not-allowed;
            transform: none;
        }

        /* =====================================
           ERROR MESSAGE
        ===================================== */
        .error-message {
            background: #fee2e2;
            color: #b91c1c;
            padding: 11px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 12px;
        }

        /* =====================================
           ACCOUNT INFORMATION
        ===================================== */
        .account-container {
            width: 300px;
            flex-shrink: 0;
            background: #fff;
            border-radius: 18px;
            padding: 28px 23px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, .15);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .account-header {
            margin-bottom: 20px;
        }

        .account-header h3 {
            color: #1e293b;
            font-size: 18px;
            margin-bottom: 6px;
        }

        .account-subtitle {
            color: #64748b;
            font-size: 11px;
            line-height: 1.6;
        }

        .account-item {
            padding: 13px;
            background: #f1f7fc;
            border: 1px solid #dceaf5;
            border-radius: 10px;
            margin-bottom: 11px;
        }

        .account-item:last-child {
            margin-bottom: 0;
        }

        .account-item strong {
            display: block;
            color: #0877bd;
            font-size: 13px;
            margin-bottom: 9px;
        }

        .account-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            font-size: 11px;
            margin-top: 7px;
        }

        .account-row span {
            color: #64748b;
        }

        .account-row b {
            color: #1e293b;
            text-align: right;
            overflow-wrap: anywhere;
        }

        .account-note {
            margin-top: 15px;
            color: #94a3b8;
            font-size: 10px;
            line-height: 1.5;
            text-align: center;
        }

        /* =====================================
           FOOTER
        ===================================== */
        .footer {
            margin-top: 27px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
        }

        /* =====================================
           LOADING OVERLAY
        ===================================== */
        .login-loading {
            position: fixed;
            inset: 0;
            z-index: 999999;
            display: none;
            align-items: center;
            justify-content: center;
            background: transparent;
            pointer-events: all;
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
            margin-bottom: 6px;
            color: #294568;
            font-size: 14px;
            font-weight: 700;
        }

        .login-loading-card p {
            color: #8997a9;
            font-size: 11px;
            line-height: 1.5;
        }

        @keyframes loginSpin {
            to {
                transform: rotate(360deg);
            }
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

        /* =====================================
           RESPONSIVE
        ===================================== */
        @media (max-width: 680px) {
            body {
                padding: 20px 15px;
                align-items: flex-start;
            }

            .login-wrapper {
                max-width: 350px;
                flex-direction: column;
                margin: auto;
            }

            .login-container {
                width: 100%;
                max-width: 100%;
            }

            .login-content {
                padding: 38px 25px;
            }

            .account-container {
                width: 100%;
                padding: 25px;
            }
        }

        @media (max-width: 360px) {
            .login-content h2 {
                font-size: 21px;
            }
        }
    </style>
</head>

<body>

    <!-- =====================================
         LOADING
    ====================================== -->
    <div class="login-loading" id="loginLoading">
        <div class="login-loading-card">
            <div class="login-spinner"></div>

            <h6>Memproses Login</h6>

            <p>
                Mohon tunggu, sedang menghubungkan ke Dashboard...
            </p>
        </div>
    </div>

    <!-- =====================================
         MAIN WRAPPER
    ====================================== -->
    <div class="login-wrapper">

        <!-- LOGIN CARD -->
        <div class="login-container">

            <div class="login-content">

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

                <form action="{{ route('login') }}" method="POST" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <label for="username">Username</label>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Masukkan Username"
                            value="{{ old('username') }}"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan Password"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <button type="submit" class="login-button" id="loginButton">
                        Login
                    </button>
                </form>

                <div class="footer">
                    &copy; 2026 ATK MDI. All Rights Reserved.
                </div>

            </div>
        </div>

        <!-- ACCOUNT INFORMATION CARD -->
        <div class="account-container">

            <div class="account-header">
                <h3>Informasi Akun</h3>

                <p class="account-subtitle">
                    Daftar akun untuk keperluan pengujian sistem.
                </p>
            </div>

            <!-- SUPER ADMIN -->
            <div class="account-item">
                <strong>Super Admin</strong>

                <div class="account-row">
                    <span>Username</span>
                    <b>superadmin</b>
                </div>

                <div class="account-row">
                    <span>Password</span>
                    <b>12345678</b>
                </div>
            </div>


            <!-- ADMIN CABANG -->
            <div class="account-item">
                <strong>Admin Cabang / HO</strong>

                <div class="account-row">
                    <span>Username</span>
                    <b>HO</b>
                </div>

                <div class="account-row">
                    <span>Password</span>
                    <b>12345678</b>
                </div>
            </div>

            <div class="account-item">
                <strong>Admin Cabang / DB14</strong>

                <div class="account-row">
                    <span>Username</span>
                    <b>DB14</b>
                </div>

                <div class="account-row">
                    <span>Password</span>
                    <b>12345678</b>
                </div>
            </div>

            <div class="account-item">
                <strong>Inventory Auditor - HO</strong>

                <div class="account-row">
                    <span>Username</span>
                    <b>auditHO</b>
                </div>

                <div class="account-row">
                    <span>Password</span>
                    <b>12345678</b>
                </div>
            </div>

            <div class="account-item">
                <strong>Inventory Auditor - DB14</strong>

                <div class="account-row">
                    <span>Username</span>
                    <b>auditDB14</b>
                </div>

                <div class="account-row">
                    <span>Password</span>
                    <b>12345678</b>
                </div>
            </div>

            <p class="account-note">
                Gunakan akun sesuai hak akses masing-masing.
            </p>

        </div>

    </div>

    <!-- =====================================
         LOGIN SCRIPT
    ====================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const loginForm = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');
            const loading = document.getElementById('loginLoading');

            if (!loginForm || !loginButton || !loading) return;

            let isSubmitting = false;

            loginForm.addEventListener('submit', function (event) {

                if (isSubmitting) {
                    event.preventDefault();
                    return;
                }

                isSubmitting = true;

                loading.classList.add('active');

                loginButton.disabled = true;

                loginButton.innerHTML = `
                    <span style="
                        display:inline-block;
                        width:13px;
                        height:13px;
                        border:2px solid rgba(255,255,255,.4);
                        border-top-color:#fff;
                        border-radius:50%;
                        animation:loginSpin .7s linear infinite;
                        vertical-align:middle;
                        margin-right:7px;
                    "></span>
                    Memproses...
                `;

            });

        });
    </script>

</body>
</html>
