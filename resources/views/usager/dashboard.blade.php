@extends('layouts.app')
@section('title', 'Mon espace — DGT')
@section('sidebar-color', 'bg-blue-800')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Bienvenue, ' . auth()->user()->prenom)

@section('sidebar-nav')
    <a href="{{ route('usager.dashboard') }}" class="sidebar-link {{ request()->routeIs('usager.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home w-4"></i> Tableau de bord
    </a>
    <a href="{{ route('usager.dossiers.create') }}" class="sidebar-link {{ request()->routeIs('usager.dossiers.create') ? 'active' : '' }}">
        <i class="fas fa-plus w-4"></i> Nouveau dossier
    </a>
    <div class="pt-4 pb-2 px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Mes dossiers</div>
    @foreach(auth()->user()->dossiers()->latest()->take(5)->get() as $d)
        <a href="{{ route('usager.dossiers.show', $d) }}" class="sidebar-link text-xs">
            <i class="fas fa-file w-4"></i>
            <span class="truncate">{{ $d->numero_suivi }}</span>
        </a>
    @endforeach
    <a href="{{ route('usager.profil') }}" class="sidebar-link mt-4 {{ request()->routeIs('usager.profil') ? 'active' : '' }}">
        <i class="fas fa-user w-4"></i> Mon profil
    </a>
@endsection

@section('content')
@php $dossiers = auth()->user()->dossiers()->latest()->get(); @endphp

<!-- STATS RAPIDES -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['label' => 'Total dossiers', 'val' => $dossiers->count(), 'color' => 'blue', 'icon' => 'fa-folder'],
        ['label' => 'En cours', 'val' => $dossiers->whereIn('statut', ['soumis','en_cours'])->count(), 'color' => 'yellow', 'icon' => 'fa-hourglass-half'],
        ['label' => 'Visés', 'val' => $dossiers->where('statut', 'vise')->count(), 'color' => 'green', 'icon' => 'fa-check-circle'],
        ['label' => 'Corrections requises', 'val' => $dossiers->where('statut', 'correction_demandee')->count(), 'color' => 'orange', 'icon' => 'fa-exclamation-circle'],
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

<!-- ACTIONS RAPIDES -->
@if($dossiers->where('statut', 'correction_demandee')->count() > 0)
<div class="bg-orange-50 border border-orange-200 rounded-2xl p-5 mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
            <i class="fas fa-exclamation-triangle text-orange-600"></i>
        </div>
        <div>
            <div class="font-semibold text-orange-900">Corrections requises</div>
            <div class="text-sm text-orange-700">{{ $dossiers->where('statut', 'correction_demandee')->count() }} dossier(s) nécessite(nt) votre attention.</div>
        </div>
    </div>
    <a href="#dossiers" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700 transition">Voir</a>
</div>
@endif

<!-- LISTE DES DOSSIERS -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100" id="dossiers">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-900">Mes dossiers</h2>
        <a href="{{ route('usager.dossiers.create') }}" class="px-4 py-2 bg-blue-700 text-white rounded-lg text-sm font-medium hover:bg-blue-800 transition">
            <i class="fas fa-plus mr-1"></i> Nouveau
        </a>
    </div>

    @if($dossiers->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-folder-open text-gray-400 text-2xl"></i>
            </div>
            <h3 class="font-medium text-gray-700 mb-2">Aucun dossier</h3>
            <p class="text-sm text-gray-500 mb-4">Déposez votre premier dossier pour commencer.</p>
            <a href="{{ route('usager.dossiers.create') }}" class="px-6 py-2 bg-blue-700 text-white rounded-lg text-sm font-medium">
                Déposer un dossier
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">N° Suivi</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Employé</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($dossiers as $dossier)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono font-medium text-gray-900">{{ $dossier->numero_suivi }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $dossier->prenom_employe }} {{ $dossier->nom_employe }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $dossier->type_contrat }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium badge-{{ $dossier->statut }}">
                                {{ $dossier->statut_info['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $dossier->date_soumission->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('usager.dossiers.show', $dossier) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Voir</a>
                                @if($dossier->statut === 'correction_demandee')
                                    <a href="{{ route('usager.dossiers.corriger', $dossier) }}" class="text-orange-600 hover:text-orange-800 text-xs font-medium">Corriger</a>
                                @endif
                                @if($dossier->peutEtreTelecharge())
                                    <a href="{{ route('usager.dossiers.telecharger', $dossier) }}" class="text-green-600 hover:text-green-800 text-xs font-medium">
                                        <i class="fas fa-download mr-1"></i>Télécharger
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
