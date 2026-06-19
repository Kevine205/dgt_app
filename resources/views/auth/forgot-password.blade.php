<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié — DGT Bénin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-800 to-green-600 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-key text-green-700 text-xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-white">Mot de passe oublié</h1>
        <p class="text-green-200 text-sm">Recevez un lien de réinitialisation par e-mail</p>
    </div>
    <div class="bg-white rounded-2xl shadow-2xl p-8">

        @if(session('status'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            {{ session('status') }}
        </div>
        @endif

        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        <p class="text-sm text-gray-500 mb-5">
            Saisissez votre adresse e-mail. Vous recevrez un lien pour réinitialiser votre mot de passe.
        </p>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse e-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="exemple@email.com">
            </div>
            <button type="submit" class="w-full py-3 bg-green-700 text-white rounded-xl font-semibold hover:bg-green-800 transition">
                Envoyer le lien de réinitialisation
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            <a href="{{ route('login') }}" class="text-green-700 font-medium hover:underline">← Retour à la connexion</a>
        </p>
    </div>
</div>
</body>
</html>
