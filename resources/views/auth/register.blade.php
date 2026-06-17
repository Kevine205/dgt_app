<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — DGT Bénin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-800 to-green-600 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-lg">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-white">Créer un compte</h1>
        <p class="text-green-200 text-sm">Plateforme DGT — Dématérialisation des contrats de travail</p>
    </div>
    <div class="bg-white rounded-2xl shadow-2xl p-8">
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:outline-none"
                        placeholder="HOUNSOU">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prénom <span class="text-red-500">*</span></label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:outline-none"
                        placeholder="Jean">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse e-mail <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:outline-none"
                    placeholder="jean.hounsou@email.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                <input type="tel" name="telephone" value="{{ old('telephone') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:outline-none"
                    placeholder="+229 XX XX XX XX">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe <span class="text-red-500">*</span></label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:outline-none"
                    placeholder="8 caractères minimum, lettres + chiffres">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:outline-none"
                    placeholder="••••••••">
            </div>
            <button type="submit" class="w-full py-3 bg-green-700 text-white rounded-xl font-semibold hover:bg-green-800 transition mt-2">
                Créer mon compte
            </button>
        </form>
        <p class="mt-4 text-center text-sm text-gray-500">
            Déjà un compte ?
            <a href="{{ route('login') }}" class="text-green-700 font-medium hover:underline">Se connecter</a>
        </p>
    </div>
</div>
</body>
</html>
