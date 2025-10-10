<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="login-logo">
            <h1>Réserver</h1>
        </div>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <label for="email">Adresse mail</label>
            <input type="email" id="email" name="email" required>
            <br>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Se connecter</button>
            <br>
            <a href="{{ route('register') }}">Pas encore de compte, créez en un</a>
        </form>
    </div>
</body>
</html>