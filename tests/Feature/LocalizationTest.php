<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test redirect from root to /gate.
     */
    public function test_root_redirects_to_gate(): void
    {
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertRedirect('/gate');
    }

    /**
     * Test /gate requires authentication.
     */
    public function test_gate_requires_authentication(): void
    {
        $response = $this->get('/gate');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * Test login page loads successfully and contains lang toggle.
     */
    public function test_login_page_loads_and_contains_toggle(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('lang/vi', false);
        $response->assertSee('lang/en', false);
        $response->assertSee('VISITOR CONTROL');
    }

    /**
     * Test switching locale via route.
     */
    public function test_switching_locale_sets_session(): void
    {
        // Switch to English
        $response = $this->get('/lang/en');
        $response->assertStatus(302);
        $response->assertSessionHas('locale', 'en');

        // Switch back to Vietnamese
        $response = $this->get('/lang/vi');
        $response->assertStatus(302);
        $response->assertSessionHas('locale', 'vi');
    }

    /**
     * Test gate control page displays unified navbar.
     */
    public function test_gate_page_displays_unified_navbar(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/gate');
        $response->assertStatus(200);
        
        // Assert top navbar is loaded
        $response->assertSee('Gate Control');
        $response->assertSee('Visitor List');
        
        // Assert that duplicate CCTV language switcher is removed
        $response->assertDontSee('Language Switcher inside CCTV Header');
        
        // Assert the modal list is removed
        $response->assertDontSee('id="list-modal"', false);
        $response->assertDontSee('id="list-iframe"', false);
    }

    /**
     * Test logout redirects to login.
     */
    public function test_logout_redirects_to_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
