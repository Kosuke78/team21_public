<?php

namespace Tests\Feature;

use Tests\TestCase;

class CafeInfoTest extends TestCase
{

    public function test_取得できる()
    {
        $response = $this->getJson('/api/cafe');

        $response->assertStatus(200);
    }
}
