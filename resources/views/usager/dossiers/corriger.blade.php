@extends('layouts.app')
@section('title', 'Corriger le dossier')
@section('sidebar-color', 'bg-blue-800')
@section('page-title', 'Corriger mon dossier')
@section('page-subtitle', $dossier->numero_suivi)

@section('sidebar-nav')
    <a href="{{ route('usager.dashboard') }}" class="sidebar-link"><i class="fas fa-arrow-left w-4"></i> Tableau de bord</a>
    <a href="{{ route('usager.dossiers.show', $dossier) }}" class="sidebar-link"><i class="fas fa-eye w-4"></i> Voir le dossier</a>
@endsection

@section('content')

<!-- MOTIF DE CORRECTION -->
<div class="bg-orange-50 border border-orange-200 rounded-2xl p-6 mb-6">
    <div class="flex items-start gap-3">
        <i class="fas fa-exclamation-triangle text-orange-600 mt-0.5 text-lg"></i>
        <div>
            <div class="font-semibold text-orange-900 mb-1">Corrections demandées par l'agent</div>
            <p class="text-orange-800 text-sm">{{ $dossier->motif_correction }}</p>
        </div>
    </div>
</div>

<!-- FORMULAIRE DE CORRECTION -->
<form method="POST" action="{{ route('usager.dossiers.correction.submit', $dossier) }}" enctype="multipart/form-data">
@csrf

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h3 class="font-semibold text-gray-900 mb-5">Remplacer les pièces non conformes</h3>

    @if($dossier->pieces->isEmpty())
        <p class="text-gray-500 text-sm">Aucune pièce jointe à corriger.</p>
    @else
        <div class="space-y-4">
            @foreach($dossier->pieces as $piece)
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas {{ str_contains($piece->mime_type, 'pdf') ? 'fa-file-pdf text-red-600' : 'fa-file-image text-blue-600' }} text-sm"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $piece->nom_original }}</div>
                        <div class="text-xs text-gray-500">{{ ucfirst($piece->type_piece) }} — {{ $piece->taille_formatee }}</div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Remplacer par un nouveau fichier (optionnel)</label>
                    <input type="hidden" name="pieces_ids[]" value="{{ $piece->id }}">
                    <input type="file" name="nouvelles_pieces[]" accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <p class="text-xs text-gray-400 mt-1">Formats acceptés : PDF, JPG, PNG — Max 5 Mo</p>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <div class="flex gap-3 mt-6">
        <button type="submit" class="flex-1 py-3 bg-blue-700 text-white rounded-xl font-semibold hover:bg-blue-800 transition">
            <i class="fas fa-paper-plane mr-2"></i>Soumettre les corrections
        </button>
        <a href="{{ route('usager.dossiers.show', $dossier) }}"
            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition text-center">
            Annuler
        </a>
    </div>
</div>

</form>
@endsection