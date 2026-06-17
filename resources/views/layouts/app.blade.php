<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DGT — Dématérialisation Contrats')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        .sidebar-link { @apply flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200; }
        .sidebar-link:hover { @apply bg-white/10; }
        .sidebar-link.active { @apply bg-white/20 font-semibold; }
        .badge-soumis { @apply bg-blue-100 text-blue-800; }
        .badge-en_cours { @apply bg-yellow-100 text-yellow-800; }
        .badge-correction_demandee { @apply bg-orange-100 text-orange-800; }
        .badge-entretien_requis { @apply bg-purple-100 text-purple-800; }
        .badge-en_attente_arbitrage { @apply bg-red-100 text-red-800; }
        .badge-vise { @apply bg-green-100 text-green-800; }
        .badge-rejete { @apply bg-red-100 text-red-800; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex">

    <!-- SIDEBAR -->
    <aside class="w-64 min-h-screen flex-shrink-0 @yield('sidebar-color', 'bg-blue-900') text-white flex flex-col">
        <div class="p-6 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-file-contract text-white"></i>
                </div>
                <div>
                    <div class="font-bold text-sm">DGT Bénin</div>
                    <div class="text-xs text-white/60">Contrats de travail</div>
                </div>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1">
            @yield('sidebar-nav')
        </nav>

        <div class="p-4 border-t border-white/10">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-xs font-bold">
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}{{ strtoupper(substr(auth()->user()->nom, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium truncate">{{ auth()->user()->nom_complet }}</div>
                    <div class="text-xs text-white/60 truncate">{{ auth()->user()->getRoleNames()->first() }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-white/70 hover:text-white hover:bg-white/10 transition">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </button>
            </form>
        </div>
    </aside>

    <!-- CONTENU PRINCIPAL -->
    <div class="flex-1 flex flex-col min-h-screen">
        <!-- TOPBAR -->
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Tableau de bord')</h1>
                <p class="text-sm text-gray-500">@yield('page-subtitle', '')</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500">{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
            </div>
        </header>

        <!-- ALERTES -->
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <span class="text-green-800 text-sm">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <span class="text-red-800 text-sm">{{ session('error') }}</span>
                </div>
            @endif
            @if(session('warning'))
                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                    <span class="text-yellow-800 text-sm">{{ session('warning') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- PAGE CONTENT -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>

        <footer class="px-6 py-4 border-t border-gray-200 text-center text-xs text-gray-400">
            © {{ date('Y') }} Direction Générale du Travail — Bénin. Tous droits réservés.
        </footer>
    </div>

</body>
</html>
