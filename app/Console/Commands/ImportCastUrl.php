<?php

namespace App\Console\Commands;

use App\Models\Movie;
use Illuminate\Console\Command;

class ImportCastUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cast-url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import cast urls';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $api_key = env('TMDB_API_KEY','d177b1faa31eb756e208e96f34fbeb53');
             // TMDb API kulcs

        // Lekérjük az összes filmet az adatbázisból
        $movies = Movie::all();

            foreach ($movies as $movie) {

                $castUrl = "https://www.themoviedb.org/movie/{$movie->off_movie_id}/cast"; //a film cast oldalára mutató link

                $movie->cast_url = $castUrl; // frissítjük az adatbázisunkban

                $movie->save();

                echo "Movie '{$movie->title}' cast URL updated successfully.\n";
            } 
    }
}
