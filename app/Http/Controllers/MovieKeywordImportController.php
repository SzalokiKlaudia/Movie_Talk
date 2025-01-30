<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use App\Models\Movie;
use App\Models\MovieKeyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MovieKeywordImportController extends Controller
{
    public function importMovieKeywords() //kulcsszavak lekérése feltöltése táblába
    {
        // Lekérjük az összes filmet az adatbázisból
        $movies = Movie::all();

        // Minden filmhez
        foreach ($movies as $movie) {
            echo "Fetching keywords for movie: {$movie->title}\n";

            // TMDB API URL a kulcsszavak lekéréséhez
            $apiUrl = "https://api.themoviedb.org/3/movie/{$movie->off_movie_id}/keywords";
            $apiKey = env('TMDB_API_KEY');

            // API kérés a kulcsszavak lekéréséhez
            $response = Http::get($apiUrl, [
                'api_key' => $apiKey,
            ]);

            if ($response->successful()) {
                $movieData = $response->json();

                // Ellenőrizzük, hogy létezik-e 'keywords' kulcs a válaszban
                if (isset($movieData['keywords']) && is_array($movieData['keywords'])) {
                    $keywordsData = $movieData['keywords'];

                    // Ha vannak kulcsszavak
                    foreach ($keywordsData as $keywordData) {
                        $keywordName = $keywordData['name'];

                        // Ellenőrizzük, hogy a kulcsszó szerepel-e a keywords táblában
                        $keyword = Keyword::where('name', $keywordName)->first();

                        if ($keyword) {
                            // Ha a kulcsszó létezik, hozzárendeljük a filmhez a movie_keywords táblában
                            MovieKeyword::insert([
                                'movie_id' => $movie->id,
                                'keyword_id' => $keyword->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            echo "Assigned keyword '{$keywordName}' to movie '{$movie->title}'\n";
                        } else {
                            echo "Keyword '{$keywordName}' does not exist in the keywords table.\n";
                        }
                    }
                } else { //ha nincs a response-ban keywords
                    echo "No keywords found for movie: {$movie->title}\n";
                }

            } else { //ha nem sikerült a fetchelés
                echo "Failed to fetch keywords for movie: {$movie->title}\n";
            }
        }

        echo "Movie keywords import completed.\n";
    }
}


