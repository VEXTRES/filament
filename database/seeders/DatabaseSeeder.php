<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = ['admin', 'vendedor', 'deportista'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Crear usuarios y asignarles roles aleatorios
        User::factory(5000)->create()->each(function ($user) use ($roles) {
            // Asignar un rol aleatorio
            $user->assignRole($roles[array_rand($roles)]);
        });


        User::factory()->create([
            'name' => 'Uriel',
            'email' => 'uriel.ss@hotmail.com',
            'password' => '12345678',
        ]);

        User::factory()->create([
            'name' => 'Salvador Sanchez Jimenez',
            'email' => 'salvador.saji@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
    }
}
