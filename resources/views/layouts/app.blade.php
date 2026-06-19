<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DGT — Dématérialisation Contrats')</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='48' fill='%23166534'/%3E%3Ctext x='50' y='66' font-size='40' font-weight='bold' fill='white' text-anchor='middle' font-family='Arial'%3EDGT%3C/text%3E%3C/svg%3E">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            transition: all 0.2s;
            margin-bottom: 2px;
        }
        .sidebar-link:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar-link.active { background: rgba(255,255,255,0.2); color: #fff; font-weight: 600; }
        .sidebar-link i { width: 18px; text-align: center; flex-shrink: 0; }
        .badge-soumis { background: #dbeafe; color: #1e40af; }
        .badge-en_cours { background: #fef9c3; color: #854d0e; }
        .badge-correction_demandee { background: #ffedd5; color: #9a3412; }
        .badge-entretien_requis { background: #f3e8ff; color: #6b21a8; }
        .badge-en_attente_arbitrage { background: #fee2e2; color: #991b1b; }
        .badge-vise { background: #dcfce7; color: #166534; }
        .badge-rejete { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex">

    <!-- SIDEBAR -->
    <aside class="w-64 min-h-screen flex-shrink-0 @yield('sidebar-color', 'bg-blue-900') text-white flex flex-col">
        <div class="p-6 border-b border-white border-opacity-10">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-white rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg width="28" height="28" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="46" fill="none" stroke="#166534" stroke-width="4"/>
                        <path d="M30 65 L30 35 L42 35 Q55 35 55 47 Q55 58 42 58 L36 58 L36 65 Z M36 41 L36 52 L42 52 Q49 52 49 47 Q49 41 42 41 Z" fill="#166534"/>
                        <path d="M60 35 L72 35 Q78 35 78 41 Q78 45 74 47 Q79 49 79 54 Q79 65 67 65 L60 65 Z M66 41 L66 47 L71 47 Q73 47 73 44 Q73 41 71 41 Z M66 53 L66 59 L67 59 Q73 59 73 56 Q73 53 67 53 Z" fill="#166534"/>
                        <line x1="50" y1="20" x2="50" y2="28" stroke="#166534" stroke-width="3" stroke-linecap="round"/>
                        <circle cx="50" cy="16" r="3" fill="#166534"/>
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-sm leading-tight">DGT Bénin</div>
                    <div class="text-xs text-white text-opacity-60 leading-tight">Contrats de travail</div>
                </div>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            @yield('sidebar-nav')
        </nav>

        <div class="p-4 border-t border-white border-opacity-10">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}{{ strtoupper(substr(auth()->user()->nom, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium truncate">{{ auth()->user()->nom_complet }}</div>
                    <div class="text-xs text-white text-opacity-60 truncate">{{ ucfirst(auth()->user()->getRoleNames()->first()) }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-white text-opacity-70 hover:text-white hover:bg-white hover:bg-opacity-10 transition">
                    <i class="fas fa-sign-out-alt w-4"></i> Déconnexion
                </button>
            </form>
        </div>
    </aside>

    <!-- CONTENU PRINCIPAL -->
    <div class="flex-1 flex flex-col min-h-screen">
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Tableau de bord')</h1>
                <p class="text-sm text-gray-500">@yield('page-subtitle', '')</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500">{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
            </div>
        </header>

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

        <main class="flex-1 p-6">
            @yield('content')
        </main>

        <footer class="px-6 py-4 border-t border-gray-200 text-center text-xs text-gray-400">
            © {{ date('Y') }} Direction Générale du Travail — Bénin. Tous droits réservés.
        </footer>
    </div>

</body>
</html>
