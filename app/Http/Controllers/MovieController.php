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
            $apiUrl = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}&with_original_language=ko&primary_release_year=2025&sort_by=popularity.desc";
        
            // lekérjük az apiból a pages oldalak össz számát
            $firstResponse = Http::get($apiUrl);

            $data = $firstResponse->json();

            $totalPages = $data['total_pages']; //kinyerjük a teljes oldalszámot

            $movies = collect(); // ide gyűjtjük az összes oldalon összegyűjtött filmeket

            for ($page = 1; $page <= $totalPages; $page++) {
                $mainApiUrl =  "{$apiUrl}&page={$page}";
                $response = Http::get($mainApiUrl);
        
                if ($response->successful()) {
                    $newMovies = collect($response->json()['results'] ?? [])
                        ->filter(fn($movie) => 
                            !empty($movie['title']) &&
                            !empty($movie['poster_path']) &&
                            !empty($movie['release_date']) &&
                            $movie['release_date'] > now()->toDateString()
                        );
        
                    $movies = $movies->merge($newMovies); // mergelni kell az összes oldalról megkapott film adatokat
                }
            }
        


            $filteredMovies = $movies // ezt tovább kell rendezni
            ->sortBy('release_date') 
            ->take(5) 
            ->map(fn($movie) => [ // azért van szükség  mert midnen filmhez egy tömböt rendel és a képeket képként tudom megjeleníteni, egy összefűzéssel
                'id' => $movie['id'],
                'title' => $movie['title'],
                'poster_path' => $baseUrl . $movie['poster_path'],
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
