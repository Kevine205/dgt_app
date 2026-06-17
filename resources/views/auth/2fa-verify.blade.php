{{-- 2fa-verify.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Vérification 2FA — DGT</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gradient-to-br from-green-800 to-green-600 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-sm">
    <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 mb-2">Vérification 2FA</h2>
        <p class="text-sm text-gray-500 mb-6">Saisissez le code à 6 chiffres généré par votre application d'authentification.</p>
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif
        <form method="POST" action="{{ route('2fa.verify') }}">
            @csrf
            <input type="text" name="otp" maxlength="6" autofocus required
                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl text-2xl text-center font-mono tracking-widest focus:border-green-500 focus:outline-none mb-4"
                placeholder="000000">
            <button type="submit" class="w-full py-3 bg-green-700 text-white rounded-xl font-semibold hover:bg-green-800 transition">
                Vérifier
            </button>
        </form>
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button class="text-sm text-gray-400 hover:text-gray-600">← Retour à la connexion</button>
        </form>
    </div>
</div>
</body>
</html>
