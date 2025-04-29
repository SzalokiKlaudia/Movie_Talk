<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\UserMovie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Psy\Readline\Userland;

class UserController extends Controller
{
    public function index()
    {
        // Az összes felhasználó lekérése
        $users = User::all();

        // A felhasználókat visszaadjuk a válaszban
        return response()->json($users);
    }

    public function userDataModify(UpdateUserRequest $request)
    {
        
        $user = Auth::user(); //automatikusan bejelentkezett felhasználó

        $user->update($request->validated());//csak a validáltat friistíjük ab-ban
           

        return response()->json([
            'message' => 'User data updated! ',

        ]);

    }


    public function topActiveUsers() // megszámoljuk az aktív felhasználókat, mely user hány értékelést adott le és abból a top 5
{
    $topUsers = UserMovie::select('users.id', 'users.user_name', 'users.created_at', 'pictures.name', 
        DB::raw("CONCAT('/storage/', pictures.name) as profile_picture_name"),
        DB::raw('COUNT(user_movies.user_id) as number'))

        ->join('users', 'users.id', '=', 'user_movies.user_id') 
        ->leftJoin('pictures', 'pictures.user_id', '=', 'users.id') 
        ->whereNotNull('user_movies.rating') 
        ->whereNull('users.deleted_at') 
        ->whereNull('user_movies.deleted_at') 
        ->groupBy('users.id', 'users.user_name', 'users.created_at', 'pictures.name') 
        ->orderByDesc('number') 
        ->limit(5) 
        ->get();

    return response()->json($topUsers); 
}


 
}
