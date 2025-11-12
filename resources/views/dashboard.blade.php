<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Dessertique</title>
</head>
<body>
    <h2>Selamat datang, {{ session('user.name') }}</h2>
    <a href="{{ route('logout') }}">Logout</a>
</body>
</html>