<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use Illuminate\Support\Facades\Http;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
        //népszerű filmek lekérése a tmdb-ből
        public function getPopularMoviesTmdb() { 
            // TMDB API kulcs és alap URL a képekhez
            $apiKey = env('TMDB_API_KEY');
            $baseUrl = "https://image.tmdb.org/t/p/w500/";
        
            // API URL (2025-ös koreai filmek népszerűség szerint)
            $apiUrl = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}&with_original_language=ko&primary_release_year=2025&sort_by=popularity.desc&page=1";
        
            // API lekérés
            $response = Http::get($apiUrl);
        
            // Ellenőrizzük, hogy sikeres volt-e a kérés
            if (!$response->successful()) {
                return response()->json(['error' => 'Failed to fetch movies'], 500);
            }
        
            $movies = $response->json()['results'] ?? [];
        
            // Népszerűség szerint csökkenő sorrendbe rakjuk
            usort($movies, function($a, $b) {
                return $b['popularity'] <=> $a['popularity'];
            });
        
            // Kiválasztjuk az első 5 filmet
            $popularMovies = array_slice($movies, 0, 5);
        
            // Csak az ID-t, címet és posztert mentjük el
            $formattedMovies = array_map(function ($movie) use ($baseUrl) {
                return [
                    'id' => $movie['id'],
                    'title' => $movie['title'],
                    'poster_path' => $baseUrl . $movie['poster_path'], // Teljes poszter URL
                    'release_date' => $movie['release_date'],
                ];
            }, $popularMovies);
        
            // JSON válasz visszaadása
            return response()->json($formattedMovies);
        }


    

     public function moviesByTitle($title){  //miylen xy című filmek vannak?

        $movieTitles = Movie::where('title','like','%'. $title . '%')->get(['title']);

        // Visszaküldjük a filmek címeit JSON formátumban
        return response()->json($movieTitles);

     }

     
     public function getMovieByTitle($title){  //mi az xy című film adatai?

        $movies = Movie::where('title','like','%'. $title . '%')->get();

        if ($movies->isEmpty()) {
            return response()->json(['message' => 'Film not found'], 404);
        }
    
        // Visszaküldjük a filmek adatait JSON formátumban
        return response()->json($movies);

     }

     //milyen xy filmek vannak adott kulcsszavak szerint?
     public function getMovieByKeyword($keyword){

        $movie = Movie::where()->get();


     }
    


    public function index() //összes film lekérdezése
    {
          
          $movies = Movie::all();

          return response()->json($movies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovieRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMovieRequest $request, Movie $movie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        //
    }
}
