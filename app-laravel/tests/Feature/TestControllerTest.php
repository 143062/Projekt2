<?php

namespace Tests\Feature;

use Tests\TestCase;

class TestControllerTest extends TestCase
{
    public function test_index_returns_test_message()
    {
        $response = $this->getJson('/api/test');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Test działa!']);
    }
}
