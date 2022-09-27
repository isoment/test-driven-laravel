<?php

namespace Tests\Feature\Backstage;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddConcertTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function guests_cannot_view_the_concert_form()
    {
        $response = $this->get('/backstage/concerts/new');

        $response->assertStatus(302);
    }

    /**
     *  @test
     */
    public function promoters_can_view_the_add_concert_form()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/backstage/concerts/new');

        $response->assertStatus(200);
    }
}