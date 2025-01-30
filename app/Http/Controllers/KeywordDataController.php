<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KeywordDataController extends Controller
{
        public function updateMovieKeywords()
    {
        $api_key = env('TMDB_API_KEY');
        // API kulcs

        // Lekérjük az összes filmet az adatbázisból
        $movies = Movie::all();

        foreach ($movies as $movie) {
            // Lekérjük az adott film kulcsszavait a TMDb API-ból
            $url = "https://api.themoviedb.org/3/movie/{$movie->off_movie_id}/keywords?api_key={$api_key}";
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();

                // Kiírjuk
                echo "Data for Movie ID {$movie->off_movie_id}:\n";
                print_r($data);

                // Ha vannak kulcsszavak, akkor végigmegyünk rajtuk
                if (isset($data['keywords']) && count($data['keywords']) > 0) {
                    foreach ($data['keywords'] as $keyword) {
                        // Megkeressük a kulcsszót az adatbázisból
                        $existingKeyword = Keyword::where('name', $keyword['name'])->first();

                        // Ha nincs ilyen, akkor hozzáadjuk
                        if (!$existingKeyword) {
                            Keyword::create(['name' => $keyword['name']]);
                            echo "Added keyword '{$keyword['name']}' to the keywords table.\n";
                        }
                    }
                } else {
                    echo "No keywords found for movie '{$movie->title}'.\n"; //ha van ignoráljuk
                }
            } else {
                echo "Failed to fetch keywords for movie ID {$movie->off_movie_id}.\n";//nem sikerült lekérni az api adatot
            }
        }
    }
}
