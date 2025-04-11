<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterUserRequest $request)
    {
        $validated = $request->validated();
       

        $user = User::create([
            'password' => Hash::make($validated['password']),// Jelszó hashelése
            'user_name' => $validated['user_name'],
            'email' => $validated['email'],
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'birth_year' => $validated['birth_year'],
          
        ]);

        event(new Registered($user));

        Auth::login($user);//tesztesetekhez


        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);

        //return response()->noContent();//tesztesetekhez


    }
}
