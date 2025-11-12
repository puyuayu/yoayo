<!DOCTYPE html>
<html>
<head>
    <title>Register - Dessertique</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fffff;
            margin: 0;
            padding: 0;
            background-image: url("{{ asset('images/background.png') }}");
            background-size: 400px;
            background-repeat: repeat;
            background-position: center;
        }

        .register-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.92);
            padding: 35px 40px;
            border-radius: 15px;
            max-width: 350px;
            width: 100%;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: 2px solid #B4D4FF;
        }

        h2 {
            margin-bottom: 20px;
            color: #2A4D79;
            font-weight: 600;
        }

        input {
            width: 85%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #A5C7FF;
            border-radius: 8px;
            outline: none;
            font-family: 'Poppins', sans-serif;
        }

        input:focus {
            border-color: #6EA8FF;
        }

        button {
            width: 90%;
            padding: 10px;
            background-color: #6EA8FF;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
            font-weight: bold;
            font-family: 'Poppins', sans-serif;
        }

        button:hover {
            background-color: #4E90F0;
        }

        a {
            color: #4E90F0;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>

</head>

<body>

<div class="register-wrapper">
    <div class="register-card">

        <h2>Register</h2>

        @if (session('success'))
            <p style="color: green;">{{ session('success') }}</p>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Nama" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required><br>

            <button type="submit">Daftar</button>
        </form>

        <p style="margin-top: 10px;">
            Sudah punya akun? <a href="{{ route('login') }}">Login</a>
        </p>

    </div>
</div>

</body>
</html>
