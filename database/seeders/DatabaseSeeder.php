<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer uniquement 3 rôles
        $roles = ['usager', 'validateur', 'admin'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Admin
        $admin = User::firstOrCreate(['email' => 'admin@dgt.bj'], [
            'nom' => 'ADMINISTRATEUR', 'prenom' => 'DGT',
            'password' => bcrypt('Admin@DGT2024!'), 'actif' => true,
        ]);
        $admin->syncRoles(['admin']);

        // Validateur
        $validateur = User::firstOrCreate(['email' => 'validateur@dgt.bj'], [
            'nom' => 'OGOUBI', 'prenom' => 'Jean',
            'password' => bcrypt('Valid@DGT2024!'), 'actif' => true,
        ]);
        $validateur->syncRoles(['validateur']);

        // Usager
        $usager = User::firstOrCreate(['email' => 'usager@test.bj'], [
            'nom' => 'HOUNDENOU', 'prenom' => 'Conceptia',
            'password' => bcrypt('Usager@2024!'), 'actif' => true,
        ]);
        $usager->syncRoles(['usager']);

        $this->command->info('✅ Rôles et utilisateurs créés.');
        $this->command->info('  Admin      : admin@dgt.bj       / Admin@DGT2024!');
        $this->command->info('  Validateur : validateur@dgt.bj  / Valid@DGT2024!');
        $this->command->info('  Usager     : usager@test.bj     / Usager@2024!');
    }
}
