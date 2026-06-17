@extends('layouts.app')
@section('title', 'Journal d\'audit — Administration')
@section('sidebar-color', 'bg-slate-800')
@section('page-title', 'Sécurité & Audit')
@section('page-subtitle', 'Historique complet des actions effectuées par le personnel sur la plateforme DGT')

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
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
    <form action="{{ route('admin.audit') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div>
            <label for="user_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Utilisateur (Agent / Admin)</label>
            <select name="user_id" id="user_id" class="w-full bg-gray-50 border border-gray-200 text-gray-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-slate-400">
                <option value="">Tous les utilisateurs</option>
                @foreach($users ?? [] as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->nom }} {{ $u->prenom }} ({{ $u->email }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="action" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Type d'action</label>
            <select name="action" id="action" class="w-full bg-gray-50 border border-gray-200 text-gray-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-slate-400">
                <option value="">Toutes les actions</option>
                @foreach($actions ?? [] as $act)
                    <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $act)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-medium text-sm px-4 py-2.5 rounded-xl transition flex items-center justify-center gap-2 shadow-sm">
                <i class="fas fa-filter text-xs"></i> Filtrer
            </button>
            @if(request()->has('user_id') || request()->has('action'))
                <a href="{{ route('admin.audit') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm px-4 py-2.5 rounded-xl transition flex items-center justify-center">
                    Effacer
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date & Heure</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Utilisateur</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Adresse IP</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Détails de l'activité</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($audits ?? $journalAudits as $audit)
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $audit->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $audit->user->nom ?? 'Système' }} {{ $audit->user->prenom ?? '' }}</div>
                            <div class="text-xs text-gray-400">{{ $audit->user->email ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium 
                                {{ str_contains($audit->action, 'suppression') || str_contains($audit->action, 'delete') || str_contains($audit->action, 'rejete') ? 'bg-red-50 text-red-700' : '' }}
                                {{ str_contains($audit->action, 'creation') || str_contains($audit->action, 'store') || str_contains($audit->action, 'vise') ? 'bg-green-50 text-green-700' : '' }}
                                {{ str_contains($audit->action, 'modification') || str_contains($audit->action, 'update') ? 'bg-blue-50 text-blue-700' : '' }}
                                {{ !str_contains($audit->action, 'suppression') && !str_contains($audit->action, 'delete') && !str_contains($audit->action, 'creation') && !str_contains($audit->action, 'store') && !str_contains($audit->action, 'modification') && !str_contains($audit->action, 'update') && !str_contains($audit->action, 'vise') && !str_contains($audit->action, 'rejete') ? 'bg-slate-100 text-slate-700' : '' }}
                            ">
                                {{ ucfirst(str_replace('_', ' ', $audit->action)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                            {{ $audit->ip_address ?? $audit->ip ?? '127.0.0.1' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="{{ $audit->details ?? $audit->description }}">
                            {{ $audit->details ?? $audit->description ?? 'Aucun détail supplémentaire.' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                            <i class="fas fa-folder-open text-gray-300 text-2xl mb-2 block"></i>
                            Aucun enregistrement d'audit trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($audits ?? $journalAudits, 'links'))
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ ($audits ?? $journalAudits)->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection