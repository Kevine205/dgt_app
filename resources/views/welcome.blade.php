<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DGT Bénin — Dématérialisation des Contrats de Travail</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="bg-white">

<!-- NAVBAR -->
<nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-700 rounded-full flex items-center justify-center">
                <i class="fas fa-file-contract text-white text-sm"></i>
            </div>
            <div>
                <div class="font-bold text-gray-900 text-sm">DGT Bénin</div>
                <div class="text-xs text-gray-500">Direction Générale du Travail</div>
            </div>
        </div>
        <div class="flex items-center gap-4">
            @auth
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-green-700 text-white rounded-lg text-sm font-medium hover:bg-green-800 transition">
                    Mon espace
                </a>
            @else
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">Connexion</a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-green-700 text-white rounded-lg text-sm font-medium hover:bg-green-800 transition">
                    Créer un compte
                </a>
            @endauth
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="bg-gradient-to-br from-green-800 to-green-600 text-white py-24">
    <div class="max-w-5xl mx-auto px-6 text-center">
        <div class="inline-flex items-center gap-2 bg-white/20 rounded-full px-4 py-2 text-sm mb-6">
            <i class="fas fa-shield-alt"></i>
            Plateforme officielle de la DGT — Bénin
        </div>
        <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
            Visez vos contrats de travail<br/>depuis chez vous
        </h1>
        <p class="text-xl text-green-100 mb-10 max-w-2xl mx-auto">
            Soumettez votre dossier en ligne, suivez son avancement en temps réel et téléchargez directement votre contrat visé. Sans déplacement systématique.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-green-800 rounded-xl font-bold text-lg hover:bg-green-50 transition shadow-lg">
                <i class="fas fa-plus mr-2"></i>Déposer un dossier
            </a>
            <a href="{{ route('login') }}" class="px-8 py-4 bg-white/10 border border-white/30 text-white rounded-xl font-medium text-lg hover:bg-white/20 transition">
                <i class="fas fa-search mr-2"></i>Suivre mon dossier
            </a>
        </div>
    </div>
</section>

<!-- ÉTAPES -->
<section class="py-20 bg-gray-50">
    <div class="max-w-5xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-4">Comment ça marche ?</h2>
        <p class="text-center text-gray-500 mb-12">Quatre étapes simples pour obtenir votre contrat visé.</p>
        <div class="grid md:grid-cols-4 gap-6">
            @foreach([
                ['icon' => 'fa-user-plus', 'num' => '1', 'titre' => 'Inscription', 'desc' => 'Créez votre compte gratuitement en quelques secondes.', 'color' => 'blue'],
                ['icon' => 'fa-upload', 'num' => '2', 'titre' => 'Soumission', 'desc' => 'Remplissez le formulaire et téléversez vos pièces justificatives.', 'color' => 'yellow'],
                ['icon' => 'fa-cogs', 'num' => '3', 'titre' => 'Traitement', 'desc' => 'Les agents de la DGT examinent votre dossier. Vous êtes notifié à chaque étape.', 'color' => 'purple'],
                ['icon' => 'fa-download', 'num' => '4', 'titre' => 'Téléchargement', 'desc' => 'Téléchargez directement votre contrat visé une fois validé.', 'color' => 'green'],
            ] as $etape)
            <div class="bg-white rounded-2xl p-6 shadow-sm text-center">
                <div class="w-12 h-12 bg-{{ $etape['color'] }}-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas {{ $etape['icon'] }} text-{{ $etape['color'] }}-600"></i>
                </div>
                <div class="text-xs font-bold text-{{ $etape['color'] }}-600 mb-1">ÉTAPE {{ $etape['num'] }}</div>
                <h3 class="font-bold text-gray-900 mb-2">{{ $etape['titre'] }}</h3>
                <p class="text-sm text-gray-500">{{ $etape['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- AVANTAGES -->
<section class="py-20 bg-white">
    <div class="max-w-5xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Pourquoi cette plateforme ?</h2>
        <div class="grid md:grid-cols-3 gap-8">
            @foreach([
                ['icon' => 'fa-clock', 'titre' => 'Gain de temps', 'desc' => 'Finies les files d\'attente. Soumettez votre dossier en 10 minutes depuis n\'importe où au Bénin.'],
                ['icon' => 'fa-bell', 'titre' => 'Suivi en temps réel', 'desc' => 'Recevez un e-mail à chaque étape. Sachez exactement où en est votre dossier, à tout moment.'],
                ['icon' => 'fa-shield-alt', 'titre' => 'Sécurité garantie', 'desc' => 'Vos documents sont chiffrés et stockés de manière sécurisée. Accès contrôlé par rôle.'],
            ] as $avantage)
            <div class="flex gap-4">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0 mt-1">
                    <i class="fas {{ $avantage['icon'] }} text-green-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 mb-1">{{ $avantage['titre'] }}</h3>
                    <p class="text-sm text-gray-500">{{ $avantage['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="bg-gray-900 text-gray-400 py-12">
    <div class="max-w-5xl mx-auto px-6 text-center">
        <div class="font-bold text-white mb-2">Direction Générale du Travail — République du Bénin</div>
        <p class="text-sm mb-4">Cotonou, Bénin | contact@dgt.bj</p>
        <p class="text-xs">© {{ date('Y') }} DGT Bénin — Tous droits réservés.</p>
    </div>
</footer>

</body>
</html>
