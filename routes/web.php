<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Usager\DossierController;
use App\Http\Controllers\Usager\TableauBordUsagerController;
use App\Http\Controllers\Validateur\ValidateurController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\FichierController;

// ===================== ACCUEIL =====================
Route::get('/', fn() => view('welcome'))->name('home');

// ===================== AUTH =====================
Route::middleware('guest')->group(function () {
    Route::get('/connexion',             [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion',            [AuthController::class, 'login'])->name('login.post');
    Route::get('/inscription',           [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription',          [AuthController::class, 'register'])->name('register.post');
    Route::get('/mot-de-passe-oublie',   [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/mot-de-passe-oublie',  [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reinitialiser/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reinitialiser',        [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
    Route::get('/2fa/verifier',   [TwoFactorController::class, 'show'])->name('2fa.show');
    Route::post('/2fa/verifier',  [TwoFactorController::class, 'verify'])->name('2fa.verify');
    Route::get('/2fa/configurer', [TwoFactorController::class, 'setup'])->name('2fa.setup');
    Route::post('/2fa/activer',   [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::post('/2fa/desactiver',[TwoFactorController::class, 'disable'])->name('2fa.disable');
});

// ===================== USAGER =====================
Route::middleware(['auth', 'role:usager', 'user.actif'])->prefix('usager')->name('usager.')->group(function () {
    Route::get('/tableau-de-bord',                       [TableauBordUsagerController::class, 'index'])->name('dashboard');
    Route::get('/dossiers/nouveau',                      [DossierController::class, 'create'])->name('dossiers.create');
    Route::post('/dossiers',                             [DossierController::class, 'store'])->name('dossiers.store');
    Route::get('/dossiers/{dossier}',                    [DossierController::class, 'show'])->name('dossiers.show');
    Route::get('/dossiers/{dossier}/corriger',           [DossierController::class, 'corriger'])->name('dossiers.corriger');
    Route::post('/dossiers/{dossier}/corriger',          [DossierController::class, 'soumettreCorrection'])->name('dossiers.correction.submit');
    Route::get('/dossiers/{dossier}/telecharger',        [DossierController::class, 'telecharger'])->name('dossiers.telecharger');
    Route::get('/profil',                                [TableauBordUsagerController::class, 'profil'])->name('profil');
    Route::put('/profil',                                [TableauBordUsagerController::class, 'updateProfil'])->name('profil.update');
});

// ===================== VALIDATEUR =====================
Route::middleware(['auth', 'role:validateur|admin', 'user.actif', '2fa'])->prefix('validateur')->name('validateur.')->group(function () {
    Route::get('/tableau-de-bord',                        [ValidateurController::class, 'dashboard'])->name('dashboard');
    Route::get('/dossiers',                               [ValidateurController::class, 'index'])->name('dossiers.index');
    Route::get('/dossiers/{dossier}',                     [ValidateurController::class, 'show'])->name('dossiers.show');
    // Actions ex-agent
    Route::post('/dossiers/{dossier}/prendre-en-charge',  [ValidateurController::class, 'prendreEnCharge'])->name('dossiers.prendre');
    Route::post('/dossiers/{dossier}/demander-correction', [ValidateurController::class, 'demanderCorrection'])->name('dossiers.correction');
    Route::post('/dossiers/{dossier}/entretien',           [ValidateurController::class, 'declencherEntretien'])->name('dossiers.entretien');
    // Actions visa
    Route::post('/dossiers/{dossier}/visa',               [ValidateurController::class, 'apposerVisa'])->name('dossiers.visa');
    Route::post('/dossiers/{dossier}/rejeter',            [ValidateurController::class, 'rejeter'])->name('dossiers.rejeter');
    Route::post('/dossiers/{dossier}/valider-entretien',  [ValidateurController::class, 'validerEntretien'])->name('dossiers.valider-entretien');
    Route::post('/dossiers/{dossier}/arbitrage',          [ValidateurController::class, 'arbitrage'])->name('dossiers.arbitrage');
    Route::get('/journal-audit',                          [ValidateurController::class, 'journalAudit'])->name('audit');
    Route::get('/fichier/{piece}',                        [FichierController::class, 'telechargerPiece'])->name('fichier.piece');
});

// ===================== ADMIN =====================
Route::middleware(['auth', 'role:admin', 'user.actif', '2fa'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/tableau-de-bord',           [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/validateurs',               [AdminController::class, 'agents'])->name('agents.index');
    Route::get('/validateurs/nouveau',       [AdminController::class, 'creerAgent'])->name('agents.create');
    Route::post('/validateurs',              [AdminController::class, 'enregistrerAgent'])->name('agents.store');
    Route::get('/validateurs/{user}/modifier',[AdminController::class, 'modifierAgent'])->name('agents.edit');
    Route::put('/validateurs/{user}',        [AdminController::class, 'mettreAJourAgent'])->name('agents.update');
    Route::post('/validateurs/{user}/suspendre',[AdminController::class, 'suspendreAgent'])->name('agents.suspendre');
    Route::post('/validateurs/{user}/reactiver',[AdminController::class, 'reactiverAgent'])->name('agents.reactiver');
    Route::post('/validateurs/{user}/reset-2fa',[AdminController::class, 'reset2fa'])->name('agents.reset2fa');
    Route::get('/journal-audit',             [AdminController::class, 'journalAudit'])->name('audit');
    Route::get('/statistiques',              [AdminController::class, 'statistiques'])->name('statistiques');
    Route::get('/statistiques/export',       [AdminController::class, 'exporterStats'])->name('statistiques.export');
});

// ===================== REDIRECTION APRÈS CONNEXION =====================
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('admin'))      return redirect()->route('admin.dashboard');
    if ($user->hasRole('validateur')) return redirect()->route('validateur.dashboard');
    return redirect()->route('usager.dashboard');
})->middleware('auth')->name('dashboard');

// ===================== PROFIL VALIDATEUR =====================
Route::middleware(['auth', 'role:validateur|admin', 'user.actif', '2fa'])->prefix('validateur')->name('validateur.')->group(function () {
    Route::get('/profil',                   [\App\Http\Controllers\Validateur\ProfilValidateurController::class, 'index'])->name('profil');
    Route::put('/profil/identifiants',      [\App\Http\Controllers\Validateur\ProfilValidateurController::class, 'updateIdentifiants'])->name('profil.identifiants');
    Route::put('/profil/password',          [\App\Http\Controllers\Validateur\ProfilValidateurController::class, 'updateMotDePasse'])->name('profil.password');
    Route::put('/profil/signature',         [\App\Http\Controllers\Validateur\ProfilValidateurController::class, 'updateSignature'])->name('profil.signature');
    Route::delete('/profil/signature',      [\App\Http\Controllers\Validateur\ProfilValidateurController::class, 'supprimerSignature'])->name('profil.signature.supprimer');
});
