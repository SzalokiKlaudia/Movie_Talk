<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        /*$request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);
        if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'Invalid login credentials'], 401); }
            
        $user = Auth::user();//tesztesetekhez
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'status' => 'Login successful',
        ]);*/

        //return response()->noContent();//tesztesetekhez


        // Az autentikáció validálása a LoginRequest alapján
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Megpróbáljuk az autentikációt az email és a jelszó alapján
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }
        
        // visszaküldünk egy válasz üzenetet vagy státuszt
        return response()->noContent();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        /*$request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout successful']);*/
        
       // A felhasználó kijelentkeztetése
       Auth::guard('web')->logout();

       // A session törlése és a token frissítése
       $request->session()->invalidate();
       $request->session()->regenerateToken();
       return response()->noContent();


       // Kijelentkezés után egy üzenet visszaküldése
       //return response()->json(['message' => 'Logout successful']);
   }
}

            
    
    

