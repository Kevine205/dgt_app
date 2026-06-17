<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Configuration 2FA — DGT</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gradient-to-br from-green-800 to-green-600 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-2xl p-8">
        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Configurer l'authentification 2FA</h2>
            <p class="text-sm text-gray-500 mt-1">Cette étape est obligatoire pour les agents DGT.</p>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 text-sm text-blue-800">
            <strong>Étapes :</strong>
            <ol class="list-decimal list-inside mt-2 space-y-1">
                <li>Installez <strong>Google Authenticator</strong> ou <strong>Authy</strong> sur votre téléphone.</li>
                <li>Scannez le QR code ci-dessous.</li>
                <li>Saisissez le code à 6 chiffres pour confirmer.</li>
            </ol>
        </div>

        <!-- QR Code SVG -->
        <div class="flex justify-center mb-4">
            <div class="bg-white p-3 rounded-xl border-2 border-gray-200">
                {!! $qrSvg !!}
            </div>
        </div>

        <!-- Clé secrète manuelle -->
        <div class="bg-gray-50 rounded-xl p-3 mb-6 text-center">
            <p class="text-xs text-gray-500 mb-1">Clé manuelle (si QR non lisible)</p>
            <code class="text-sm font-mono font-bold text-gray-800 tracking-widest">{{ $secret }}</code>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('2fa.enable') }}">
            @csrf
            <label class="block text-sm font-medium text-gray-700 mb-1">Code de confirmation</label>
            <input type="text" name="otp" maxlength="6" autofocus required
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-xl text-center font-mono tracking-widest focus:border-green-500 focus:outline-none mb-4"
                placeholder="000000">
            <button type="submit" class="w-full py-3 bg-green-700 text-white rounded-xl font-semibold hover:bg-green-800 transition">
                Activer la 2FA
            </button>
        </form>
    </div>
</div>
</body>
</html>
