@extends('layouts.app')
@section('title', 'Espace Validateur — DGT')
@section('sidebar-color', 'bg-amber-800')
@section('page-title', 'Tableau de bord Validateur')
@section('page-subtitle', 'Traitement et visa des contrats de travail')

@section('sidebar-nav')
    <a href="{{ route('validateur.dashboard') }}" class="sidebar-link {{ request()->routeIs('validateur.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home w-4"></i> Tableau de bord
    </a>
    <a href="{{ route('validateur.dossiers.index') }}" class="sidebar-link {{ request()->routeIs('validateur.dossiers.*') ? 'active' : '' }}">
        <i class="fas fa-folder w-4"></i> Tous les dossiers
    </a>
    <a href="{{ route('validateur.dossiers.index', ['statut' => 'soumis']) }}" class="sidebar-link">
        <i class="fas fa-inbox w-4"></i> À traiter
        @if($stats['soumis'] > 0)
            <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $stats['soumis'] }}</span>
        @endif
    </a>
    <a href="{{ route('validateur.dossiers.index', ['statut' => 'en_cours']) }}" class="sidebar-link">
        <i class="fas fa-stamp w-4"></i> En cours
    </a>
    <a href="{{ route('validateur.dossiers.index', ['statut' => 'entretien_requis']) }}" class="sidebar-link">
        <i class="fas fa-calendar w-4"></i> Entretiens
        @if($stats['entretiens'] > 0)
            <span class="ml-auto bg-purple-500 text-white text-xs rounded-full px-2 py-0.5">{{ $stats['entretiens'] }}</span>
        @endif
    </a>
    <a href="{{ route('validateur.profil') }}" class="sidebar-link {{ request()->routeIs('validateur.profil') ? 'active' : '' }}"><i class="fas fa-user w-4"></i> Mon profil</a>
    <a href="{{ route('validateur.audit') }}" class="sidebar-link">
        <i class="fas fa-list w-4"></i> Journal d'audit
    </a>
@endsection

@section('content')
<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
    @foreach([
        ['label' => 'Soumis', 'val' => $stats['soumis'], 'color' => 'blue', 'icon' => 'fa-inbox'],
        ['label' => 'En cours', 'val' => $stats['en_cours'], 'color' => 'yellow', 'icon' => 'fa-spinner'],
        ['label' => 'Entretiens', 'val' => $stats['entretiens'], 'color' => 'purple', 'icon' => 'fa-calendar-alt'],
        ['label' => 'Arbitrages', 'val' => $stats['arbitrages'], 'color' => 'red', 'icon' => 'fa-gavel'],
        ['label' => 'Visés ce mois', 'val' => $stats['vises_mois'], 'color' => 'green', 'icon' => 'fa-check-circle'],
        ['label' => 'Rejetés ce mois', 'val' => $stats['rejetes_mois'], 'color' => 'gray', 'icon' => 'fa-times-circle'],
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

<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-900">Dossiers à traiter en priorité</h2>
        <a href="{{ route('validateur.dossiers.index') }}" class="text-sm text-amber-700 hover:underline font-medium">Voir tout →</a>
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
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($dossiersPrioritaires as $dossier)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-mono font-medium text-sm">{{ $dossier->numero_suivi }}</td>
                    <td class="px-6 py-4">{{ $dossier->prenom_employe }} {{ $dossier->nom_employe }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $dossier->type_contrat }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium badge-{{ $dossier->statut }}">
                            {{ $dossier->statut_info['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $dossier->date_soumission->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('validateur.dossiers.show', $dossier) }}"
                            class="text-amber-700 hover:text-amber-900 text-xs font-medium">
                            Traiter →
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">Aucun dossier en attente.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
