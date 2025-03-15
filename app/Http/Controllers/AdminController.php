<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserMovie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller

{

        public function getUsersForAdmin()
        {
            if (Auth::user() && Auth::user()->is_admin) {
                return response()->json(User::all());
            }
            
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // admin listázza a usereket
        public function getUsers(int $isActive, string $userName = null) // ha  nem ad username-et akkor default null
        {
            $users = User::withTrashed()->select([ // kell a withtrashed h mutasa a törölt fh-kat is a soft delet aut eltakarja
                    'id',
                    'user_name',
                    'email',
                    'name',
                    'gender',
                    'birth_year',
                    'created_at',
                    'deleted_at'
                ])
                ->when($userName, fn($query) => $query->where('user_name', 'LIKE', "%{$userName}%"))
                ->when($isActive === 0, fn($query) => $query->whereNull('deleted_at')) // aktív
                ->when($isActive === 1, fn($query) => $query->whereNotNull('deleted_at')) // inaktív
                ->get();
    
            return response()->json($users);
        }


        public function deleteUser($id) // user felh törlése
        {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $user->delete(); // itt törli

            return response()->json(['message' => 'User soft deleted successfully']);
        }
     

        public function restoreUser($id) // ha az admin visszaállítaná a usert
        {
            $user = User::onlyTrashed()->find($id);
    
            if (!$user) {
                return response()->json(['error' => 'User not found or not deleted'], 404);
            }
    
            $user->restore(); // visszaállítja a soft delete-et
        
            return response()->json(['message' => 'User restored successfully']);
        }

        public function getUsersMovies(int $isActive, string $userName = null) // ha  nem ad username-et akkor default null
        {
            $query = DB::table('user_movies')
            ->join('users', 'user_movies.user_id', '=', 'users.id') 
            ->join('movies', 'user_movies.movie_id', '=', 'movies.id') 
            ->select(
                'users.user_name',
                'movies.title as movie_title', 
                'user_movies.watching_date',
                'user_movies.rating',
                'user_movies.deleted_at as user_movie_deleted_at',
            );

            $query->when($userName, function ($query) use ($userName) {
                $query->where('users.user_name', 'LIKE', "%{$userName}%"); // itt szűrünk rá ha van user name
            });

            $query->when($isActive === 0, function ($query) {
                $query->whereNull('user_movies.deleted_at'); // ha aktív
            });

            $query->when($isActive === 1, function ($query) {
                $query->whereNotNull('user_movies.deleted_at'); // ha inaktív
            });

            
            $users = $query->get();

            return response()->json($users);
        }
}
