<?php

namespace App\Console\Commands;

use App\Models\Movie;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ImportMoviesWithTrailers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:movies-with-trailers';// ide írjuk az artisan parancs nevét

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import movies with trailers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $api_key = env('TMDB_API_KEY', 'd177b1faa31eb756e208e96f34fbeb53');
        $base_url = 'https://api.themoviedb.org/3/discover/movie'; //ehhez fűzzük hozzá az url-t amin ciklussal végig iterálunk

        $excludedMovieIds = [59421, 215743, 384641, 435511, 719346, 510589, 663116];


        for ($page = 1; $page <= 8; $page++) {
            $url = $base_url . "?api_key=$api_key&with_original_language=ko&adult=false&certification_country=US&certification.lte=PG-13&page=$page";

        $response = Http::get($url);

        if (!$response->successful()) {
            echo "Error fetching page $page: " . $response->status() . " - " . $response->body() . "\n";
            continue;
        }

        $movies = $response->json()['results'];// elmentjük a választ

        foreach ($movies as $movie) { // végig iterálunk rajta, ha nincs kitöltve midnen adat átugorjuk, empty()üres vagy null érték

            if (in_array($movie['id'], $excludedMovieIds)) {// kihagyunk pár filmet
                echo "Movie with ID {$movie['id']} skipped.\n";
                continue;
            }

            if (empty($movie['title']) || empty($movie['overview']) || empty($movie['poster_path']) || empty($movie['release_date'])) {
                echo "Movie skipped due to missing data: " . json_encode($movie) . "\n";
                continue;
            }

            // ha a release_date nem érvényes formátumú, ugrunk
            if (!Carbon::hasFormat($movie['release_date'], 'Y-m-d')) {
                echo "Movie '{$movie['title']}' skipped due to invalid release date.\n";
                continue;
            }

            $releaseDate = Carbon::parse($movie['release_date'])->format('Y-m-d');// átalakítjuk a stringet dátummá

            // Előzetes URL lekérése
            $trailerUrl = $this->getTrailerUrlForMovie($movie['id']);

            // Ha nincs valid trailer, akkor átugorjuk
            if (!$trailerUrl) {
                echo "No valid trailer for movie '{$movie['title']}', skipping.\n";
                continue;
            }

            try {
                // Film mentése az adatbázisba
                Movie::create([
                    'off_movie_id' => $movie['id'],// uniqe érték az ab-ban
                    'title' => $movie['title'],
                    'description' => $movie['overview'],
                    'release_date' => $releaseDate,
                    'image_url' => 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'],
                    'trailer_url' => $trailerUrl,
                ]);

                echo "Movie '{$movie['title']}' imported successfully with trailer.\n";
            } catch (\Exception $e) {
                echo "Failed to save '{$movie['title']}'\n";
            }
        }

        echo "Page $page movies processed successfully.\n";
    }
    }

      // Filmeknél trailer URL lekérése, itt több videó adat van a filmről, kiválasztjuk a legrelevánsabbat
      private function getTrailerUrlForMovie($movieId)
      {
        $api_key = env('TMDB_API_KEY','d177b1faa31eb756e208e96f34fbeb53');
        // API kulcs
        $url = "https://api.themoviedb.org/3/movie/{$movieId}/videos?api_key={$api_key}";
    
        // Lekérjük a válasz adatokat
        $response = Http::get($url);
    
        if ($response->successful()) {
            $data = $response->json();
            $trailers = $data['results'] ?? [];
    
            // Megkeressük az összes trailer típusú videót, ami YouTube-on elérhető
            $officialTrailer = collect($trailers)->first(function ($trailer) {
                return $trailer['type'] === 'Trailer' && strtolower($trailer['site']) === 'youtube';
            });
    
            // Ha találunk érvényes youtube trailer-t, akkor visszatérünk a linkkel
            if ($officialTrailer) {
                return "https://www.youtube.com/watch?v={$officialTrailer['key']}";
            }
        }
    
        return null; // Ha nincs érvényes trailer, visszatérünk null-lal
      }
}
