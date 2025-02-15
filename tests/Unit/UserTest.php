<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

  

    public function test_user(): void
    {
         
        $response = $this->withoutMiddleware()->get('/api/user');

        $response->assertStatus(200);
    }
}
