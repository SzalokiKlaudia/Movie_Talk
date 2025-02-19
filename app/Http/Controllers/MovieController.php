<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Http\Requests\SimpleSearchRequest;
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
        
        
            // API URL
            $apiUrl = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}&with_original_language=ko&primary_release_year=2025&sort_by=popularity.desc&page=1";
        
            // API lekérés
            $response = Http::get($apiUrl);
        
                
            if (!$response->successful()) {
                return response()->json(['error' => 'Failed to fetch movies from TMDB API'], 500);
            }

            $movies = collect($response->json()['results'] ?? []);

            $filteredMovies = $movies->filter(function ($movie) {
                    return !empty($movie['title']) 
                        && !empty($movie['poster_path']) 
                        && !empty($movie['release_date'])
                        && $movie['release_date'] > now()->toDateString();
                })
                ->sortByDesc('release_date')
                ->take(5)
                ->map(fn($movie) => [
                    'id' => $movie['id'],
                    'title' => $movie['title'],
                    'poster_path' => "https://image.tmdb.org/t/p/w500/" . $movie['poster_path'],
                    'release_date' => $movie['release_date'],
                ])
                ->values();

            return response()->json($filteredMovies);
        
          
        }
     
        // egyszerű kereséshez cím alapján dobja vissza a film adatokat
     public function getMovieByTitle(SimpleSearchRequest $request){  
        $validatedRequest = $request->validated();
        $title = $validatedRequest['title'];

        $movies = Movie::where('title','like','%'. $title . '%')->get();

        if ($movies->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Film not found'
            ], 404);
        }
    
        return response()->json([
            'success' => true,
            'data' => $movies
        ], 200);

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
