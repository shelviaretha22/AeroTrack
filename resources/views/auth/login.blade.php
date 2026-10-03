<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AeroTrack</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #F3F6F5;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            width: 380px;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            margin: 0;
            color: #365F65;
            font-size: 32px;
        }

        .logo p {
            margin-top: 5px;
            color: #777;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            color: #365F65;
            font-size: 14px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            border: 1px solid #D9E5E3;
            border-radius: 8px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #4F8189;
        }

        .login-button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: #4F8189;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .login-button:hover {
            background: #3F7078;
        }
    </style>
</head>

<body>

<div class="login-container">

    <div class="login-box">

        <div class="logo">
            <h1>AeroTrack</h1>
            <p>Airport Tenant Monitoring System</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label>Email</label>
                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >
            </div>

            <div class="form-group">
                <label>Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >
            </div>

            <button type="submit" class="login-button">
                Login
            </button>

        </form>

    </div>

</div>

</body>
</html>