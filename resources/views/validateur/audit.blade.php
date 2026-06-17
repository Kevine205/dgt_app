@extends('layouts.app')
@section('title', 'Journal d\'audit — Validateur')
@section('sidebar-color', 'bg-slate-800')
@section('page-title', 'Mon Journal d\'Audit')
@section('page-subtitle', 'Historique et traçabilité des actions effectuées sur l\'espace de validation')

@section('sidebar-nav')
    {{-- On garde la structure de navigation cohérente pour le validateur --}}
    <a href="{{ route('validateur.dashboard') }}" class="sidebar-link {{ request()->routeIs('validateur.dashboard') ? 'active' : '' }}">
        <i class="fas fa-stamp w-4"></i> Tableau de bord
    </a>
    <a href="{{ route('validateur.audit') }}" class="sidebar-link {{ request()->routeIs('validateur.audit') ? 'active' : '' }}">
        <i class="fas fa-shield-alt w-4"></i> Journal d'audit
    </a>
    <div class="pt-4 pb-2 px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Retour</div>
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
        <i class="fas fa-arrow-left w-4"></i> Vue Administration
    </a>
@endsection

@section('content')
<!-- INFORMATIONS DE SÉCURITÉ -->
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6 flex gap-3 items-start">
    <i class="fas fa-info-circle text-amber-600 mt-0.5 text-sm"></i>
    <div class="text-xs text-amber-800 leading-relaxed">
        <strong>Principe de transparence :</strong> Conformément à la politique de sécurité de la DGT, toutes les actions critiques (visas, rejets, modifications de dossiers) sont enregistrées de manière immuable à des fins de conformité légale.
    </div>
</div>

<!-- TABLEAU LOGS D'AUDIT VALIDATEUR -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date & Heure</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Acteur</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Type d'action</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Adresse IP</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Description de l'action</th>
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
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-sm truncate" title="{{ $audit->details ?? $audit->description }}">
                            {{ $audit->details ?? $audit->description ?? 'Aucun détail fourni.' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                            <i class="fas fa-history text-gray-300 text-2xl mb-2 block"></i>
                            Aucune trace enregistrée pour le moment.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION (S'il y en a une configurée dans le contrôleur) -->
    @if(method_exists($audits ?? $journalAudits, 'links'))
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ ($audits ?? $journalAudits)->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection