@extends('layouts.app')
@section('title', 'Administration — DGT')
@section('sidebar-color', 'bg-slate-800')
@section('page-title', 'Administration')
@section('page-subtitle', 'Supervision et gestion de la plateforme DGT')

@section('sidebar-nav')
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home w-4"></i> Tableau de bord
    </a>
    <a href="{{ route('admin.agents.index') }}" class="sidebar-link {{ request()->routeIs('admin.agents.*') ? 'active' : '' }}">
        <i class="fas fa-users w-4"></i> Agents DGT
    </a>
    <a href="{{ route('admin.statistiques') }}" class="sidebar-link {{ request()->routeIs('admin.statistiques') ? 'active' : '' }}">
        <i class="fas fa-chart-bar w-4"></i> Statistiques
    </a>
    <a href="{{ route('admin.audit') }}" class="sidebar-link {{ request()->routeIs('admin.audit') ? 'active' : '' }}">
        <i class="fas fa-shield-alt w-4"></i> Journal d'audit
    </a>
    <div class="pt-4 pb-2 px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Accès rapide</div>
    <a href="{{ route('validateur.dashboard') }}" class="sidebar-link">
        <i class="fas fa-stamp w-4"></i> Espace Validateur
    
@endsection

@section('content')
<!-- STATS GLOBALES -->
<div class="grid grid-cols-3 md:grid-cols-5 gap-4 mb-8">
    @foreach([
        ['label' => 'Total dossiers', 'val' => $stats['total_dossiers'], 'color' => 'blue', 'icon' => 'fa-folder'],
        ['label' => 'Soumis', 'val' => $stats['soumis'], 'color' => 'sky', 'icon' => 'fa-upload'],
        ['label' => 'Visés', 'val' => $stats['vises'], 'color' => 'green', 'icon' => 'fa-check-circle'],
        ['label' => 'Rejetés', 'val' => $stats['rejetes'], 'color' => 'red', 'icon' => 'fa-times-circle'],
        ['label' => 'Entretiens', 'val' => $stats['entretiens'], 'color' => 'purple', 'icon' => 'fa-calendar'],
    ] as $stat)
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-gray-500">{{ $stat['label'] }}</span>
            <div class="w-8 h-8 bg-{{ $stat['color'] }}-100 rounded-lg flex items-center justify-center">
                <i class="fas {{ $stat['icon'] }} text-{{ $stat['color'] }}-600 text-sm"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-gray-900">{{ $stat['val'] }}</div>
    </div>
    @endforeach
</div>

<!-- ÉQUIPE DGT -->
<div class="grid md:grid-cols-3 gap-4 mb-8">
    @foreach([
        ['label' => 'Usagers inscrits', 'val' => $stats['total_usagers'], 'color' => 'blue', 'icon' => 'fa-user'],
        ['label' => 'Validateurs', 'val' => $stats['total_validateurs'], 'color' => 'amber', 'icon' => 'fa-user-check'],
    ] as $stat)
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 bg-{{ $stat['color'] }}-100 rounded-xl flex items-center justify-center">
            <i class="fas {{ $stat['icon'] }} text-{{ $stat['color'] }}-600 text-lg"></i>
        </div>
        <div>
            <div class="text-2xl font-bold text-gray-900">{{ $stat['val'] }}</div>
            <div class="text-sm text-gray-500">{{ $stat['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<!-- GRAPHIQUE 6 MOIS -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="font-semibold text-gray-900">Activité des 6 derniers mois</h3>
        <a href="{{ route('admin.statistiques.export') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
            <i class="fas fa-download mr-2"></i>Export CSV
        </a>
    </div>
    <canvas id="chartActivite" height="80"></canvas>
</div>

<script>
const ctx = document.getElementById('chartActivite').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($statsMois, 'mois')) !!},
        datasets: [
            {
                label: 'Soumis',
                data: {!! json_encode(array_column($statsMois, 'soumis')) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderRadius: 4,
            },
            {
                label: 'Visés',
                data: {!! json_encode(array_column($statsMois, 'vises')) !!},
                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                borderRadius: 4,
            },
            {
                label: 'Rejetés',
                data: {!! json_encode(array_column($statsMois, 'rejetes')) !!},
                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                borderRadius: 4,
            },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>

<!-- ACTIONS RAPIDES -->
<div class="grid md:grid-cols-3 gap-4">
    <a href="{{ route('admin.agents.create') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:border-slate-300 transition group">
        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-slate-200 transition">
            <i class="fas fa-user-plus text-slate-700"></i>
        </div>
        <div class="font-semibold text-gray-900">Créer un agent</div>
        <div class="text-sm text-gray-500 mt-1">Ajouter un nouveau membre de l'équipe DGT</div>
    </a>
    <a href="{{ route('admin.audit') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:border-slate-300 transition group">
        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-slate-200 transition">
            <i class="fas fa-shield-alt text-slate-700"></i>
        </div>
        <div class="font-semibold text-gray-900">Journal d'audit</div>
        <div class="text-sm text-gray-500 mt-1">Consulter toutes les actions réalisées</div>
    </a>
    <a href="{{ route('admin.statistiques') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:border-slate-300 transition group">
        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-slate-200 transition">
            <i class="fas fa-chart-line text-slate-700"></i>
        </div>
        <div class="font-semibold text-gray-900">Statistiques détaillées</div>
        <div class="text-sm text-gray-500 mt-1">Rapports mensuels et annuels exportables</div>
    </a>
</div>
@endsection
