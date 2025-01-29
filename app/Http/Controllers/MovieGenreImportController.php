<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\MovieGenre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MovieGenreImportController extends Controller
{
    public function importMovieGenres()
    {
        // TMDB API URL műfajok lekéréséhez
        $apiUrl = 'https://api.themoviedb.org/3/movie/';
        $apiKey = env('TMDB_API_KEY'); // API kulcs
    
        // Lekérjük az összes filmet a Movie táblából
        $movies = Movie::all();
    
        // Filmek és műfajok összekapcsolása
        foreach ($movies as $movie) {
            // API URL a film műfajainak lekéréséhez
            $movieApiUrl = "{$apiUrl}{$movie->off_movie_id}?api_key={$apiKey}";
    
            // API kérés a film műfajainak lekéréséhez
            $response = Http::get($movieApiUrl);
    
            // Ha sikeres a válasz
            if ($response->successful()) {
                $movieData = $response->json();
                echo "API response is ok: {$movie->title}\n";
                echo "Genres: : {$movie->title}\n";
                print_r($movieData['genres']);  // Kiírjuk a filmhez tartozó műfajokat
    
                // Műfajok feldolgozása
                foreach ($movieData['genres'] as $genreData) {
                    // Műfaj neve
                    $genreName = $genreData['name'];
                    echo "Genre: {$genreName}\n"; // Logoljunk ki minden műfajt, amit ellenőrzünk.
    
                    // Ellenőrizzük, hogy a műfaj szerepel-e az adatbázisban
                    $genre = Genre::where('name', $genreName)->first();
    
                    if ($genre) {
                        echo "Genre is valid {$genreName}\n";
                        try {
                            // Ha megtaláltuk a műfajt, adjuk hozzá a movie_genre táblához
                            MovieGenre::insert([
                                'movie_id' => $movie->id,
                                'genre_id' => $genre->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            echo "Added to the movie_genre table {$movie->id}, Műfaj ID: {$genre->id}\n";
                        } catch (\Exception $e) {
                            echo "Something went wrong.. " . $e->getMessage() . "\n";
                        }
                    } else {
                        echo "The genre is not in the genre table: {$genreName}\n";
                    }
                }
            } else {
                // Ha nem sikerült lekérni a műfajokat a filmhez
                echo "Could not find the genre: {$movie->title}\n";
            }
        }
    
        // Válasz visszaadása a sikeres művelethez
        return response()->json(['message' => 'Movie genres updated successfully']);
    }
}

