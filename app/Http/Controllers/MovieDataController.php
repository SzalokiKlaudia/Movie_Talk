<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MovieDataController extends Controller
{
    public function updateMoviesData() //itt updateljük a duration timest
    {
        $api_key = 'd177b1faa31eb756e208e96f34fbeb53'; // TMDb API kulcs

        // Lekérjük az összes filmet az adatbázisból, amiknek van movie_id
        $movies = Movie::all(); 

        foreach ($movies as $movie) {
            // Lekérjük az adott film részletes adatait a TMDb API-ból
            $url = "https://api.themoviedb.org/3/movie/{$movie->off_movie_id}?api_key={$api_key}";

            // Lekérjük az adatokat
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();
            
                // Kiírjuk a válasz adatstruktúráját debug céljából
                echo "Data for Movie ID {$movie->off_movie_id}:\n";
                print_r($data);
            
                // Ellenőrizzük a 'runtime' mezőt
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

    public function manuallyUpdateDuration()
{
    $manualDurations = [ //manuálísan beálíltjuk a nullos duration time-t
        1149395 => 110, // Movie ID => Duration
        1137172 => 130,
        1390590 => 90,
        840528 => 86,
        719346 => 100,
        663116 => 75,
        435511 => 85,
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
        $api_key = 'd177b1faa31eb756e208e96f34fbeb53'; // TMDb API kulcs

        // Lekérjük az összes filmet az adatbázisból
        $movies = Movie::all();

        foreach ($movies as $movie) {
            // Lekérjük az adott film cast adatlapját a TMDb API-ból
            $url = "https://api.themoviedb.org/3/movie/{$movie->off_movie_id}/credits?api_key={$api_key}";

            // Lekérjük az adatokat
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();

                // A TMDb link, ami a film cast oldalára mutat
                $castUrl = "https://www.themoviedb.org/movie/{$movie->off_movie_id}/cast";

                // Frissítjük a film 'cast_url' mezőjét
                $movie->cast_url = $castUrl;

                // Elmentjük a frissített adatokat
                $movie->save();

                echo "Movie '{$movie->title}' cast URL updated successfully.\n";
            } else {
                echo "Failed to fetch cast for movie '{$movie->title}'.\n";
            }
        }
    }
}

