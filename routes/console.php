<?php

use App\Http\Controllers\MovieGenreImportController;
use App\Http\Controllers\MovieKeywordImportController;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

//feltöltjük adatokkal a movie táblát, azért get mert csak adatokat kérek le a tmdb-ről de nem módosítom annak az adatait


Artisan::command('import:movies-with-trailers', function () {
    app()->call('App\Http\Controllers\MovieImportController@importMoviesWithTrailers');
    $this->info('Filmek importálása előzetesekkel kész!');
});

Artisan::command('update:movie-data', function () {
    app()->call('App\Http\Controllers\MovieDataController@updateMoviesData');
    $this->info('Filmadatok frissítése kész!');
});

Artisan::command('manually:update-duration', function () {
    app()->call('App\Http\Controllers\MovieDataController@manuallyUpdateDuration');
    $this->info('Filmek hosszának kézi frissítése kész!');
});

Artisan::command('update:movie-cast-urls', function () {
    app()->call('App\Http\Controllers\MovieDataController@updateMoviesCastUrl');
    $this->info('Szereplői URL-ek frissítése kész!');
});

Artisan::command('update:movies-genres', function () {
    app()->call('App\Http\Controllers\GenreDataController@updateMoviesGenre');
    $this->info('Filmek műfaj adatai frissítve!');
});

Artisan::command('update:movie-keywords', function () {
    app()->call('App\Http\Controllers\KeywordDataController@updateMovieKeywords');
    $this->info('Kulcsszavak frissítése kész!');
});

Artisan::command('import:movie-genres', function () {
    $controller = new MovieGenreImportController();
    $controller->importMovieGenres();
    $this->info('Movie genres import finished.');
});


Artisan::command('import:movie-keywords', function () {
    // Controller példányosítása
    $controller = new MovieKeywordImportController();

    // Meghívjuk az importálás metódust
    $controller->importMovieKeywords();

    // Visszajelzés a konzolon
    $this->info('Movie keywords import finished.');
});