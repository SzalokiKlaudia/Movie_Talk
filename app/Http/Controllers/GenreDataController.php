<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GenreDataController extends Controller
{
        public function updateMoviesGenre()
    {
        $api_key = env('TMDB_API_KEY');
            // API kulcs

      
        $movies = Movie::all();  // Lekérjük az összes filmet az adatbázisból

        foreach ($movies as $movie) { //végigmegyünk auz ab összes filmjén id szerint
            // Lekérjük az adott film részletes adatait a TMDb API-ból
            $url = "https://api.themoviedb.org/3/movie/{$movie->off_movie_id}?api_key={$api_key}";//behelyettesítjük az értékeket
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();//elmentjük json-ba ha van válaszunk

                // Kiírjuk ellenőrzésképp
                echo "Data for Movie ID {$movie->off_movie_id}:\n";
                print_r($data);

                // Ha vannak műfajok, akkor hozzáadjuk őket a genres táblához
                if (isset($data['genres']) && count($data['genres']) > 0) {
                    foreach ($data['genres'] as $genre) { //végigmegyünk az összes response adaton
                        try {
                            // Megpróbáljuk beszúrni a műfajt, ha már létezik, akkor duplikált hibát kapunk
                            Genre::create([
                                'name' => $genre['name'],
                            ]);
                            echo "Added new genre: {$genre['name']}\n";
                        } catch (\Illuminate\Database\QueryException $e) {
                            // Ha már létezik, akkor az adatbázis nem engedi felvinni a name uniqe érték miatt
                            echo "Genre '{$genre['name']}' already exists.\n";
                        }
                    }
                } else {
                    echo "No genres found for movie '{$movie->title}'.\n";//nem találtun kaz adatban műfajokat
                }
            }
        }
    }
}
