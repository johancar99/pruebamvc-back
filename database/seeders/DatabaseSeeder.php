<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin_room_911']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);

        // Crear usuario admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@prueba.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('12345678') // Cambia la contraseña según convenga
            ]
        );

        // Asignar rol admin_room_911 al usuario admin
        $admin->assignRole($adminRole);
    }
}
