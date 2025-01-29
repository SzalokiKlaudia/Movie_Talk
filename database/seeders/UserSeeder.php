<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //admin létrehozása

        User::create([
            'user_name' => 'adminAnna',
            'email' => 'admin@example.com',
            'name' => 'Admin Anna',
            'gender' => 'female',
            'birth_year' => 1991,
            'is_admin' => 1,
            'password' => 'admin12345',

        ]);

        //user létrehozás
        User::create([
            'user_name' => 'tesztElek',
            'email' => 'tesztelek@example.com',
            'name' => 'Teszt Elek',
            'gender' => 'male',
            'birth_year' => 1980,
            'is_admin' => 0,
            'password' => 'teszt12345',

        ]);
    }
}
