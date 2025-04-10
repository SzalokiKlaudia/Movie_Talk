<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void    
    {

        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1'); //a számláló 1-től fog indulni

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

        User::create([
            'user_name' => 'tesztJakab',
            'email' => 'tesztjakab@example.com',
            'name' => 'Teszt Jakab',
            'gender' => 'male',
            'birth_year' => 1980,
            'is_admin' => 0,
            'password' => 'jakab12345',

        ]);

        User::create([
            'user_name' => 'tesztMarta',
            'email' => 'tesztmarta@example.com',
            'name' => 'Teszt Marta',
            'gender' => 'female',
            'birth_year' => 1988,
            'is_admin' => 0,
            'password' => 'marta12345',

        ]);

        User::create([
            'user_name' => 'tesztKriszta',
            'email' => 'tesztkriszta@example.com',
            'name' => 'Teszt Kriszta',
            'gender' => 'female',
            'birth_year' => 1983,
            'is_admin' => 0,
            'password' => 'kriszta12345',

        ]);

        User::create([
            'user_name' => 'tesztAngela',
            'email' => 'tesztangela@example.com',
            'name' => 'Teszt Angela',
            'gender' => 'female',
            'birth_year' => 1995,
            'is_admin' => 0,
            'password' => 'angela12345',

        ]);
    }
}
