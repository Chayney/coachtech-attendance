<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class DateTimeTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_it_displays_the_current_datetime()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $now = now()->isoFormat("YYYY年MM月DD日(ddd)");
        $response = $this->get('/attendance');
        $response->assertStatus(200)->assertSee($now);
        $user->delete();
    }
}
