<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the landing page works when database/roles table is completely empty.
     */
    public function test_landing_page_renders_successfully_with_empty_database(): void
    {
        // No seeding is performed, so users/roles/submissions tables are empty
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SEMAR');
        $response->assertSee('Manajemen');
        $response->assertSee('Protokol Masuk');
        $response->assertSee('Reviewer Tervalidasi');
    }

    /**
     * Test that about page works when database is empty.
     */
    public function test_about_page_renders_successfully_with_empty_database(): void
    {
        $response = $this->get('/tentang');

        $response->assertStatus(200);
    }

    /**
     * Test that SOP page works when database is empty.
     */
    public function test_sop_page_renders_successfully_with_empty_database(): void
    {
        $response = $this->get('/sop');

        $response->assertStatus(200);
    }
}
