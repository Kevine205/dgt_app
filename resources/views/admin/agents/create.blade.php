{{-- admin/agents/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Nouvel agent — DGT')
@section('sidebar-color', 'bg-slate-800')
@section('page-title', 'Créer un agent DGT')
@section('page-subtitle', 'Ajouter un nouveau membre de l\'équipe')

@section('sidebar-nav')
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link"><i class="fas fa-arrow-left w-4"></i> Tableau de bord</a>
    <a href="{{ route('admin.agents.index') }}" class="sidebar-link"><i class="fas fa-users w-4"></i> Agents</a>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form method="POST" action="{{ route('admin.agents.store') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-slate-500 focus:outline-none"
                        placeholder="AGOSSOU">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prénom <span class="text-red-500">*</span></label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-slate-500 focus:outline-none"
                        placeholder="Béatrice">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse e-mail <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-slate-500 focus:outline-none"
                    placeholder="b.agossou@dgt.bj">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                <input type="tel" name="telephone" value="{{ old('telephone') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-slate-500 focus:outline-none"
                    placeholder="+229 XX XX XX XX">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rôle <span class="text-red-500">*</span></label>
                <select name="role" required class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-slate-500 focus:outline-none">
                    <option value="">-- Sélectionner un rôle --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">L'agent devra configurer le 2FA à sa première connexion.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe temporaire <span class="text-red-500">*</span></label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-slate-500 focus:outline-none"
                    placeholder="8 caractères min, lettres + chiffres">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-slate-500 focus:outline-none">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-3 bg-slate-800 text-white rounded-xl font-semibold hover:bg-slate-900 transition">
                    <i class="fas fa-user-plus mr-2"></i>Créer le compte
                </button>
                <a href="{{ route('admin.agents.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition text-center">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
