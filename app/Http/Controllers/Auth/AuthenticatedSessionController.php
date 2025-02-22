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
    public function store(LoginRequest $request): Response
    {  
        // Az autentikáció validálása a LoginRequest alapján
        $request->authenticate(); // Meghívja a LoginRequest-ben lévő hitelesítést

        $request->session()->regenerate(); // Session fixation elleni védelem
    
        return response()->noContent(); // 204 válasz (sikeres, de nincs adat)
    }

    public function storeBearer(LoginRequest $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);
        if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'Invalid login credentials'], 401); }
            
        $user = Auth::user();//tesztesetekhez
        //$user->id
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'status' => 'Login successful',
        ]);
    }

    public function logout(Request $request)
{
    // A bejelentkezett felhasználó tokenjeinek törlése
    $request->user()->tokens->each(function ($token) {
        $token->delete(); // Minden generált token törlése
    });

    return response()->json(['message' => 'Successfully logged out'], 200);
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        /*$request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout successful']);*/
        
       // Kijelentkezés után egy üzenet visszaküldése
        //return response()->json(['message' => 'Logout successful']);
        

        
       // A felhasználó kijelentkeztetése
       Auth::guard('web')->logout();

       // A session törlése és a token frissítése
       $request->session()->invalidate();
       $request->session()->regenerateToken();
       return response()->noContent();


   }
}

            
    
    

