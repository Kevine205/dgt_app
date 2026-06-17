<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pièces justificatives
        Schema::create('pieces_justificatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained()->onDelete('cascade');
            $table->string('nom_original');
            $table->string('nom_stockage');
            $table->string('type_piece'); // contrat, identite, etc.
            $table->string('mime_type');
            $table->unsignedBigInteger('taille');
            $table->boolean('conforme')->nullable();
            $table->text('motif_non_conformite')->nullable();
            $table->timestamps();
        });

        // Entretiens
        Schema::create('entretiens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained()->onDelete('cascade');
            $table->foreignId('declenche_par')->constrained('users');
            $table->text('motif_convocation');
            $table->timestamp('date_convocation');
            $table->timestamp('date_limite'); // J+15
            $table->timestamp('relance_j5')->nullable();
            $table->timestamp('relance_j10')->nullable();
            $table->enum('statut', ['programme', 'tenu', 'expire', 'annule'])->default('programme');
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('date_validation')->nullable();
            $table->text('notes_validateur')->nullable();
            $table->timestamps();
        });

        // Journal d'audit
        Schema::create('journal_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('modele')->nullable();
            $table->unsignedBigInteger('modele_id')->nullable();
            $table->json('anciennes_valeurs')->nullable();
            $table->json('nouvelles_valeurs')->nullable();
            $table->string('adresse_ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('description');
            $table->timestamps();
        });

        // Jobs pour les tâches différées (relances)
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // Cache
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pieces_justificatives');
        Schema::dropIfExists('entretiens');
        Schema::dropIfExists('journal_audits');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
