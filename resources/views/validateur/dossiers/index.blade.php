@extends('layouts.app')
@section('title', 'Tous les dossiers — Validateur')
@section('sidebar-color', 'bg-amber-800')
@section('page-title', 'Tous les dossiers')
@section('page-subtitle', 'Liste complète des dossiers soumis à la DGT')

@section('sidebar-nav')
    <a href="{{ route('validateur.dashboard') }}" class="sidebar-link"><i class="fas fa-arrow-left w-4"></i> Tableau de bord</a>
    <a href="{{ route('validateur.dossiers.index') }}" class="sidebar-link active"><i class="fas fa-folder w-4"></i> Tous les dossiers</a>
    <a href="{{ route('validateur.audit') }}" class="sidebar-link"><i class="fas fa-list w-4"></i> Journal d'audit</a>
@endsection

@section('content')

<!-- FILTRES -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-700 mb-1">Recherche</label>
            <input type="text" name="recherche" value="{{ request('recherche') }}"
                placeholder="N° suivi, nom employé, employeur..."
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Statut</label>
            <select name="statut" class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                <option value="">Tous les statuts</option>
                @foreach(\App\Models\Dossier::STATUTS as $key => $info)
                    <option value="{{ $key }}" {{ request('statut') === $key ? 'selected' : '' }}>
                        {{ $info['label'] }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-amber-700 text-white rounded-xl text-sm font-medium hover:bg-amber-800 transition">
            <i class="fas fa-search mr-1"></i> Filtrer
        </button>
        @if(request()->hasAny(['recherche', 'statut']))
        <a href="{{ route('validateur.dossiers.index') }}" class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-xl text-sm hover:bg-gray-50 transition">
            Réinitialiser
        </a>
        @endif
    </form>
</div>

<!-- TABLEAU -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-900">
            {{ $dossiers->total() }} dossier(s) trouvé(s)
        </h2>
    </div>

    @if($dossiers->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-folder-open text-gray-400 text-2xl"></i>
            </div>
            <p class="text-gray-500">Aucun dossier trouvé.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">N° Suivi</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Employé</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Employeur</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Soumis le</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($dossiers as $dossier)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono font-medium text-sm text-gray-900">
                            {{ $dossier->numero_suivi }}
                        </td>
                        <td class="px-6 py-4 text-gray-800">
                            {{ $dossier->prenom_employe }} {{ $dossier->nom_employe }}
                        </td>
                        <td class="px-6 py-4 text-gray-600 max-w-xs truncate">
                            {{ $dossier->nom_employeur }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $dossier->type_contrat }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium badge-{{ $dossier->statut }}">
                                {{ $dossier->statut_info['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">
                            {{ $dossier->date_soumission->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('validateur.dossiers.show', $dossier) }}"
                                class="text-amber-700 hover:text-amber-900 text-xs font-medium">
                                Traiter →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($dossiers->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $dossiers->links() }}
        </div>
        @endif
    @endif
</div>

@endsection