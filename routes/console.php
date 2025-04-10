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
    $this->call('App\Console\Commands\ImportMoviesWithTrailers');
    $this->info('Movie data imports are successfull');
});


Artisan::command('import:duration-times', function () {
    $this->call('App\Console\Commands\ImportDurationtimes');
    $this->info('Duration minutes updated succesfully');
});


Artisan::command('import:cast-url', function () {
    $this->call('App\Console\Commands\ImportCastUrl');
    $this->info('Movies casts data is updated succesfully!');
});

Artisan::command('import:genres', function () {
    $this->call('App\Console\Commands\ImportGenres');
    $this->info('Import genres succesfully!');
});

Artisan::command('import:keywords', function () {
    $this->call('App\Console\Commands\ImportKeywords');
    $this->info('Import keywords!');
});

Artisan::command('import:movies-genres', function () {
    $this->call('App\Console\Commands\ImportMoviesGenres');
    $this->info('Movie genres import finished.');
});


Artisan::command('import:movies-keywords', function () {
    $this->call('App\Console\Commands\ImportMoviesKeywords');
    $this->info('Movie keywords import finished.');
});


// adatok feltöltése

// php artisan import:movies-with-trailers
// php artisan import:duration-times
// php artisan import:cast-url
// php artisan import:genres
// php artisan import:keywords
// php artisan import:movies-genres 
//php artisan import:movies-keywords

//seederek
// php artisan db:seed --class=UserSeeder
// php artisan db:seed --class=UserMovieSeeder