@extends('layouts.app')
@section('title', 'Statistiques DGT — Administration')
@section('sidebar-color', 'bg-slate-800')
@section('page-title', 'Rapports & Analyses')
@section('page-subtitle', 'Suivi global et indicateurs clés de performance de la DGT')

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
    </a>
@endsection

@section('content')
<div class="flex justify-end mb-6">
    <a href="{{ route('admin.statistiques.export') }}" class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 shadow-sm transition flex items-center gap-2">
        <i class="fas fa-download"></i>
        Exporter les données complètes (CSV)
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-semibold text-gray-900">Activité annuelle globale</h3>
                <p class="text-xs text-gray-500 mt-0.5">Historique des flux de dossiers sur les 12 derniers mois</p>
            </div>
        </div>
        <div class="relative w-full" style="height: 320px;">
            <canvas id="chartEvolutionAnnuelle"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
        <div>
            <h3 class="font-semibold text-gray-900">Types de contrats</h3>
            <p class="text-xs text-gray-500 mt-0.5">Proportion des dossiers selon le type de contrat</p>
        </div>
        <div class="relative w-full flex items-center justify-center my-auto" style="height: 240px;">
            <canvas id="chartTypeContrat"></canvas>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h3 class="font-semibold text-gray-900 mb-4">Légende & Volume par Contrat</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @forelse($statsTypeContrat as $index => $contrat)
            <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $contrat->type_contrat ?? 'Non spécifié' }}</div>
                <div class="text-2xl font-bold text-gray-900 mt-1">{{ $contrat->total }}</div>
            </div>
        @empty
            <div class="col-span-4 text-center py-4 text-sm text-gray-500">Aucune donnée contractuelle enregistrée.</div>
        @endforelse
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. CONFIGURATION DU GRAPHIQUE COMPLET DES 12 MOIS (Barres empilées/groupées)
    const ctxAnnuel = document.getElementById('chartEvolutionAnnuelle').getContext('2d');
    const donnéesMois = {!! json_encode($statsMois) !!};
    
    new Chart(ctxAnnuel, {
        type: 'bar',
        data: {
            labels: donnéesMois.map(item => item.mois),
            datasets: [
                {
                    label: 'Soumis',
                    data: donnéesMois.map(item => item.soumis),
                    backgroundColor: 'rgba(59, 130, 246, 0.85)', // Blue
                    borderRadius: 4,
                },
                {
                    label: 'Visés',
                    data: donnéesMois.map(item => item.vises),
                    backgroundColor: 'rgba(34, 197, 94, 0.85)', // Green
                    borderRadius: 4,
                },
                {
                    label: 'Rejetés',
                    data: donnéesMois.map(item => item.rejetes),
                    backgroundColor: 'rgba(239, 68, 68, 0.85)', // Red
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { position: 'top', labels: { boxWidth: 12, usePointStyle: true } } 
            },
            scales: { 
                y: { beginAtZero: true, ticks: { stepSize: 5 } } 
            }
        }
    });

    // 2. CONFIGURATION DU CAMEMBERT (DOUGHNUT) DES TYPES DE CONTRAT
    const ctxContrat = document.getElementById('chartTypeContrat').getContext('2d');
    const donnéesContrat = {!! json_encode($statsTypeContrat) !!};

    new Chart(ctxContrat, {
        type: 'doughnut',
        data: {
            labels: donnéesContrat.map(item => item.type_contrat || 'Non spécifié'),
            datasets: [{
                data: donnéesContrat.map(item => item.total),
                backgroundColor: [
                    '#3b82f6', // blue-500
                    '#10b981', // green-500
                    '#f59e0b', // amber-500
                    '#8b5cf6', // purple-500
                    '#ec4899', // pink-500
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 10, padding: 15, usePointStyle: true, font: { size: 11 } }
                }
            },
            cutout: '70%' // Donne l'effet d'anneau moderne
        }
    });
});
</script>
@endsection