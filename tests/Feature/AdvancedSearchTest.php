<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdvancedSearchTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $data = [
            'title' => 'the',
            'releasFrom' => '2020-01-01',
            'releaseTo' => '2025-01-01'
        ];

        $response = $this->postJson('/http://127.0.0.1:8000/api/movie/advanced-search', $data);

        $response->assertStatus(200);
    }
}
