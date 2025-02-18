<?php

namespace App\Console\Commands;

use App\Models\Keyword;
use App\Models\Movie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-keywords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $api_key = env('TMDB_API_KEY','d177b1faa31eb756e208e96f34fbeb53');

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
                    $counter = 0; // Kulcsszavak számlálója

                    foreach ($data['keywords'] as $keyword) {
                        if ($counter >= 5) { // max 5 kulccszót táolunk filmenként
                            break;
                        }

                        Keyword::firstOrCreate(['name' => $keyword['name']]);// ha létezik az ab-ban visszaadja és nem lesz insert, ha nem beszúrja
                        echo "Added keyword '{$keyword['name']}' to the keywords table.\n";
                        $counter++; // növeljük a kulcsszó számlálót
                    }
                } else {
                    echo "No keywords found for movie '{$movie->title}'.\n"; //ha nincs kulcsszó, figyelmen kívül hagyjuk
                }
            } else {
                echo "Failed to fetch keywords for movie ID {$movie->off_movie_id}.\n";//nem sikerült lekérni az api adatot
            }
        }
    }
}
