<?php

namespace App\Console\Commands;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\MovieGenre;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportMoviesGenres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-movies-genres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import movies genre data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
          $apiUrl = 'https://api.themoviedb.org/3/movie/'; 
          $apiKey = env('TMDB_API_KEY','d177b1faa31eb756e208e96f34fbeb53'); 
      
          $movies = Movie::all(); // lekérem az összes filmem
      
          foreach ($movies as $movie) {
              $movieApiUrl = "{$apiUrl}{$movie->off_movie_id}?api_key={$apiKey}"; // a film műfajainak url-e
      
              $response = Http::get($movieApiUrl); // itt kérem le a műfajokat
      
              if ($response->successful()) {
                  $movieData = $response->json();
                  echo "API response is ok: {$movie->title}\n";
                  print_r($movieData['genres']);  // Kiírjuk a filmhez tartozó műfajokat
      
                  // műfajok feldolgozása
                  foreach ($movieData['genres'] as $genreData) {

                    $genreName = $genreData['name'];
                      echo "Genre: {$genreName}\n"; // múfaj kiiratás
      
                      $genre = Genre::where('name', $genreName)->first(); // ha létezik a műfaj a genre táblában
      
                      if ($genre) { // akkor
                          echo "Genre is valid {$genreName}\n";
                          try {
                              MovieGenre::insert([ // hozzáadjuk a táblánnkba
                                  'movie_id' => $movie->id,
                                  'genre_id' => $genre->id,
                                  'created_at' => now(),
                                  'updated_at' => now(),
                              ]);
                              echo "Added to the movie_genre table {$movie->id}, Műfaj ID: {$genre->id}\n";
                          } catch (\Exception $e) {
                              echo "Something went wrong.. " . $e->getMessage() . "\n"; //kapjuk el a hibát
                          }
                      } else {
                          echo "The genre is not in the genre table: {$genreName}\n";//ha nem szerepel ab-ban
                      }
                  }
              } else {
                  // Ha nem sikerült lekérni a műfajokat a filmhez
              echo "Could not find the genre: {$movie->title}\n"; //api kérés nem volt sikeres
              }
          }
      
          // Válasz visszaadása a sikeres művelethez
          return response()->json(['message' => 'Movie genres updated successfully']);
      
    }
}
