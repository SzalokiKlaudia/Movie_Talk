<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Http\Requests\SimpleSearchRequest;
use App\Http\Requests\MovieSearchRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

        if(empty($title)){
            return response()->json([
                'success' => false,
                'message' => 'Title is empty!'
            ], 400);
        }

        $movies = Movie::where('title','like',$title . '%')->get();//collenction objektumot ad vissza

        if ($movies->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Film not found'
            ], 404);
        }
    
        $moviesWithGenresAndKeywords = $movies->map(function ($movie) { //minden egyes filmen végigmegyünk és megkeressük a műfajait, kulcsszavait
            $genres = DB::table('movie_genres')
                ->join('genres', 'genres.id', '=', 'movie_genres.genre_id')
                ->where('movie_genres.movie_id', $movie->id)
                ->pluck('name');//tömbbel tér vissza a műfajok

            $keywords = DB::table('movie_keywords')
                ->join('keywords', 'keywords.id', '=', 'movie_keywords.keyword_id')
                ->where('movie_keywords.movie_id', $movie->id)
                ->pluck('name');

            $movie->genres = $genres;//hozzáadjuk a műfajokat az akt film objektumhoz
            $movie->keywords = $keywords;//hozzáadjuk a kulcssazavakat az akt film objektumhoz.

            return $movie;
        });

        return response()->json([
            'success' => true,
            'data' => $moviesWithGenresAndKeywords
        ], 200);

     }

     public function getMovieGenresAndKeywords($id){//lekérjük a film kulcsszavait és műfajait megjelenítésre
      
        /*$genres = DB::table('movie_genres')
            ->join('genres', 'genres.id', '=', 'movie_genres.genre_id')
            ->where('movie_genres.movie_id', $id)
            ->pluck('name');

        $keywords = DB::table('movie_keywords')
            ->join('keywords', 'keywords.id', '=', 'movie_keywords.keyword_id')
            ->where('movie_keywords.movie_id', $id)
            ->pluck('name');

        return response()->json([
            'id' => $id, 
            'genres' => $genres,
            'keywords' => $keywords,
        ], 200);*/

     }

    
     public function advancedSearch(MovieSearchRequest $request) {

        $releaseFrom = $request->filled('releaseFrom') ? Carbon::parse($request->releaseFrom)->toDateString() : null;//dátum formázás h biztos jó legyen a dátum összehasonlítás
        $releaseTo = $request->filled('releaseTo') ? Carbon::parse($request->releaseTo)->toDateString() : null;

        $subquery = DB::table('movies as m')
            ->select([
                'm.id',
                'm.title',
                'm.release_date', 
                'm.description', 
                'm.duration_minutes', 
                'm.image_url', 
                'm.trailer_url', 
                'm.cast_url', 
                'm.created_at', 
                'm.updated_at',
                DB::raw('GROUP_CONCAT(DISTINCT g.name) as genre_name'),//egyetlen stringben fűzi össze a műfaj értékeket
                DB::raw('GROUP_CONCAT(DISTINCT k.name) as keyword')//-||-
            ])
            ->join('movie_genres as mg', 'm.id', '=', 'mg.movie_id')
            ->join('genres as g', 'g.id', '=', 'mg.genre_id')
            ->join('movie_keywords as mk', 'm.id', '=', 'mk.movie_id')
            ->join('keywords as k', 'k.id', '=', 'mk.keyword_id')
            ->groupBy('m.id','m.title','m.release_date','m.description', 
                'm.duration_minutes', 
                'm.image_url', 
                'm.trailer_url', 
                'm.cast_url', 
                'm.created_at', 
                'm.updated_at', );

        $query = DB::table(DB::raw("({$subquery->toSql()}) as a"))//ellenőrzés
            ->mergeBindings($subquery);


            if ($request->filled('title') && $request->title !== '') {
                $query->where('a.title', 'LIKE', "%{$request->title}%");
            }
            
            if ($request->filled('genre') && $request->genre !== '') {
                $query->where('a.genre_name', 'LIKE', "%{$request->genre}%");
            }
            
            if ($request->filled('keyword') && $request->keyword !== '') {
                $query->where('a.keyword', 'LIKE', "%{$request->keyword}%");
            }
            
            if ($releaseFrom && $releaseTo) {
                $query->whereBetween(DB::raw("STR_TO_DATE(a.release_date, '%Y-%m-%d')"), [$releaseFrom, $releaseTo]);
            } elseif ($releaseFrom) {
                $query->where(DB::raw("STR_TO_DATE(a.release_date, '%Y-%m-%d')"), '>=', $releaseFrom);
            } elseif ($releaseTo) {
                $query->where(DB::raw("STR_TO_DATE(a.release_date, '%Y-%m-%d')"), '<=', $releaseTo);
            }
        
        $movies = $query->get();//collection obj ad vissza amin már lehet map-elni

        $moviesWithGenresAndKeywords = $movies->map(function ($movie) {
            $movie->genres = explode(',', $movie->genre_name); //átalakítjuk a string értékeket tömbbé explode segítségével a megjelenítés miatt kell
            $movie->keywords = explode(',', $movie->keyword); 
            
            unset($movie->genre_name, $movie->keyword);//
    
            return $movie;
        });

    
        if ($moviesWithGenresAndKeywords->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No data found',
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data successfully selected',
            'data' => $moviesWithGenresAndKeywords,
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
    public function show($id)
    {
        
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
