@extends('layouts.app')
@section('title', 'Nouveau dossier — DGT')
@section('sidebar-color', 'bg-blue-800')
@section('page-title', 'Soumettre un dossier')
@section('page-subtitle', 'Remplissez le formulaire pour soumettre votre contrat de travail')

@section('sidebar-nav')
    <a href="{{ route('usager.dashboard') }}" class="sidebar-link"><i class="fas fa-arrow-left w-4"></i> Retour</a>
    <a href="{{ route('usager.dossiers.create') }}" class="sidebar-link active"><i class="fas fa-plus w-4"></i> Nouveau dossier</a>
@endsection

@section('content')
<form method="POST" action="{{ route('usager.dossiers.store') }}" enctype="multipart/form-data" id="formDossier">
@csrf

<!-- ÉTAPES INDICATOR -->
<div class="flex items-center gap-2 mb-8">
    @foreach(['Employeur', 'Employé', 'Contrat', 'Documents'] as $i => $etape)
    <div class="flex items-center gap-2">
        <div class="step-indicator w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $i === 0 ? 'bg-blue-700 text-white' : 'bg-gray-200 text-gray-500' }}" data-step="{{ $i }}">
            {{ $i + 1 }}
        </div>
        <span class="text-sm {{ $i === 0 ? 'font-semibold text-blue-700' : 'text-gray-400' }}">{{ $etape }}</span>
        @if($i < 3)<div class="h-px w-8 bg-gray-200"></div>@endif
    </div>
    @endforeach
</div>

<!-- SECTION 1 : EMPLOYEUR -->
<div class="form-section" id="section-0">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">
            <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center text-blue-700 text-xs font-bold">1</div>
            Informations de l'employeur
        </h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom / Raison sociale de l'employeur <span class="text-red-500">*</span></label>
                <input type="text" name="nom_employeur" value="{{ old('nom_employeur') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Ex: SOCIÉTÉ BÉNINOISE DE CONSTRUCTION SARL">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Secteur d'activité</label>
                <select name="secteur_activite" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Sélectionner --</option>
                    @foreach(['Agriculture','Industrie','Commerce','Services','BTP','Transport','Santé','Éducation','Informatique','Finance','Hôtellerie','Autre'] as $s)
                        <option value="{{ $s }}" {{ old('secteur_activite') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse de l'employeur</label>
                <input type="text" name="adresse_employeur" value="{{ old('adresse_employeur') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Cotonou, Bénin">
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <button type="button" onclick="nextSection(0)" class="px-6 py-2 bg-blue-700 text-white rounded-xl text-sm font-medium hover:bg-blue-800 transition">
                Suivant <i class="fas fa-arrow-right ml-1"></i>
            </button>
        </div>
    </div>
</div>

<!-- SECTION 2 : EMPLOYÉ -->
<div class="form-section hidden" id="section-1">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">
            <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center text-blue-700 text-xs font-bold">2</div>
            Informations de l'employé
        </h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="nom_employe" value="{{ old('nom_employe') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="HOUNSOU">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prénom <span class="text-red-500">*</span></label>
                <input type="text" name="prenom_employe" value="{{ old('prenom_employe') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Jean">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
                <input type="date" name="date_naissance_employe" value="{{ old('date_naissance_employe') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nationalité</label>
                <input type="text" name="nationalite_employe" value="{{ old('nationalite_employe', 'Béninoise') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
        <div class="flex justify-between mt-4">
            <button type="button" onclick="prevSection(1)" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-1"></i> Précédent
            </button>
            <button type="button" onclick="nextSection(1)" class="px-6 py-2 bg-blue-700 text-white rounded-xl text-sm font-medium hover:bg-blue-800 transition">
                Suivant <i class="fas fa-arrow-right ml-1"></i>
            </button>
        </div>
    </div>
</div>

<!-- SECTION 3 : CONTRAT -->
<div class="form-section hidden" id="section-2">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">
            <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center text-blue-700 text-xs font-bold">3</div>
            Informations du contrat
        </h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type de contrat <span class="text-red-500">*</span></label>
                <select name="type_contrat" required class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">-- Sélectionner --</option>
                    @foreach(['CDI','CDD','Apprentissage','Stage','Interim','Saisonnier'] as $t)
                        <option value="{{ $t }}" {{ old('type_contrat') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Poste occupé <span class="text-red-500">*</span></label>
                <input type="text" name="poste" value="{{ old('poste') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Ex: Comptable, Maçon, Secrétaire...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de signature <span class="text-red-500">*</span></label>
                <input type="date" name="date_signature" value="{{ old('date_signature') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de début <span class="text-red-500">*</span></label>
                <input type="date" name="date_debut" value="{{ old('date_debut') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin <span class="text-xs text-gray-400">(CDD uniquement)</span></label>
                <input type="date" name="date_fin" value="{{ old('date_fin') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Salaire mensuel (FCFA)</label>
                <input type="number" name="salaire" value="{{ old('salaire') }}" min="0" step="1000"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Ex: 75000">
            </div>
        </div>
        <div class="flex justify-between mt-4">
            <button type="button" onclick="prevSection(2)" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-1"></i> Précédent
            </button>
            <button type="button" onclick="nextSection(2)" class="px-6 py-2 bg-blue-700 text-white rounded-xl text-sm font-medium hover:bg-blue-800 transition">
                Suivant <i class="fas fa-arrow-right ml-1"></i>
            </button>
        </div>
    </div>
</div>

<!-- SECTION 4 : DOCUMENTS -->
<div class="form-section hidden" id="section-3">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">
            <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center text-blue-700 text-xs font-bold">4</div>
            Pièces justificatives
        </h3>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-5 text-sm text-yellow-800">
            <i class="fas fa-info-circle mr-2"></i>
            Formats acceptés : <strong>PDF, JPG, PNG</strong> — Taille max par fichier : <strong>5 Mo</strong>
        </div>
        <div id="pieces-container" class="space-y-3">
            <div class="piece-item grid grid-cols-3 gap-3 items-center">
                <select name="types_pieces[]" class="px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="contrat">Contrat de travail</option>
                    <option value="identite">Pièce d'identité</option>
                    <option value="diplome">Diplôme / Attestation</option>
                    <option value="autre">Autre</option>
                </select>
                <input type="file" name="pieces[]" accept=".pdf,.jpg,.jpeg,.png" required
                    class="col-span-2 px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>
        <button type="button" onclick="ajouterPiece()" class="mt-3 flex items-center gap-2 text-sm text-blue-700 hover:text-blue-900 font-medium">
            <i class="fas fa-plus-circle"></i> Ajouter une pièce
        </button>
        <div class="flex justify-between mt-6">
            <button type="button" onclick="prevSection(3)" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-1"></i> Précédent
            </button>
            <button type="submit" class="px-8 py-3 bg-green-700 text-white rounded-xl font-semibold hover:bg-green-800 transition">
                <i class="fas fa-paper-plane mr-2"></i>Soumettre le dossier
            </button>
        </div>
    </div>
</div>

</form>

<script>
function nextSection(current) {
    document.getElementById('section-' + current).classList.add('hidden');
    document.getElementById('section-' + (current + 1)).classList.remove('hidden');
    updateSteps(current + 1);
    window.scrollTo(0, 0);
}
function prevSection(current) {
    document.getElementById('section-' + current).classList.add('hidden');
    document.getElementById('section-' + (current - 1)).classList.remove('hidden');
    updateSteps(current - 1);
    window.scrollTo(0, 0);
}
function updateSteps(active) {
    document.querySelectorAll('.step-indicator').forEach((el, i) => {
        if (i <= active) {
            el.classList.remove('bg-gray-200', 'text-gray-500');
            el.classList.add('bg-blue-700', 'text-white');
        } else {
            el.classList.remove('bg-blue-700', 'text-white');
            el.classList.add('bg-gray-200', 'text-gray-500');
        }
    });
}
function ajouterPiece() {
    const container = document.getElementById('pieces-container');
    const div = document.createElement('div');
    div.className = 'piece-item grid grid-cols-3 gap-3 items-center';
    div.innerHTML = `
        <select name="types_pieces[]" class="px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="contrat">Contrat de travail</option>
            <option value="identite">Pièce d'identité</option>
            <option value="diplome">Diplôme / Attestation</option>
            <option value="autre">Autre</option>
        </select>
        <div class="col-span-2 flex gap-2">
            <input type="file" name="pieces[]" accept=".pdf,.jpg,.jpeg,.png"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <button type="button" onclick="this.closest('.piece-item').remove()"
                class="px-3 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition text-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>`;
    container.appendChild(div);
}
</script>
@endsection
