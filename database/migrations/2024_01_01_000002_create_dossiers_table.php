<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossiers', function (Blueprint $table) {
            $table->id();
            $table->string('numero_suivi')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('validateur_id')->nullable()->constrained('users')->nullOnDelete();

            // Informations employeur
            $table->string('nom_employeur');
            $table->string('secteur_activite')->nullable();
            $table->string('adresse_employeur')->nullable();

            // Informations employé
            $table->string('nom_employe');
            $table->string('prenom_employe');
            $table->date('date_naissance_employe')->nullable();
            $table->string('nationalite_employe')->nullable();

            // Informations contrat
            $table->string('type_contrat'); // CDD, CDI, Apprentissage...
            $table->date('date_signature');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->decimal('salaire', 12, 2)->nullable();
            $table->string('poste');

            // Workflow
            $table->enum('statut', [
                'soumis',
                'en_cours',
                'correction_demandee',
                'entretien_requis',
                'en_attente_arbitrage',
                'vise',
                'rejete'
            ])->default('soumis');

            $table->text('motif_correction')->nullable();
            $table->text('motif_rejet')->nullable();
            $table->text('notes_agent')->nullable();

            // PDF généré
            $table->string('chemin_contrat_vise')->nullable();

            $table->timestamp('date_soumission')->useCurrent();
            $table->timestamp('date_visa')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dossiers');
    }
};
