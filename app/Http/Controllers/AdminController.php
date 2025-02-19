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
            $users = User::select('id', 'user_name','name','gender', 'email', 'created_at')->paginate(10);
            return response()->json($users);
        }

        public function deleteUser($id)
        {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $user->delete(); // itt törli

            return response()->json(['message' => 'User soft deleted successfully']);
        }

        public function getDeletedUsers() // törölt userek mutatása
        {
            $deletedUsers = User::onlyTrashed()->get(); // azok a felh akiket soft delete-el töröltek

            return response()->json($deletedUsers);
        }       

        public function restoreUser($id) // ha az admin visszaállítaná a usert
        {
            $user = User::onlyTrashed()->find($id);
    
            if (!$user) {
                return response()->json(['error' => 'User not found or not deleted'], 404);
            }
    
            $user->restore(); // Visszaállítja a soft delete-et
    
            return response()->json(['message' => 'User restored successfully']);
        }
}
