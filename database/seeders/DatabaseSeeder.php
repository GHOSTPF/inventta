<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@inventta.test'],
            ['name' => 'Administrador', 'password' => 'password'],
        );

        $this->call(InventorySeeder::class);
    }
}
