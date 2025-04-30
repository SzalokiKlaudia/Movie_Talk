<?php

namespace Tests\Unit;

use App\Models\Movie;
use App\Models\User;
use App\Models\UserMovie;
use Tests\TestCase;

class TopusersUserControllerUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $movie = Movie::create([
            'title' => 'Test Movie', 
            'release_date' => now(),
            'description' => 'Test description',
            'duration_minutes' => 120,
            'off_movie_id' => 9876543,
            'image_url' => 'https//picture',
            'cast_url' => 'https//cast',
            'trailer_url' => 'https//video'
        ]);

        $userWithRating = User::create([
            'user_name' => 'TestTom',
            'name' => 'Test User 1',
            'email' => 'testTom@example.com',
            'gender' => 'male',
            'birth_year' => '1980',
            'password' => '012345'
        
        ]);

        $userWithoutRating = User::create([
            'user_name' => 'TestMark',
            'name' => 'Teszt User 2',
            'email' => 'testMark@example.com',
            'gender' => 'male',
            'birth_year' => '1980',
            'password' => '678910'
        ]);

        UserMovie::create([
            'user_id' => $userWithRating->id,
            'rating' => 5,
            'movie_id' => $movie->id
        ]);

        $response = $this->getJson('/api/movie/top-users');
        $data = $response->json();

        $userNames = array_column($data, 'user_name');

        $this->assertContains('TestTom', $userNames);
        $this->assertNotContains('TestMark', $userNames);
    }
}
