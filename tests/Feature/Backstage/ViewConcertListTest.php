<?php

namespace Tests\Feature\Backstage;

use App\Models\Concert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewConcertListTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function guests_cannot_view_a_promoters_concert_list()
    {
        $response = $this->get('/backstage/concerts');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     *  @test
     */
    public function promoters_can_only_view_a_list_of_their_own_concerts()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $concertA = Concert::factory()->create(['user_id' => $user->id]);
        $concertB = Concert::factory()->create(['user_id' => $user->id]);
        $concertC = Concert::factory()->create(['user_id' => $otherUser->id]);
        $concertD = Concert::factory()->create(['user_id' => $user->id]);

        $response = $this->get('/backstage/concerts');

        $response->assertStatus(200);
        $this->assertTrue($response->original->getData()['concerts']->contains($concertA));
        $this->assertTrue($response->original->getData()['concerts']->contains($concertB));
        $this->assertTrue($response->original->getData()['concerts']->contains($concertD));
        $this->assertFalse($response->original->getData()['concerts']->contains($concertC));
    }
}