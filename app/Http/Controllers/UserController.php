<?php

namespace App\Http\Controllers;

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

    public function userDataModify(Request $request)
    {
        $user = Auth::user(); //automatikusan bejelentkezett felhasználó

        $user->update([
            'name' => $request->input('name'),
            'birth_year' => $request->input('birth_year'),
        ]);

        return response()->json([
            'message' => 'User data updated! ',

        ]);

    }

 

    
    public function topActiveUsers()//megszámoljuk az aktív felh-kat és abból a top 5
    {    $topUsers = UserMovie::select('users.id', 'users.user_name', 'users.created_at', DB::raw('COUNT(user_movies.user_id) as number'))
        ->join('users', 'users.id', '=', 'user_movies.user_id') 
        ->whereNotNull('user_movies.rating') 
        ->whereNull('users.deleted_at')  // töröltet ne számoljon
        ->whereNull('user_movies.deleted_at')
        ->groupBy('users.id', 'users.user_name', 'users.created_at') 
        ->orderByDesc('number') 
        ->limit(5) 
        ->get();
    
        return response()->json($topUsers);

    }


 
}
