<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class MovieImportController extends Controller
{
    public function importMovies()
{
    $api_key = 'd177b1faa31eb756e208e96f34fbeb53'; // API kulcs
    $base_url = 'https://api.themoviedb.org/3/discover/movie';

    // Az oldalakat végigjárjuk 7 oldalon keresztül
    for ($page = 1; $page <= 7; $page++) {
        $url = $base_url . "?api_key=$api_key&with_original_language=ko&adult=false&certification_country=US&certification.lte=PG-13&page=$page";

        // Lekérjük az adatokat az API-ból
        $response = Http::get($url);

        if ($response->successful()) {
            $movies = $response->json()['results'];

            foreach ($movies as $movie) {
                // Ha a release_date nem létezik vagy érvénytelen dátum, akkor ugrjuk át
                if (!isset($movie['release_date']) || !Carbon::hasFormat($movie['release_date'], 'Y-m-d')) {
                    echo "Movie '{$movie['title']}' skipped due to invalid or missing release date.\n";
                    continue; // Ha nincs release_date, vagy érvénytelen, akkor ugrjuk át
                }

                // A release_date stringet Carbon objektummá alakítjuk
                $releaseDate = Carbon::parse($movie['release_date'])->format('Y-m-d');

                // Ellenőrizzük, hogy az off_movie_id már létezik-e az adatbázisban
                $existingMovie = Movie::where('off_movie_id', $movie['id'])->first();
                if ($existingMovie) {
                    echo "Movie '{$movie['title']}' already exists, skipping.\n";
                    continue; // Ha már létezik a film, akkor ugrik a következőre
                }

                // Ha nincs, akkor folytatjuk a beszúrást
                $movieModel = new Movie();
                $movieModel->off_movie_id = $movie['id']; // Az URL-ből érkező film azonosító
                $movieModel->title = $movie['title'];
                $movieModel->description = $movie['overview'];
                $movieModel->release_date = $releaseDate; // Csak ha érvényes dátum van, stringként tároljuk
                $movieModel->duration_minutes = isset($movie['runtime']) ? $movie['runtime'] : null;
                $movieModel->image_url = isset($movie['poster_path']) ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'] : null;
                $movieModel->trailer_url = null; // Ha nincs trailer, null
                $movieModel->cast_url = null; // Ha nincs cast_url, null
                $movieModel->save();

                echo "Movie '{$movie['title']}' imported successfully.\n";
            }

            echo "Page $page movies processed successfully.\n";
        } else {
            echo "Error fetching page $page.\n";
        }
    }
}
}
