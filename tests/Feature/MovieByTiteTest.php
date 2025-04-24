<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MovieByTiteTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $data = [
            'title' => 'train'
        ];

        $response = $this->postJson('http://127.0.0.1:8000/api/movie/title', $data);

        $response->assertStatus(200);
    }
}
