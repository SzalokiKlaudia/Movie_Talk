<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class MovieImportController extends Controller
{
    public function importMoviesWithTrailers()// move tábla feltöltése, cím, leírás, megjelenés éve, poszter kép, a többi más útvnalon
    {
        $api_key = env('TMDB_API_KEY');
                 // API kulcs
        $base_url = 'https://api.themoviedb.org/3/discover/movie'; //ez az alap

        // Az oldalakat végigjárjuk 7 oldalon keresztül
        //hozzáadjuk a leszűrt preferenciák szerint a filmeket
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

                    // Megnézzük, hogy van-e valid trailer a filmhez
                    $trailerUrl = $this->getTrailerUrlForMovie($movie['id'], $api_key);

                    // Ha nincs valid trailer, akkor átugorjuk ezt a filmet
                    if (!$trailerUrl) {
                        echo "No valid trailer for movie '{$movie['title']}', skipping.\n";
                        continue;
                    }

                    // Ha van trailer, akkor mentjük a filmet az adatbázisba
                    $movieModel = new Movie();
                    $movieModel->off_movie_id = $movie['id']; // Az URL-ből érkező film azonosító
                    $movieModel->title = $movie['title'];
                    $movieModel->description = $movie['overview'];
                    $movieModel->release_date = $releaseDate; // Csak ha érvényes dátum van, stringként tároljuk
                    $movieModel->duration_minutes = isset($movie['runtime']) ? $movie['runtime'] : null; //ez null lesz
                    $movieModel->image_url = isset($movie['poster_path']) ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'] : null;
                    $movieModel->trailer_url = $trailerUrl; // A valid trailer URL
                    $movieModel->cast_url = null; // Ha nincs cast_url, null
                    $movieModel->save();

                    echo "Movie '{$movie['title']}' imported successfully with trailer.\n";
                }

                echo "Page $page movies processed successfully.\n";
            } else {
                echo "Error fetching page $page.\n"; //ha nincs response adat
            }
        }
    }

    // Filmeknél trailer URL lekérése
    private function getTrailerUrlForMovie($movieId)
    {
        $api_key = env('TMDB_API_KEY');
        // API kulcs
        $url = "https://api.themoviedb.org/3/movie/{$movieId}/videos?api_key={$api_key}";
    
        // Lekérjük a válasz adatokat
        $response = Http::get($url);
    
        if ($response->successful()) {
            $data = $response->json();
            $trailers = $data['results'] ?? [];
    
            // Megkeressük az első trailer típusú videót,és youtubeon elérhető-e
            $officialTrailer = collect($trailers)->first(function ($trailer) {
                return $trailer['type'] === 'Trailer' && strtolower($trailer['site']) === 'youtube';
            });
    
            if ($officialTrailer) { //ha van térjünk vele vissza, mmint a youtube linkjéel h eltárolhassuk a filmekkel együtt
                return "https://www.youtube.com/watch?v={$officialTrailer['key']}";
            }
        }
    
        return null; // Ha nincs érvényes trailer, visszatérünk null-lal
    }
}
