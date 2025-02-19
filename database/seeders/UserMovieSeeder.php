<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserMovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = [2, 3]; // random adatokat generálunk a 2-es, és 3-as felh-nak

        // Lekérjük az összes elérhető filmet
        $movies = Movie::inRandomOrder()->limit(10)->get(); // kiválasztunk 10 random filmet ab-ból

        foreach ($userIds as $userId) { //végigmegyünk a felh-ókon
            $selectedMovies = $movies->random(5); //kiválasztunk 5 random filmet

            foreach ($selectedMovies as $movie) { // insert
                DB::table('user_movies')->insert([
                    'user_id' => $userId,
                    'movie_id' => $movie->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
