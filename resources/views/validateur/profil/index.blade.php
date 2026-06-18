@extends('layouts.app')
@section('title', 'Mon profil — Validateur')
@section('sidebar-color', 'bg-amber-800')
@section('page-title', 'Mon profil')
@section('page-subtitle', 'Gérer vos identifiants et votre signature électronique')

@section('sidebar-nav')
    <a href="{{ route('validateur.dashboard') }}" class="sidebar-link"><i class="fas fa-arrow-left w-4"></i> Tableau de bord</a>
    <a href="{{ route('validateur.profil') }}" class="sidebar-link active"><i class="fas fa-user w-4"></i> Mon profil</a>
    <a href="{{ route('validateur.dossiers.index') }}" class="sidebar-link"><i class="fas fa-folder w-4"></i> Dossiers</a>
@endsection

@section('content')
<div class="grid md:grid-cols-2 gap-6">

    <!-- ══════════════════════════════════════
         BLOC 1 : IDENTIFIANTS
    ══════════════════════════════════════ -->
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">
                <div class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-amber-700 text-xs"></i>
                </div>
                Informations personnelles
            </h3>

            @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('validateur.profil.identifiants') }}" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom', $user->nom) }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Adresse e-mail <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    @error('email')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="tel" name="telephone" value="{{ old('telephone', $user->telephone) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none"
                        placeholder="+229 XX XX XX XX">
                </div>
                <button type="submit" class="w-full py-3 bg-amber-700 text-white rounded-xl font-semibold hover:bg-amber-800 transition">
                    <i class="fas fa-save mr-2"></i>Mettre à jour
                </button>
            </form>
        </div>

        <!-- MOT DE PASSE -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">
                <div class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-lock text-red-700 text-xs"></i>
                </div>
                Modifier le mot de passe
            </h3>

            @if(session('success_mdp'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success_mdp') }}
            </div>
            @endif

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-4 text-xs text-blue-800">
                <i class="fas fa-info-circle mr-1"></i>
                Le mot de passe doit contenir au moins <strong>8 caractères</strong>, des <strong>lettres</strong> et des <strong>chiffres</strong>.
            </div>

            <form method="POST" action="{{ route('validateur.profil.password') }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Mot de passe actuel <span class="text-red-500">*</span></label>
                    <input type="password" name="mot_de_passe_actuel" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:outline-none"
                        placeholder="••••••••">
                    @error('mot_de_passe_actuel')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nouveau mot de passe <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:outline-none"
                        placeholder="8 caractères min, lettres + chiffres">
                    @error('password')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Confirmer le nouveau mot de passe <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:outline-none"
                        placeholder="••••••••">
                </div>
                <button type="submit" class="w-full py-3 bg-red-700 text-white rounded-xl font-semibold hover:bg-red-800 transition">
                    <i class="fas fa-key mr-2"></i>Changer le mot de passe
                </button>
            </form>
        </div>
    </div>

    <!-- ══════════════════════════════════════
         BLOC 2 : SIGNATURE ÉLECTRONIQUE
    ══════════════════════════════════════ -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-fit">
        <h3 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
            <div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-pen-nib text-green-700 text-xs"></i>
            </div>
            Signature électronique
        </h3>
        <p class="text-sm text-gray-500 mb-5">Dessinez votre signature. Elle sera apposée sur les contrats visés.</p>

        @if(session('success_sig'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success_sig') }}
        </div>
        @endif

        <!-- Signature actuelle -->
        @if($user->signature_electronique)
        <div class="mb-5">
            <p class="text-xs font-medium text-gray-600 mb-2">Signature enregistrée :</p>
            <div class="border-2 border-green-200 rounded-xl p-3 bg-green-50 text-center">
                <img src="{{ $user->signature_electronique }}" alt="Signature" class="max-h-24 mx-auto">
            </div>
            <form method="POST" action="{{ route('validateur.profil.signature.supprimer') }}" class="mt-2">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Supprimer la signature ?')"
                    class="text-xs text-red-600 hover:text-red-800 font-medium">
                    <i class="fas fa-trash mr-1"></i>Supprimer la signature
                </button>
            </form>
        </div>
        @endif

        <!-- Canvas de dessin -->
        <div class="mb-4">
            <p class="text-xs font-medium text-gray-600 mb-2">{{ $user->signature_electronique ? 'Dessiner une nouvelle signature :' : 'Dessiner votre signature :' }}</p>
            <div class="relative border-2 border-dashed border-gray-300 rounded-xl overflow-hidden bg-gray-50 hover:border-amber-400 transition">
                <canvas id="signatureCanvas" width="400" height="180"
                    class="w-full cursor-crosshair touch-none"
                    style="background: white;"></canvas>
                <div id="hint" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <p class="text-gray-300 text-sm select-none"><i class="fas fa-pen mr-2"></i>Signez ici</p>
                </div>
            </div>
        </div>

        <!-- Boutons canvas -->
        <div class="flex gap-2 mb-5">
            <button type="button" onclick="effacerSignature()"
                class="flex-1 py-2 border border-gray-300 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                <i class="fas fa-eraser mr-1"></i>Effacer
            </button>
            <button type="button" onclick="previsualiser()"
                class="flex-1 py-2 border border-amber-300 text-amber-700 rounded-xl text-sm font-medium hover:bg-amber-50 transition">
                <i class="fas fa-eye mr-1"></i>Prévisualiser
            </button>
        </div>

        <!-- Prévisualisation -->
        <div id="preview" class="hidden mb-4 p-3 bg-gray-50 border border-gray-200 rounded-xl text-center">
            <p class="text-xs text-gray-500 mb-2">Prévisualisation :</p>
            <img id="previewImg" src="" alt="Prévisualisation" class="max-h-20 mx-auto border border-gray-200 rounded">
        </div>

        <!-- Formulaire d'enregistrement -->
        <form method="POST" action="{{ route('validateur.profil.signature') }}" id="formSignature">
            @csrf @method('PUT')
            <input type="hidden" name="signature" id="signatureData">
            <button type="button" onclick="enregistrerSignature()"
                class="w-full py-3 bg-green-700 text-white rounded-xl font-semibold hover:bg-green-800 transition">
                <i class="fas fa-save mr-2"></i>Enregistrer la signature
            </button>
        </form>

        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-xl text-xs text-yellow-800">
            <i class="fas fa-shield-alt mr-1"></i>
            Votre signature est stockée de manière sécurisée et ne peut être utilisée que par vous lors du visa des contrats.
        </div>
    </div>
</div>

<script>
// ── CANVAS SIGNATURE ──────────────────────────────────────────
const canvas  = document.getElementById('signatureCanvas');
const ctx     = canvas.getContext('2d');
const hint    = document.getElementById('hint');
let drawing   = false;
let hasDrawn  = false;

ctx.strokeStyle = '#1e3a5f';
ctx.lineWidth   = 2.5;
ctx.lineCap     = 'round';
ctx.lineJoin    = 'round';

function getPos(e) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    if (e.touches) {
        return {
            x: (e.touches[0].clientX - rect.left) * scaleX,
            y: (e.touches[0].clientY - rect.top) * scaleY,
        };
    }
    return {
        x: (e.clientX - rect.left) * scaleX,
        y: (e.clientY - rect.top) * scaleY,
    };
}

canvas.addEventListener('mousedown',  e => { drawing = true; ctx.beginPath(); const p = getPos(e); ctx.moveTo(p.x, p.y); });
canvas.addEventListener('mousemove',  e => { if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); hasDrawn = true; hint.style.display = 'none'; });
canvas.addEventListener('mouseup',    () => drawing = false);
canvas.addEventListener('mouseleave', () => drawing = false);

canvas.addEventListener('touchstart',  e => { e.preventDefault(); drawing = true; ctx.beginPath(); const p = getPos(e); ctx.moveTo(p.x, p.y); }, { passive: false });
canvas.addEventListener('touchmove',   e => { e.preventDefault(); if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); hasDrawn = true; hint.style.display = 'none'; }, { passive: false });
canvas.addEventListener('touchend',    () => drawing = false);

function effacerSignature() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    hasDrawn = false;
    hint.style.display = 'flex';
    document.getElementById('preview').classList.add('hidden');
}

function previsualiser() {
    if (!hasDrawn) { alert('Veuillez d\'abord dessiner votre signature.'); return; }
    const dataUrl = canvas.toDataURL('image/png');
    document.getElementById('previewImg').src = dataUrl;
    document.getElementById('preview').classList.remove('hidden');
}

function enregistrerSignature() {
    if (!hasDrawn) { alert('Veuillez dessiner votre signature avant de l\'enregistrer.'); return; }
    const dataUrl = canvas.toDataURL('image/png');
    document.getElementById('signatureData').value = dataUrl;
    document.getElementById('formSignature').submit();
}
</script>
@endsection
