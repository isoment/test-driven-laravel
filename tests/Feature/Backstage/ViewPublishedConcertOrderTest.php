<?php

namespace Tests\Feature\Backstage;

use App\Models\Concert;
use App\Models\User;
use Database\Helpers\FactoryHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewPublishedConcertOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function a_promoter_can_view_the_orders_of_their_own_published_concert()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $concert = FactoryHelpers::createPublished(['user_id' => $user->id]);

        $response = $this->get("/backstage/published-concerts/{$concert->id}/orders");

        $response->assertStatus(200);
        $response->assertViewIs('backstage.published-concert-orders.index');
        $this->assertTrue($response->data('concert')->is($concert));
    }

    /**
     *  @test
     */
    public function a_promoter_cannot_view_the_orders_of_unpublished_concerts()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $concert = FactoryHelpers::createUnpublished(['user_id' => $user->id]);

        $response = $this->get("/backstage/published-concerts/{$concert->id}/orders");

        $response->assertStatus(404);
    }

    /**
     *  @test
     */
    public function a_promoter_cannot_view_the_orders_of_another_published_concert()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $concert = FactoryHelpers::createPublished(['user_id' => $otherUser->id]);

        $response = $this->get("/backstage/published-concerts/{$concert->id}/orders");

        $response->assertStatus(404);
    }

    /**
     *  @test
     */
    public function a_guest_cannot_view_the_orders_of_any_published_concert()
    {
        $concert = FactoryHelpers::createPublished();

        $response = $this->get("/backstage/published-concerts/{$concert->id}/orders");

        $response->assertRedirect('/login');
    }
}