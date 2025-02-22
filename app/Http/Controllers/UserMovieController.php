<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserMovieRequest;
use App\Http\Requests\UpdateUserMovieRequest;
use App\Models\UserMovie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class UserMovieController extends Controller
{
    public function addMovieToUser(AddUserMovieRequest $request) // validálunk, és a felh-hoz adunk adott filmet
    { // itt jelöljük megnézésre egy filmet

        $user = Auth::user(); 
        if (!$user) {
            return response()->json(['message' => 'Did you forget to login?'], 401); 
        }

        UserMovie::create([
            'user_id' => $user->id,
            'movie_id' => $request->movie_id,
            'rating' => null,
            'watching_date' => null
        ]);

        return response()->json(['message' => 'You have succesfully added the movie to the list'], 201);
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

        return response()->json(['message' => 'Movie updated successfully']);

    }

    public function index() // bej. user filmjei
    {
        $movies = UserMovie::where('user_id', Auth::id())->get();
        return response()->json($movies);
    }

    public function destroy(UserMovie $movie) // törtli a saját filmjeit
    {
        if ($movie->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $movie->delete();
        return response()->json(['message' => 'Movie deleted successfully']);
    }


    
}
