<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
 use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@pdbagusputra.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir1@pdbagusputra.com'],
            [
                'name' => 'Kasir 1',
                'password' => bcrypt('password'),
                'role' => 'kasir',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir2@example.com'],
            [
                'name' => 'Kasir 2',
                'password' => bcrypt('password'),
                'role' => 'kasir',
            ]
        );
    }
}
