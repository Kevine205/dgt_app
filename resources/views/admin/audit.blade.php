@extends('layouts.app')
@section('title', 'Journal d\'audit — Admin')
@section('sidebar-color', 'bg-slate-800')
@section('page-title', 'Journal d\'audit')
@section('page-subtitle', 'Traçabilité complète de toutes les actions réalisées sur la plateforme')

@section('sidebar-nav')
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home w-4"></i> Tableau de bord
    </a>
    <a href="{{ route('admin.agents.index') }}" class="sidebar-link {{ request()->routeIs('admin.agents.*') ? 'active' : '' }}">
        <i class="fas fa-users w-4"></i> Validateurs
    </a>
    <a href="{{ route('admin.statistiques') }}" class="sidebar-link {{ request()->routeIs('admin.statistiques') ? 'active' : '' }}">
        <i class="fas fa-chart-bar w-4"></i> Statistiques
    </a>
    <a href="{{ route('admin.audit') }}" class="sidebar-link active">
        <i class="fas fa-shield-alt w-4"></i> Journal d'audit
    </a>
@endsection

@section('content')

<!-- FILTRES -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-40">
            <label class="block text-xs font-medium text-gray-700 mb-1">Action</label>
            <select name="action" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-slate-500 focus:outline-none">
                <option value="">Toutes les actions</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ $action }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-40">
            <label class="block text-xs font-medium text-gray-700 mb-1">Utilisateur</label>
            <select name="user_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-slate-500 focus:outline-none">
                <option value="">Tous les utilisateurs</option>
                @foreach($agents as $agent)
                    <option value="{{ $agent->id }}" {{ request('user_id') == $agent->id ? 'selected' : '' }}>
                        {{ $agent->nom_complet }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Du</label>
            <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-slate-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Au</label>
            <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-slate-500 focus:outline-none">
        </div>
        <button type="submit" class="px-6 py-2.5 bg-slate-800 text-white rounded-xl text-sm font-medium hover:bg-slate-900 transition">
            <i class="fas fa-search mr-1"></i> Filtrer
        </button>
        @if(request()->hasAny(['action', 'user_id', 'date_debut', 'date_fin']))
        <a href="{{ route('admin.audit') }}" class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-xl text-sm hover:bg-gray-50 transition">
            Réinitialiser
        </a>
        @endif
    </form>
</div>

<!-- INFO SÉCURITÉ -->
<div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
    <i class="fas fa-shield-alt text-blue-600 text-lg"></i>
    <div class="text-sm text-blue-800">
        <strong>Principe de traçabilité :</strong> Toutes les actions réalisées sur la plateforme sont enregistrées de manière immuable avec l'identité de l'acteur, l'horodatage et l'adresse IP.
    </div>
</div>

<!-- TABLEAU -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-900">{{ $audits->total() }} entrée(s)</h2>
        <span class="text-xs text-gray-400">Mis à jour en temps réel</span>
    </div>

    @if($audits->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-gray-400 text-2xl"></i>
            </div>
            <p class="text-gray-500">Aucune entrée dans le journal.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Horodatage</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Acteur</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Adresse IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($audits as $audit)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                            {{ $audit->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($audit->user)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-slate-200 rounded-full flex items-center justify-center text-xs font-bold text-slate-700">
                                        {{ strtoupper(substr($audit->user->prenom, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $audit->user->nom_complet }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs italic">Système</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $colors = [
                                    'CONNEXION' => 'bg-blue-100 text-blue-800',
                                    'DECONNEXION' => 'bg-gray-100 text-gray-800',
                                    'VISA_APPOSE' => 'bg-green-100 text-green-800',
                                    'DOSSIER_REJETE' => 'bg-red-100 text-red-800',
                                    'DOSSIER_SOUMIS' => 'bg-sky-100 text-sky-800',
                                    'CORRECTION_DEMANDEE' => 'bg-orange-100 text-orange-800',
                                    'ENTRETIEN_DECLENCHE' => 'bg-purple-100 text-purple-800',
                                    '2FA_VERIFICATION' => 'bg-yellow-100 text-yellow-800',
                                    'AGENT_CREE' => 'bg-emerald-100 text-emerald-800',
                                    'AGENT_SUSPENDU' => 'bg-red-100 text-red-800',
                                ];
                                $color = $colors[$audit->action] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $color }}">
                                {{ $audit->action }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700 max-w-xs">{{ $audit->description }}</td>
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
