<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserDataConfUserControllerUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $user = User::create([
            'user_name' => 'TestUser123',
            'name' => 'Test Name',
            'email' => 'test@example.com',
            'password' => 'test2121',
            'gender' => 'male',
            'birth_year' => 1990,
        ]);

        $this->actingAs($user);
        
        $updateData = [
            'user_name' => 12345,
        ];
        $response = $this->patchJson('/api/user/update', $updateData);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user_name']);
    }
}
