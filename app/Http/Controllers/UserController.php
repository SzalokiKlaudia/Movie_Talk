<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Az összes felhasználó lekérése
        $users = User::all();

        // A felhasználókat visszaadjuk a válaszban
        return response()->json($users);
    }
}
