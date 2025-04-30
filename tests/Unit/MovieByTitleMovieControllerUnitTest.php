<?php

namespace Tests\Unit;
use Tests\TestCase;


class MovieByTitleMovieControllerUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $response = $this->postJson('/api/movie/title', [
            'title' => '000'
        ]);

        $data = $response->getData(true);

        $this->assertEquals(404, $response->status());
        $this->assertFalse($data['success']);
        $this->assertEquals('Film not found', $data['message']);
    }
}

