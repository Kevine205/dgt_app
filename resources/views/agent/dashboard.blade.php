{{-- agent/dashboard.blade.php --}}
@extends('layouts.app')
@section('title', 'Espace Agent — DGT')
@section('sidebar-color', 'bg-emerald-800')
@section('page-title', 'Tableau de bord Agent')
@section('page-subtitle', 'Gestion des dossiers en cours de traitement')

@section('sidebar-nav')
    <a href="{{ route('agent.dashboard') }}" class="sidebar-link {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home w-4"></i> Tableau de bord
    </a>
    <a href="{{ route('agent.dossiers.index') }}" class="sidebar-link {{ request()->routeIs('agent.dossiers.*') ? 'active' : '' }}">
        <i class="fas fa-folder w-4"></i> Tous les dossiers
    </a>
    <a href="{{ route('agent.dossiers.index', ['statut' => 'soumis']) }}" class="sidebar-link">
        <i class="fas fa-inbox w-4"></i> À traiter
        @if($stats['soumis'] > 0)
            <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $stats['soumis'] }}</span>
        @endif
    </a>
    <a href="{{ route('agent.dossiers.index', ['statut' => 'en_cours']) }}" class="sidebar-link">
        <i class="fas fa-spinner w-4"></i> En cours
    </a>
@endsection

@section('content')
<!-- STATS -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['label' => 'À traiter', 'val' => $stats['soumis'], 'color' => 'blue', 'icon' => 'fa-inbox'],
        ['label' => 'En cours', 'val' => $stats['en_cours'], 'color' => 'yellow', 'icon' => 'fa-spinner'],
        ['label' => 'Corrections', 'val' => $stats['corrections'], 'color' => 'orange', 'icon' => 'fa-edit'],
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

<!-- DERNIERS DOSSIERS -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-900">Dossiers récents</h2>
        <a href="{{ route('agent.dossiers.index') }}" class="text-sm text-emerald-700 hover:underline font-medium">Voir tout</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">N° Suivi</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Employé</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Soumis le</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($derniersDossiers as $dossier)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-mono text-sm font-medium">{{ $dossier->numero_suivi }}</td>
                    <td class="px-6 py-4">{{ $dossier->prenom_employe }} {{ $dossier->nom_employe }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $dossier->type_contrat }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium badge-{{ $dossier->statut }}">{{ $dossier->statut_info['label'] }}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $dossier->date_soumission->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('agent.dossiers.show', $dossier) }}" class="text-emerald-700 hover:text-emerald-900 text-xs font-medium">Instruire →</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
