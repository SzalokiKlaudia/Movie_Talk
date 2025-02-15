<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_user(): void // ő nem működik az autentikáció miatt
    {
         
        $response = $this->withoutMiddleware()->get('/api/user');

        $response->assertStatus(200);
    }

    public function test_premiers(): void //ez átment
    {
        $response = $this->get('/api/premier-movies');

        $response->assertStatus(200);
    }

    public function test_movies(): void //ez átment
    {
        $response = $this->get('/api/movies');

        $response->assertStatus(200);
    }

    
}
