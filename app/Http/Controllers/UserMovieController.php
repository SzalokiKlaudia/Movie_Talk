<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserMovieRequest;
use App\Models\UserMovie;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;

class UserMovieController extends Controller
{
    public function addMovieToUser(AddUserMovieRequest $request) // validálunk
    {

        UserMovie::create([
            'user_id' => $request->user_id,
            'movie_id' => $request->movie_id,
            'rating' => null,
            'watching_date' => null
        ]);

        return response()->json(['message' => 'Film sikeresen hozzáadva a listához'], 201);
    }
}
