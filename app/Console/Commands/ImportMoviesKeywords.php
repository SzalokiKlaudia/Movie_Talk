<?php

namespace App\Console\Commands;

use App\Models\Keyword;
use App\Models\Movie;
use App\Models\MovieKeyword;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportMoviesKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:movies-keywords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import movies with keyword datas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = env('TMDB_API_KEY','d177b1faa31eb756e208e96f34fbeb53');  
        $movies = Movie::all();  // Lekérjük az összes filmet az adatbázisból
    
        foreach ($movies as $movie) {
            $url = "https://api.themoviedb.org/3/movie/{$movie->off_movie_id}/keywords?api_key={$apiKey}"; // url a kulcsszavakra
            $response = Http::get($url); // itt történik a kérés
    
            if ($response->successful()) {
                $data = $response->json();
    
                // Ha vannak kulcsszavak a válaszban
                if (isset($data['keywords']) && count($data['keywords']) > 0) {
                    foreach ($data['keywords'] as $keywordData) {
                        $keywordName = $keywordData['name'];
    
                        $keyword = Keyword::where('name', $keywordName)->first(); // ellenőrzés létezik-e a táblában
    
                        // Ha a kulcsszó létezik
                        if ($keyword) {
                            echo "Keyword '{$keywordName}' is valid.\n";
                            try {
                                MovieKeyword::insert([ // hozzárendeljük a táblához
                                    'movie_id' => $movie->id,
                                    'keyword_id' => $keyword->id,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                echo "Assigned keyword '{$keywordName}' to movie '{$movie->title}'\n";
                            } catch (\Exception $e) {
                                echo "Something went wrong: " . $e->getMessage() . "\n";  // kezeljük ha hiba van
                            }
                        } else {
                            // ha a kulcsszó nem létezik az ab-ban
                            echo "The keyword '{$keywordName}' does not exist in the keywords table.\n";
                        }
                    }
                } else {
                    // Ha nincs kulcsszó a filmhez
                    echo "No keywords found for movie '{$movie->title}'.\n";
                }
            } else {
                // Ha nem sikerült lekérni a kulcsszavakat
                echo "Failed to fetch keywords for movie '{$movie->title}'.\n";
            }
        }
        
    }
}
