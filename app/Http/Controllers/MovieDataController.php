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
}

