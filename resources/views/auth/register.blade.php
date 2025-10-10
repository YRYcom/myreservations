<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>
<body>
    <h1>Créer un compte</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <label for="name">Nom :</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required>
            @error('name') <p style="color:red">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email">Email :</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            @error('email') <p style="color:red">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password">Mot de passe :</label>
            <input id="password" type="password" name="password" required>
            @error('password') <p style="color:red">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation">Confirmer mot de passe :</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>

        <button type="submit">S’inscrire</button>
    </form>
</body>
</html>