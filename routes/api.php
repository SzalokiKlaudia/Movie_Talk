<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PictureController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserMovieController;
use App\Http\Middleware\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::middleware('web')->group(base_path('routes/auth.php'));

//Route::post('/login-bearer',[AuthenticatedSessionController::class, 'storeBearer']);// teszteléshez
//Route::post('/logout-bearer', [AuthenticatedSessionController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/movie/{id}/details', [MovieController::class, 'getMovieGenresAndKeywords']); //film műfajai, kulcsszavai
Route::get('/premier-movies', [MovieController::class, 'getPremierMoviesTmdb']);//népszerűség szerint rendezve az első 5 premier film a kezdőoldalon!
Route::get('/movies',[MovieController::class, 'index']); // összes film adatát látod
Route::post('/movie/title', [MovieController::class, 'getMovieByTitle']); // adott film adatának megjelenítése cím szerint
Route::post('/movie/advanced-search', [MovieController::class, 'advancedSearch']); // adott film adatának megjelenítése paraméterek szerint
Route::get('/movie/top-users', [UserController::class, 'topActiveUsers']);//top 5 tagot kiadja
Route::get('/movie/top-rated-movies', [UserMovieController::class, 'getUsersTopRatedMovies']);// fh-k legmagasabban értékelt filmjei

//regisztrált felhasználó
Route::middleware(['auth:sanctum'])
->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user(); //// Visszaadja a jelenlegi bejelentkezett felhasználó adatait
    });
    //módositunk adatot felhazsnálónak, név és szül_év
    Route::patch('/user/update', [UserController::class, 'userDataModify']);
    Route::post('/user/add-movies', [UserMovieController::class, 'addMovieToUser']); //hozzáadja a user a filmet a sajátjába
    Route::patch('/user/add/rating', [UserMovieController::class, 'updateRatingAndWatchingDate']);// értékelés
    Route::get('/user/movies', [UserMovieController::class, 'index']); // ő visszaadja a bej felh filmjeit
    Route::delete('/user/movies/{movie}', [UserMovieController::class, 'destroy']);
    Route::post('user/profile-picture/upload', [PictureController::class, 'store']); // képfeltöltés
    Route::get('/user/profile-picture', [PictureController::class, 'show']); // kép lekérés
    Route::get('user/{userId}/favorite-movies', [UserMovieController::class, 'userFavoriteMoviesByGenre']); //top filmek

});

//admin útvonalak
Route::middleware(['auth:sanctum', Admin::class])
->group(function () {
    Route::get('/admin/users', [AdminController::class, 'getUsersForAdmin']);
    Route::get('/admin/users/{isActive}/{userName?}', [AdminController::class, 'getUsers']); // listázza a fh-kat paraméterek alapján
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']); // itt soft delete-el törli
    Route::patch('/admin/users/{id}/restore', [AdminController::class, 'restoreUser']); // visszaállítja  a törölt felh-t
    Route::get('/admin/user-movies/{isActive}/{userName?}', [AdminController::class, 'getUsersMovies']); // listázza a fh filmjeit, paraméter alapján
  
});

