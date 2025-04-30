<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserMovieRequest;
use App\Http\Requests\UpdateUserMovieRequest;
use App\Models\UserMovie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserMovieController extends Controller
{
    public function addMovieToUser(AddUserMovieRequest $request) // validálunk, és a felh-hoz adunk adott filmet
    { // itt jelöljük megnézésre egy filmet

        $user = Auth::user(); 
        if (!$user) {
            return response()->json(['message' => 'Did you forget to login?'], 401); 
        }

        $userMovie = UserMovie::create([
            'user_id' => $user->id,
            'movie_id' => $request->movie_id,
            'rating' => null,
            'watching_date' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'You have succesfully added the movie to the list!',
            'data' => $userMovie,
        ], 200);
    }

    public function updateRatingAndWatchingDate(UpdateUserMovieRequest $request) // validálunk form reqest-el
    { // itt értékelünk, és megnézés dát-ot adunk
        $validatedData = $request->validated(); //csak a validált adatokkal dolgozunk

        $user = Auth::user(); //a bej. user

        $userMovie = UserMovie::where('id', $validatedData['id'])
            ->where('user_id', $user->id) // lekérem a user adott rekordját
            ->first();

        if (!$userMovie) { // ha nincs ilyen
            return response()->json(['error' => 'Movie record not found for this user.'], 404);
        }

        $userMovie->rating = $validatedData['rating'];
        $userMovie->watching_date = $validatedData['watching_date'];
        $userMovie->updated_at = Carbon::now();
        $userMovie->save();



        return response()->json([
            'success' => true,
            'message' => 'Movie is rated successfully!',
            'data' => $userMovie,
        ], 200);

    }

    public function index() // bej. user filmjei
    {
        $userMovies = DB::table('user_movies as um')
        ->select([
            'um.id',
            'um.user_id',
            'um.movie_id',
            'm.title',
            'um.watching_date',
            'um.rating',
        ])
        ->join('movies as m', 'm.id', '=', 'um.movie_id')
        ->where('um.user_id', Auth::id())
        ->get();

        if ($userMovies->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'You have not added any movies to your list yet!',
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'You can browse freely through your movie list now!',
            'data' => $userMovies,
        ], 200);
    }

    public function destroy(UserMovie $movie) // törtli a saját filmjeit
    {
        if ($movie->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $movie->delete();
        return response()->json(['message' => 'Movie deleted successfully']);
    }

    /*public function getUsersTopRatedMovies()
    {
        $movies = DB::table('UsersMaxRatings')
            ->join('movies', 'UsersMaxRatings.movie_id', '=', 'movies.id')
            ->select('UsersMaxRatings.user_id','UsersMaxRatings.movie_id','movies.title', 'movies.image_url', 'UsersMaxRatings.max_rating')
            ->whereIn('UsersMaxRatings.max_rating', function($query) {
                $query->select(DB::raw('max(max_rating)'))
                      ->from('UsersMaxRatings')
                      ->groupBy('UsersMaxRatings.user_id');

            })
            ->inRandomOrder() // a filmek véletszerű sorrendben jelennek
            ->distinct()
            ->limit(5) 
            ->get();

        return response()->json($movies);
    }*/

    public function getUsersTopRatedMovies()//userek top filmjei
    {
        $movies = DB::table('UsersMaxRatings')
            ->select('UsersMaxRatings.movie_id','movies.title', 'movies.image_url', 'UsersMaxRatings.max_rating','movies.trailer_url','movies.cast_url','movies.description','movies.release_date','movies.duration_minutes')
            ->join('movies', 'UsersMaxRatings.movie_id', '=', 'movies.id')
            
            ->whereIn('UsersMaxRatings.max_rating', function($query) {//csak 5 pontos filmek szerepeljenek
                $query->select(DB::raw('max(max_rating)'))
                      ->from('UsersMaxRatings')
                      ->groupBy('UsersMaxRatings.user_id');

            })
            ->groupBy(
                'UsersMaxRatings.movie_id',
                'movies.title', 'movies.image_url',
                'UsersMaxRatings.max_rating',
                'movies.trailer_url',
                'movies.cast_url',
                'movies.description',
                'movies.release_date',
                'movies.duration_minutes'
            )

            ->inRandomOrder() // a filmek véletszerű sorrendben jelennek
            ->limit(5) 
            ->get();

            $moviesWithGenresAndKeywords = $movies->map(function ($movie) { //minden egyes filmen végigmegyünk és megkeressük a műfajait, kulcsszavait (kattinthatóság miatt)
                $genres = DB::table('movie_genres')
                    ->join('genres', 'genres.id', '=', 'movie_genres.genre_id')
                    ->where('movie_genres.movie_id', $movie->movie_id)
                    ->pluck('name');//tömbbel tér vissza a műfajok
    
                $keywords = DB::table('movie_keywords')
                    ->join('keywords', 'keywords.id', '=', 'movie_keywords.keyword_id')
                    ->where('movie_keywords.movie_id', $movie->movie_id)
                    ->pluck('name');

                    $movie->genres = $genres;
                    $movie->keywords = $keywords;
    
    
                return $movie;
            });

        return response()->json([
            'succes'=>true,
            'data'=>$moviesWithGenresAndKeywords,

        ],200);
    }

    public function userFavoriteMoviesByGenre($userId) // top filmek egy user-nek (csak 5-ös értékelésű filmek)
    {
        $movies = DB::table('UsersMaxRatings as u') 
            ->select('u.movie_id', 'f.title','u.user_id', 'u.max_rating','f.image_url','f.release_date','f.trailer_url','f.cast_url','f.description','f.duration_minutes')
            ->join('movies as f', 'u.movie_id', '=', 'f.id')
            ->where('u.user_id', '=', $userId)
            ->whereRaw('u.max_rating = (SELECT MAX(l.max_rating) FROM UsersMaxRatings l WHERE l.user_id = u.user_id)')
            ->groupBy('u.movie_id', 'f.title','u.user_id', 'u.max_rating','f.image_url', 'f.release_date','f.trailer_url','f.cast_url','f.description','f.duration_minutes')
            ->inRandomOrder() // a filmek véletszerű sorrendben jelennek
            ->limit(5) 
            ->get();

            $moviesWithGenresAndKeywords = $movies->map(function ($movie) { //minden egyes filmen végigmegyünk és megkeressük a műfajait, kulcsszavait
                $genres = DB::table('movie_genres')
                    ->join('genres', 'genres.id', '=', 'movie_genres.genre_id')
                    ->where('movie_genres.movie_id', $movie->movie_id)
                    ->pluck('name');//tömbbel tér vissza a műfajok
    
                $keywords = DB::table('movie_keywords')
                    ->join('keywords', 'keywords.id', '=', 'movie_keywords.keyword_id')
                    ->where('movie_keywords.movie_id', $movie->movie_id)
                    ->pluck('name');

                    $movie->genres = $genres;
                    $movie->keywords = $keywords;
            
                return $movie;
            });

        return response()->json([
            'success' => true,
            'message' => 'Top movies are ready to show!',
            'data' => $moviesWithGenresAndKeywords,
        ], 200);
    }


    
}
