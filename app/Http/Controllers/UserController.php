<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
