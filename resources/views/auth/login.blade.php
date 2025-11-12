<!DOCTYPE html>
<html>
<head>
    <title>Login - Dessertique</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fffff;
            margin: 0;
            padding: 0;
            background-image: url("{{ asset('images/background.png') }}");

            background-size: 400px;
            background-repeat: repeat;
            background-position: center;
        }

        .login-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.92);
            padding: 35px 40px;
            border-radius: 15px;
            max-width: 350px;
            width: 100%;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: 2px solid #B4D4FF; /* outline biru soft */
        }

        h2 {
            margin-bottom: 20px;
            color: #2A4D79; /* biru gelap manis */
        }

        input {
            width: 85%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #A5C7FF; /* garis biru muda */
            border-radius: 8px;
            outline: none;
        }

        input:focus {
            border-color: #6EA8FF; /* fokus lebih terang */
        }

        button {
            width: 90%;
            padding: 10px;
            background-color: #6EA8FF; /* biru pastel */
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
            font-weight: bold;
        }

        button:hover {
            background-color: #4E90F0; /* hover lebih gelap */
        }

        a {
            color: #4E90F0; /* link biru elegan */
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>

</head>

<body>

<div class="login-wrapper">
    <div class="login-card">
        <h2>Login</h2>

        @if (session('error'))
            <p style="color:red">{{ session('error') }}</p>
        @endif
        @if (session('success'))
            <p style="color:green">{{ session('success') }}</p>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>

        <p>Belum punya akun? <br><a href="{{ route('register') }}">Daftar</a></p>
    </div>
</div>

</body>
</html>
