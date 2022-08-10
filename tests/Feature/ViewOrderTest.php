<?php

namespace Tests\Feature;

use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ViewOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function user_can_view_their_order_confirmation()
    {
        // Create a concert
        $concert = Concert::factory()->create();

        // Create an order
        $order = Order::factory()->create();

        // Create a ticket
        $ticket = Ticket::factory()->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id
        ]);

        // Visit the order confirmation page
        $this->get("/orders/{$order->id}");

        // Assert we see the correct order details
    }
}