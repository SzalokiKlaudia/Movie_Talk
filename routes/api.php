<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//bárki által elérhető
Route::post('/register',[RegisteredUserController::class, 'store']);
Route::post('/login',[AuthenticatedSessionController::class, 'store']);
// Kijelentkezési útvonal minden bejelentkezett felhasználónak
Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'destroy']);

Route::get('/users',[UserController::class, 'index']);//próba

Route::get('/popular-movies', [MovieController::class, 'getPopularMoviesTmdb']);//népszerűség szerint rendezve az első 5 premier film a kezdőoldalon!
Route::get('/movies',[MovieController::class, 'index']);





//regisztrált felhasználó
Route::middleware(['auth:sanctum'])
->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user(); //// Visszaadja a jelenlegi bejelentkezett felhasználó adatait
    });
    //módositunk adatot felhazsnálónak, név és szül_év
    Route::patch('/user/update', [UserController::class, 'userDataModify']);


  
});

//admin útvonalak
Route::middleware(['auth:sanctum', Admin::class])
->group(function () {
    Route::get('/admin/users', [AdminController::class, 'index']);
  
  
});

