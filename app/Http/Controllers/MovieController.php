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

     public function getPopularMoviesTmdb(){

        $apiKey = env('TMDB_API_KEY');
        $baseUrl = "https://image.tmdb.org/t/p/w500/";

        $apiUrl = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}&with_original_language=ko&primary_release_year=2025&sort_by=popularity.desc&page=1";

        $allMovie = [];

            $response = Http::get($apiUrl); //lekérjük

            if($response->successful()) {
                $movies = $response->json()['results'];
                $allMovies = array_merge($allMovie, $movies); // itt adjuk hozzá az üres tömbhöz a filmek választ
            }

    

        //népszerűség szerint redezzük
        usort($allMovies, function($a, $b) { //összehasonlító fgv
            return $b['popularity'] <=> $a['popularity']; //csökkenő sorrendben népszerűség szerint rendezve <=> z jelzi
        });

        //kiválasztjuk az első 5-öt
        $popularMovies = array_slice($allMovies, 0, 5);

        $posters = array_map(function ($movie)use ($baseUrl) { // végig map-elünk rajtuk, és kinyerjük a poster img-t
            return $baseUrl . $movie['poster_path'];
        }, $popularMovies);

        return response()->json($posters); //visszatérünk az első 5 képpel

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
