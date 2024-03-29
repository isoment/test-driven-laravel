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
        $concert = Concert::factory()->create([
            'date' => '2022-08-21 18:00:00'
        ]);

        // Create an order
        $order = Order::factory()->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'card_last_four' => '1881',
            'amount' => 8500
        ]);

        // Ticket A
        Ticket::factory()->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'TICKETCODE123'
        ]);

        // Ticket B
        Ticket::factory()->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'TICKETCODE456'
        ]);

        // Visit the order confirmation page
        $response = $this->get("/orders/ORDERCONFIRMATION1234");
        
        $response->assertStatus(200);

        // Assert we see the correct order details
        $response->assertViewHas('order', $order);

        $response->assertSee('ORDERCONFIRMATION1234');
        $response->assertSee('$85.00');
        $response->assertSee('**** **** **** 1881');
        $response->assertSee('TICKETCODE123');
        $response->assertSee('TICKETCODE456');
        $response->assertSee('Example Band');
        $response->assertSee('With the Fake Openers');
        $response->assertSee('The Example Theatre');
        $response->assertSee('123 Example Ln');
        $response->assertSee('Fakeville');
        $response->assertSee('NY');
        $response->assertSee('90210');
        $response->assertSee('somebody@example.com');
        $response->assertSee('2022-08-21 18:00');
    }
}