<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MovieDataController extends Controller
{
    public function updateMoviesData() //itt updateljük a duration timest
    {
        $api_key = env('TMDB_API_KEY','d177b1faa31eb756e208e96f34fbeb53');
        // TMDb API kulcs

        // Lekérjük az összes filmet az adatbázisból, amiknek van movie_id
        $movies = Movie::all(); 

        foreach ($movies as $movie) {
            // Lekérjük az adott film részletes adatait a TMDb API-ból
            $url = "https://api.themoviedb.org/3/movie/{$movie->off_movie_id}?api_key={$api_key}";

           
            $response = Http::get($url);  // Lekérést változóba tároljuk

            if ($response->successful()) { //ha van response
                $data = $response->json(); //jsonba tároljuk
            
                
                echo "Data for Movie ID {$movie->off_movie_id}:\n"; //ellenőrizzük mi jött vissza
                print_r($data);
            
                // Ellenőrizzük a 'runtime' mezőt, és h van
                if (isset($data['runtime']) && $data['runtime'] !== null) {
                    // Frissítjük a film hossza adatot
                    $movie->duration_minutes = (int) $data['runtime']; // Cast-oljuk egész számra, ha nem az
                } else {
                    // Ha nincs 'runtime' adat, akkor null értékre állítjuk
                    echo "No runtime for movie '{$movie->title}'\n";

                    $movie->duration_minutes = null;
                }
        
            
                // Mentsük el a frissített adatokat
                if ($movie->save()) {
                    echo "Movie '{$movie->title}' updated successfully.\n";
                } else {
                    echo "Failed to update movie '{$movie->title}'.\n";
                }
            
             
            }
        }
    } 

        public function manuallyUpdateDuration() //muszáj volt manuálisan beállítani a megmaradt null értéket
    {
        $manualDurations = [ //manuálísan beálíltjuk a nullos duration time-t
            1149395 => 110, // Movie ID => Duration
            1137172 => 130,
            840528 => 86,
            346448 => 110,
            663116 => 75,
        
        ];

        foreach ($manualDurations as $movieId => $duration) {
            // Megkeressük a filmet az ID alapján
            $movie = Movie::where('off_movie_id', $movieId)->first();

            if ($movie) {
                // Ha megtaláljuk, frissítjük az időtartamot
                $movie->duration_minutes = $duration;
                $movie->save();

                echo "Movie '{$movie->title}' updated with duration {$duration} minutes.\n";
            } else {
                echo "Movie with ID {$movieId} not found.\n";
            }
        }

        echo "Manual updates completed.\n";
    }

    // cast url hozzáadása

    public function updateMoviesCastUrl()
    {
        $api_key = env('TMDB_API_KEY','d177b1faa31eb756e208e96f34fbeb53');
             // TMDb API kulcs

        // Lekérjük az összes filmet az adatbázisból
        $movies = Movie::all();

            foreach ($movies as $movie) {

                // A TMDb link, ami a film cast oldalára mutat
                $castUrl = "https://www.themoviedb.org/movie/{$movie->off_movie_id}/cast";

                // Frissítjük a film 'cast_url' mezőjét
                $movie->cast_url = $castUrl;

                // Elmentjük a frissített adatokat egyenként
                $movie->save();

                echo "Movie '{$movie->title}' cast URL updated successfully.\n";
            } 
        
    }
}

