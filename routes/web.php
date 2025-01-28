<?php

use App\Http\Controllers\MovieDataController;
use App\Http\Controllers\MovieImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';

Route::get('/import-movies', [MovieImportController::class, 'importMovies']);

Route::get('/update-movie-data', [MovieDataController::class, 'updateMoviesData']);

Route::get('/update-movie-data', [MovieDataController::class, 'updateMoviesTrailerUrls']);

