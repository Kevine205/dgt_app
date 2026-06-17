{{-- admin/agents/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Gestion agents — DGT')
@section('sidebar-color', 'bg-slate-800')
@section('page-title', 'Agents DGT')
@section('page-subtitle', 'Gestion des habilitations et des comptes agents')

@section('sidebar-nav')
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link"><i class="fas fa-arrow-left w-4"></i> Tableau de bord</a>
    <a href="{{ route('admin.agents.index') }}" class="sidebar-link active"><i class="fas fa-users w-4"></i> Agents</a>
    <a href="{{ route('admin.agents.create') }}" class="sidebar-link"><i class="fas fa-user-plus w-4"></i> Nouvel agent</a>
@endsection

@section('content')
<!-- FILTRES -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
    <form method="GET" class="flex gap-4 items-end">
        <div class="flex-1">
            <label class="block text-xs font-medium text-gray-700 mb-1">Recherche</label>
            <input type="text" name="recherche" value="{{ request('recherche') }}" placeholder="Nom, prénom, email..."
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-slate-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Rôle</label>
            <select name="role" class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-slate-500 focus:outline-none">
                <option value="">Tous</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-slate-800 text-white rounded-xl text-sm font-medium hover:bg-slate-900 transition">
            Filtrer
        </button>
        <a href="{{ route('admin.agents.create') }}" class="px-6 py-2.5 bg-green-700 text-white rounded-xl text-sm font-medium hover:bg-green-800 transition">
            <i class="fas fa-plus mr-1"></i> Nouvel agent
        </a>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Agent</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Rôle</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">2FA</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($agents as $agent)
            <tr class="hover:bg-gray-50 {{ !$agent->actif ? 'opacity-60' : '' }}">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center text-xs font-bold text-slate-700">
                            {{ strtoupper(substr($agent->prenom, 0, 1)) }}{{ strtoupper(substr($agent->nom, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $agent->nom_complet }}</div>
                            <div class="text-xs text-gray-400">{{ $agent->telephone ?? '—' }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $agent->email }}</td>
                <td class="px-6 py-4">
                    @foreach($agent->getRoleNames() as $role)
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $role === 'admin' ? 'bg-red-100 text-red-800' : ($role === 'validateur' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                            {{ ucfirst($role) }}
                        </span>
                    @endforeach
                </td>
                <td class="px-6 py-4">
                    @if($agent->google2fa_enabled)
                        <span class="flex items-center gap-1 text-green-700 text-xs font-medium">
                            <i class="fas fa-shield-alt"></i> Activé
                        </span>
                    @else
                        <span class="flex items-center gap-1 text-red-600 text-xs font-medium">
                            <i class="fas fa-exclamation-triangle"></i> Non configuré
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $agent->actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $agent->actif ? 'Actif' : 'Suspendu' }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.agents.edit', $agent) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Modifier</a>

                        @if($agent->actif)
                            <form method="POST" action="{{ route('admin.agents.suspendre', $agent) }}" onsubmit="return confirm('Suspendre ce compte ?')">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Suspendre</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.agents.reactiver', $agent) }}">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-medium">Réactiver</button>
                            </form>
                        @endif

                        @if($agent->google2fa_enabled)
                            <form method="POST" action="{{ route('admin.agents.reset2fa', $agent) }}" onsubmit="return confirm('Réinitialiser le 2FA de cet agent ?')">
                                @csrf
                                <button type="submit" class="text-orange-600 hover:text-orange-800 text-xs font-medium">Reset 2FA</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">
        {{ $agents->links() }}
    </div>
</div>
@endsection
