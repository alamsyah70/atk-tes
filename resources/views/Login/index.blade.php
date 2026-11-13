
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ATK MDI</title>

    <style>
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

</body>
</html>
