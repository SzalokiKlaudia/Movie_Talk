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
        public function getPremierMoviesTmdb() {
            // TMDB API kulcs 
            $apiKey = env('TMDB_API_KEY', 'd177b1faa31eb756e208e96f34fbeb53');
            $baseUrl = "https://image.tmdb.org/t/p/w500/";//alap URL a képekhez!!
        
            if (!$apiKey) {// ha ninc skulcsunk
                return response()->json(['error' => 'TMDB API key is missing'], 500);
            }
        
            // API URL
            $apiUrl = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}&with_original_language=ko&primary_release_year=2025&sort_by=popularity.desc&page=1";
        
            // API lekérés
            $response = Http::get($apiUrl);
        
            // Ellenőrizzük, hogy sikeres volt-e a kérés
            if (!$response->successful()) {
                return response()->json(['error' => 'Failed to fetch movies from TMDB API'], 500);
            }
        
            // Ellenőrizzük, hogy a válasz tartalmazza a kívánt adatokat
            $movies = $response->json()['results'] ?? null;
            if (empty($movies)) {
                return response()->json(['error' => 'No movies found'], 404);
            }
        
            // Kiválasztjuk azokat a filmeket, amelyek kiadási dátuma nagyobb mint a mai dátum
            $today = now()->toDateString(); // Mai dátum
            $futureMovies = array_filter($movies, function($movie) use ($today) {
                return isset($movie['release_date']) && $movie['release_date'] > $today;
            });
        
            // Kiválasztjuk az első 5 filmet a jövőbeni kiadási dátumok alapján, és rendezzük őket csökkenő sorrendben
            usort($futureMovies, function($a, $b) {
                return $b['release_date'] <=> $a['release_date'];
            });
        
            // Csak az első 5 filmet tartjuk meg
            $futureMovies = array_slice($futureMovies, 0, 5);
        
            // Csak az ID-t, címet és posztert mentjük el
            $formattedMovies = array_map(function ($movie) use ($baseUrl) {
                return [
                    'id' => $movie['id'],
                    'title' => $movie['title'],
                    'poster_path' => isset($movie['poster_path']) ? $baseUrl . $movie['poster_path'] : null, // Ellenőrizzük, hogy van poszter
                    'release_date' => $movie['release_date'], 
                ];
            }, $futureMovies);
        
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
