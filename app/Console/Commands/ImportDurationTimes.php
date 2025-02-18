<?php

namespace App\Console\Commands;

use App\Models\Movie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportDurationTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:duration-times';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the duration time datas';

    /**
     * Execute the console command.
     */
    public function handle() // import áljuk a filmek duration minutes mezőit
    {
        $api_key = env('TMDB_API_KEY','d177b1faa31eb756e208e96f34fbeb53');
   

        $movies = Movie::all(); 

        foreach ($movies as $movie) {
            
            $url = "https://api.themoviedb.org/3/movie/{$movie->off_movie_id}?api_key={$api_key}";

            $response = Http::get($url);  

            if ($response->successful()) { 
                $data = $response->json(); 
                
    
                if (!empty($data['runtime']) && $data['runtime'] > 0) {// ha van duration minutes a válaszban
                    $movie->duration_minutes = (int) $data['runtime']; // cast-oljuk egész számra, és mentjük
                
                } 
                else {
                    echo "No runtime for movie '{$movie->title}', generating a random duration.\n";
                    $movie->duration_minutes = rand(1, 120); // ha nincs megadva generálun kegy számot ami 0> és <=120
                }

                if ($movie->save()) {
                    echo "Movie '{$movie->title}' updated successfully.\n";
                } else {
                    echo "Failed to update movie '{$movie->title}'.\n";
                }
            }
        }
    }
}
