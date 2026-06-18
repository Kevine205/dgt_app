@extends('layouts.app')
@section('title', 'Journal d\'audit — Validateur')
@section('sidebar-color', 'bg-amber-800')
@section('page-title', 'Journal d\'audit')
@section('page-subtitle', 'Historique de vos actions de validation')

@section('sidebar-nav')
    <a href="{{ route('validateur.dashboard') }}" class="sidebar-link"><i class="fas fa-arrow-left w-4"></i> Tableau de bord</a>
    <a href="{{ route('validateur.dossiers.index') }}" class="sidebar-link"><i class="fas fa-folder w-4"></i> Dossiers</a>
    <a href="{{ route('validateur.audit') }}" class="sidebar-link active"><i class="fas fa-list w-4"></i> Journal d'audit</a>
    <a href="{{ route('validateur.profil') }}" class="sidebar-link"><i class="fas fa-user w-4"></i> Mon profil</a>
@endsection

@section('content')

<!-- INFO -->
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
    <i class="fas fa-info-circle text-amber-600 text-lg"></i>
    <div class="text-sm text-amber-800">
        <strong>Transparence et responsabilité :</strong> Toutes vos actions sont enregistrées horodatées. Ce journal garantit l'intégrité des décisions prises sur chaque dossier.
    </div>
</div>

<!-- FILTRES -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Action</label>
            <select name="action" class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                <option value="">Toutes</option>
                @foreach(['VISA_APPOSE','DOSSIER_REJETE','CORRECTION_DEMANDEE','ENTRETIEN_DECLENCHE','ENTRETIEN_VALIDE','ARBITRAGE_PROLONGATION','ARBITRAGE_ANNULATION','ARBITRAGE_REJET','DOSSIER_PRIS_EN_CHARGE'] as $action)
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ $action }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Date</label>
            <input type="date" name="date" value="{{ request('date') }}"
                class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>
        <button type="submit" class="px-6 py-2.5 bg-amber-700 text-white rounded-xl text-sm font-medium hover:bg-amber-800 transition">
            <i class="fas fa-search mr-1"></i> Filtrer
        </button>
        @if(request()->hasAny(['action', 'date']))
        <a href="{{ route('validateur.audit') }}" class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-xl text-sm hover:bg-gray-50 transition">
            Réinitialiser
        </a>
        @endif
    </form>
</div>

<!-- TABLEAU -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100">
        <h2 class="font-semibold text-gray-900">{{ $audits->total() }} entrée(s)</h2>
    </div>

    @if($audits->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-list text-gray-400 text-2xl"></i>
            </div>
            <p class="text-gray-500">Aucune entrée dans le journal.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Horodatage</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($audits as $audit)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                            {{ $audit->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $colors = [
                                    'VISA_APPOSE' => 'bg-green-100 text-green-800',
                                    'DOSSIER_REJETE' => 'bg-red-100 text-red-800',
                                    'CORRECTION_DEMANDEE' => 'bg-orange-100 text-orange-800',
                                    'ENTRETIEN_DECLENCHE' => 'bg-purple-100 text-purple-800',
                                    'ENTRETIEN_VALIDE' => 'bg-blue-100 text-blue-800',
                                    'DOSSIER_PRIS_EN_CHARGE' => 'bg-sky-100 text-sky-800',
                                ];
                                $color = $colors[$audit->action] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $color }}">
                                {{ $audit->action }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $audit->description }}</td>
                        <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $audit->adresse_ip ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $audits->links() }}
        </div>
    @endif
</div>
@endsection
