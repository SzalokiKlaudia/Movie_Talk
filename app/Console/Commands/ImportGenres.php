<?php

namespace App\Console\Commands;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportGenres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:genres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import genre datas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $api_key = env('TMDB_API_KEY','d177b1faa31eb756e208e96f34fbeb53');
        // API kulcs

  
        $movies = Movie::all();  // lekérjük az összes filmet az adatbázisból

        foreach ($movies as $movie) { //végigmegyünk auz ab összes filmjén id szerint
            // Lekérjük az adott film részletes adatait a TMDb API-ból
            $url = "https://api.themoviedb.org/3/movie/{$movie->off_movie_id}?api_key={$api_key}";//behelyettesítjük az értékeket
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();//elmentjük json-ba ha van válaszunk

                echo "Data for Movie ID {$movie->off_movie_id}:\n";
                print_r($data);

                // Ha vannak műfajok, akkor hozzáadjuk őket a genres táblához
                if (isset($data['genres']) && count($data['genres']) > 0) {
                    foreach ($data['genres'] as $genre) { //végigmegyünk az összes response adaton
                        try {
                            Genre::create([ // ha nincs hozzáadjuk
                                'name' => $genre['name'],
                            ]);
                            echo "Added new genre: {$genre['name']}\n";
                        } catch (\Exception $e) { // ha van hibát dobunk
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
