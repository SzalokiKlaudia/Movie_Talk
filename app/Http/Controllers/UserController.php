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
    {
        $topActiveUsers = UserMovie::select('user_id', DB::raw('count(*) as number'))
        ->whereNotNull('rating')  
        ->groupBy('user_id')  
        ->orderByDesc(DB::raw('count(*)'))  
        ->limit(5)  
        ->get();

    }

 
}
