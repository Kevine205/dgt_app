{{-- login.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — DGT Bénin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-800 to-green-600 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-700" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
        </div>
        <h1 class="text-2xl font-bold text-white">DGT Bénin</h1>
        <p class="text-green-200 text-sm">Connexion à votre espace</p>
    </div>
    <div class="bg-white rounded-2xl shadow-2xl p-8">
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse e-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="exemple@email.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="••••••••">
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded"> Se souvenir de moi
                </label>
                <a href="{{ route('password.request') }}" class="text-sm text-green-700 hover:underline">Mot de passe oublié ?</a>
            </div>
            <button type="submit" class="w-full py-3 bg-green-700 text-white rounded-xl font-semibold hover:bg-green-800 transition">
                Se connecter
            </button>
        </form>
        <p class="mt-6 text-center text-sm text-gray-500">
            Pas encore de compte ?
            <a href="{{ route('register') }}" class="text-green-700 font-medium hover:underline">Créer un compte</a>
        </p>
    </div>
    <p class="text-center text-green-200 text-xs mt-6">© {{ date('Y') }} Direction Générale du Travail — Bénin</p>
</div>
</body>
</html>
