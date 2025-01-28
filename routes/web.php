<?php

use App\Http\Controllers\MovieDataController;
use App\Http\Controllers\MovieImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';

Route::get('/import-movies-with-trailers', [MovieImportController::class, 'importMoviesWithTrailers']);

Route::get('/update-movie-data', [MovieDataController::class, 'updateMoviesData']);

Route::get('/manually-update-duration', [MovieDataController::class, 'manuallyUpdateDuration']);

Route::get('/update-movie-cast-urls', [MovieDataController::class, 'updateMoviesCastUrl']);




