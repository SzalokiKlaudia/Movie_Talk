<?php

namespace Tests\Unit;

use App\Http\Requests\MovieSearchRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AdvancedSearchMovieControllerUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $requestData = [
            'keyword' => '0123456789101112131415',
        ];
        $validator = Validator::make($requestData, (new MovieSearchRequest())->rules());
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('keyword', $validator->errors()->toArray());
        $this->assertEquals('The keyword field must not be greater than 20 characters.', $validator->errors()->first('keyword'));

    }
}
