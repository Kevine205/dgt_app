<?php

use Illuminate\Support\Facades\Schedule;

// Lancer la vérification des entretiens expirés chaque jour à 8h00
Schedule::command('dgt:traiter-entretiens')->dailyAt('08:00');
