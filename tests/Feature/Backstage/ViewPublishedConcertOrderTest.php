<?php

namespace Tests\Feature\Backstage;

use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
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
        $response->assertViewIs('backstage.published-concert-orders.list');
        $this->assertTrue($response->data('concert')->is($concert));
    }

    /**
     *  @test
     */
    public function a_promoter_can_view_the_10_most_recent_orders_for_their_concerts()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $concert = FactoryHelpers::createPublished(['user_id' => $user->id]);

        $oldOrder = FactoryHelpers::createOrderForConcert($concert, ['created_at' => Carbon::parse('11 days ago')]);
        $recentOrder1 = FactoryHelpers::createOrderForConcert($concert, ['created_at' => Carbon::parse('10 days ago')]);
        $recentOrder2 = FactoryHelpers::createOrderForConcert($concert, ['created_at' => Carbon::parse('9 days ago')]);
        $recentOrder3 = FactoryHelpers::createOrderForConcert($concert, ['created_at' => Carbon::parse('8 days ago')]);
        $recentOrder4 = FactoryHelpers::createOrderForConcert($concert, ['created_at' => Carbon::parse('7 days ago')]);
        $recentOrder5 = FactoryHelpers::createOrderForConcert($concert, ['created_at' => Carbon::parse('6 days ago')]);
        $recentOrder6 = FactoryHelpers::createOrderForConcert($concert, ['created_at' => Carbon::parse('5 days ago')]);
        $recentOrder7 = FactoryHelpers::createOrderForConcert($concert, ['created_at' => Carbon::parse('4 days ago')]);
        $recentOrder8 = FactoryHelpers::createOrderForConcert($concert, ['created_at' => Carbon::parse('3 days ago')]);
        $recentOrder9 = FactoryHelpers::createOrderForConcert($concert, ['created_at' => Carbon::parse('2 days ago')]);
        $recentOrder10 = FactoryHelpers::createOrderForConcert($concert, ['created_at' => Carbon::parse('1 days ago')]);

        $response = $this->get("/backstage/published-concerts/{$concert->id}/orders");

        $response->data('orders')->assertNotContains($oldOrder);

        // We want to assert that the orders response is in the correct order
        $response->data('orders')->assertEquals([
            $recentOrder10,
            $recentOrder9,
            $recentOrder8,
            $recentOrder7,
            $recentOrder6,
            $recentOrder5,
            $recentOrder4,
            $recentOrder3,
            $recentOrder2,
            $recentOrder1,
        ]);
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