<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PictureController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::middleware('web')->group(base_path('routes/auth.php'));

//bárki által elérhető
//Route::post('/register',[RegisteredUserController::class, 'store']);
//Route::post('/login-bearer',[AuthenticatedSessionController::class, 'storeBearer']);



//Kijelentkezési útvonal minden bejelentkezett felhasználónak
//Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'destroy']);

Route::get('file-upload', [PictureController::class, 'index'])->name('file.upload');
Route::post('file-upload', [PictureController::class, 'store'])->name('file.upload.store');
Route::get('/users',[UserController::class, 'index']);//próba

Route::get('/premier-movies', [MovieController::class, 'getPremierMoviesTmdb']);//népszerűség szerint rendezve az első 5 premier film a kezdőoldalon!
Route::get('/movies',[MovieController::class, 'index']);
//miylen xy című filmek vannak
Route::get('/movies/titles/{title}', [MovieController::class, 'moviesByTitle']);
//miylen adatai vannak az xy című filmek vannak?
Route::get('/movie/title/{title}', [MovieController::class, 'getMovieByTitle']);

Route::get('/movie/top-users/', [UserController::class, 'topActiveUsers']);//top 5 tag



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
    Route::get('/admin/users', [AdminController::class, 'index']);//admin látja a felhazsnálók adatait
});

//Route::post('/login',[AuthenticatedSessionController::class, 'store']);