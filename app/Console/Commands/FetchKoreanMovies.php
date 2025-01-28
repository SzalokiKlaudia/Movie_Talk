<?php

namespace App\Console\Commands;

use App\Models\Movie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchKoreanMovies extends Command

{

    
    protected $signature = 'fetch:korean-movies';
    protected $description = 'Fetch Korean movies from TMDB API and store them in the database';

    public function handle()
    {
        $apiKey = 'd177b1faa31eb756e208e96f34fbeb53'; // TMDB API kulcsod
        $baseUrl = 'https://api.themoviedb.org/3/discover/movie';
        $genreUrl = 'https://api.themoviedb.org/3/movie'; // Műfajok lekérése
        $keywordUrl = 'https://api.themoviedb.org/3/movie'; // Kulcsszavak lekérése
        $movies = []; // Filmek tömbje

        // Loop through the 7 pages
        for ($page = 1; $page <= 7; $page++) {
            $response = Http::get($baseUrl, [
                'api_key' => $apiKey,
                'with_original_language' => 'ko',  // Koreai filmek
                'adult' => false,
                'certification_country' => 'US',
                'certification.lte' => 'PG-13',
                'page' => $page,
            ]);

            // Ellenőrizzük, hogy a válasz sikeres
            if ($response->successful()) {
                $data = $response->json();

                // A filmek végigiterálása és mentése az adatbázisba
                foreach ($data['results'] as $movie) {
                    // Film mentése
                    $movieModel = Movie::updateOrCreate(
                        ['tmdb_id' => $movie['id']],  // Azonosító, hogy ne legyen duplikáció
                        [
                            'title' => $movie['title'],
                            'description' => $movie['overview'],
                            'release_date' => $movie['release_date'],
                            'duration_minutes' => $movie['runtime'],
                            'image_url' => $movie['poster_path'] ? "https://image.tmdb.org/t/p/w500/{$movie['poster_path']}" : null,
                            'trailer_url' => $movie['video'] ? $movie['video'] : null, // Ha van trailer
                            'cast_url' => "https://www.themoviedb.org/movie/{$movie['id']}/cast", // Példa a cast URL-re
                        ]
                    );

                    // Filmek hozzáadása a tömbhöz, hogy lekérjük a műfajokat
                    $movies[] = $movieModel;
                }

                $this->info("Page $page fetched and stored.");
            } else {
                $this->error("Failed to fetch data for page $page.");
            }
        }
    }
}
