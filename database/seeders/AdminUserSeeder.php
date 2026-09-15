<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // is_admin NO es fillable (privilegio) → asignación explícita después.
        $admin = User::firstOrNew(['email' => 'admin@bellezaaurea.com']);
        $admin->name     = 'Administrador Belleza Áurea';
        $admin->password = Hash::make('aurea2026');
        $admin->is_admin = true;
        $admin->save();
    }
}
