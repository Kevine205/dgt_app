@extends('layouts.app')
@section('title', 'Mon profil — DGT')
@section('sidebar-color', 'bg-blue-800')
@section('page-title', 'Mon profil')
@section('page-subtitle', 'Gérer vos informations personnelles')

@section('sidebar-nav')
    <a href="{{ route('usager.dashboard') }}" class="sidebar-link"><i class="fas fa-arrow-left w-4"></i> Tableau de bord</a>
    <a href="{{ route('usager.profil') }}" class="sidebar-link active"><i class="fas fa-user w-4"></i> Mon profil</a>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form method="POST" action="{{ route('usager.profil.update') }}" class="space-y-5">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" name="nom" value="{{ $user->nom }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                    <input type="text" name="prenom" value="{{ $user->prenom }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse e-mail</label>
                <input type="email" value="{{ $user->email }}" disabled
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                <p class="text-xs text-gray-400 mt-1">L'adresse e-mail ne peut pas être modifiée.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                <input type="tel" name="telephone" value="{{ $user->telephone }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="+229 XX XX XX XX">
            </div>
            <button type="submit" class="w-full py-3 bg-blue-700 text-white rounded-xl font-semibold hover:bg-blue-800 transition">
                Mettre à jour mon profil
            </button>
        </form>
    </div>
</div>
@endsection