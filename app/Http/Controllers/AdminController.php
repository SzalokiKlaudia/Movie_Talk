<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
        // Admin felhasználók listázása
        public function index()
        {
            // Az összes felhasználó lekérése
            $users = User::all();
    
            // A felhasználókat visszaadjuk a válaszban
            return response()->json($users);
        }
}
