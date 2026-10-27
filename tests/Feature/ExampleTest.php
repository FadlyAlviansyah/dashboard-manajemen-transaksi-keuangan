<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/dashboard-and-tabel');

        $dashboardResponse = $this->get('/dashboard-and-tabel');
        $dashboardResponse->assertStatus(200);
    }
}
