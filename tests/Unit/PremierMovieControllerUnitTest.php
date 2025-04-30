<?php

namespace Tests\Unit;

use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PremierMovieControllerUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        Http::fake([//mi történik ha kevesebb mint 5 találat jön vissza
            'https://api.themoviedb.org/3/discover/movie*' => Http::response([
                'total_pages' => 1,
                'results' => [
                    [
                        'id' => 123,
                        'title' => 'Test Movie 1',
                        'poster_path' => '/poster1.jpg',
                        'release_date' => now()->addDays(2)->toDateString(),
                    ],
                    [
                        'id' => 124,
                        'title' => 'Test Movie 2',
                        'poster_path' => '/poster2.jpg',
                        'release_date' => now()->addDays(5)->toDateString(),
                    ],
                    [
                        'id' => 125,
                        'title' => 'Test Movie 3',
                        'poster_path' => '/poster3.jpg',
                        'release_date' => now()->addDays(7)->toDateString(),
                    ],
                    [
                        'id' => 126,
                        'title' => 'Test Movie 4',
                        'poster_path' => '/poster4.jpg',
                        'release_date' => now()->addDays(10)->toDateString(),
                    ],
                
                ],
            ]),
        ]);

        $controller = new MovieController();
        $response = $controller->getPremierMoviesTmdb();

        $data = $response->getData(true);
        $this->assertNotCount(5,$data, 'A válasz nem 5 db találatot ad vissza!!');

    }
}
