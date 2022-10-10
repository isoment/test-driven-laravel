<?php

namespace Tests\Feature\Backstage;

use App\Models\Concert;
use App\Models\User;
use Database\Helpers\FactoryHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishConcertTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function a_promoter_can_publish_their_own_concert()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'ticket_quantity' => 3,
        ]);

        $response = $this->post('/backstage/published-concerts', [
             'concert_id' => $concert->id
        ]);

        $response->assertRedirect('/backstage/concerts');
        $concert = $concert->fresh();
        $this->assertTrue($concert->isPublished());
        $this->assertEquals(3, $concert->ticketsRemaining());
    }

    /**
     *  @test
     */
    public function a_concert_can_only_be_published_once()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $concert = FactoryHelpers::createPublished([
            'user_id' => $user->id,
            'ticket_quantity' => 3,
        ]);

        $response = $this->post('/backstage/published-concerts', [
             'concert_id' => $concert->id
        ]);

        $response->assertStatus(422);
        $this->assertEquals(3, $concert->fresh()->ticketsRemaining());
    }

    /**
     *  @test
     */
    public function a_promoter_cannot_publish_other_concerts()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $otherUser->id,
            'ticket_quantity' => 3,
        ]);

        $response = $this->post('/backstage/published-concerts', [
            'concert_id' => $concert->id
        ]);

        $response->assertStatus(404);
        $concert = $concert->fresh();
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());
    }

    /**
     *  @test
     */
    public function guests_cannot_publish_concerts()
    {
        $concert = Concert::factory()->unpublished()->create([
            'ticket_quantity' => 3,
        ]);

        $response = $this->post('/backstage/published-concerts', [
            'concert_id' => $concert->id
        ]);

        $response->assertRedirect('login');
        $concert = $concert->fresh();
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());
    }

    /**
     *  @test
     */
    public function concerts_that_do_not_exist_cannot_be_published()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/backstage/published-concerts', [
            'concert_id' => 99999
        ]);

        $response->assertStatus(404);
    }
}