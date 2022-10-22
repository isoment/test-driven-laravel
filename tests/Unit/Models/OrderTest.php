<?php

namespace Tests\Unit\Models;

use App\Billing\Charge;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function creating_an_order_from_tickets_email_and_charge()
    {
        $charge = new Charge([
            'amount' => 3600,
            'card_last_four' => '1234'
        ]);

        // Instead of creating real tickets we can create some spies.
        $tickets = collect([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
        ]);

        $order = Order::forTickets($tickets, 'john@example.com', $charge);

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals('1234', $order->card_last_four);
        // Since we are calling claimFor() in forTickets() we can check that for each ticket spy
        // this method was called.
        $tickets->each->shouldHaveReceived('claimFor', [$order]);
    }

    /**
     *  @test
     */
    public function retrieving_an_order_by_confirmation_number()
    {
        $order = Order::factory()->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234'
        ]);

        $foundOrder = Order::findByConfirmationNumber('ORDERCONFIRMATION1234');

        $this->assertEquals($order->id, $foundOrder->id);
    }

    /**
     *  @test
     *  @doesNotPerformAssertions
     */
    public function retrieving_a_nonexistent_order_by_confirmation_number_throws_an_exception()
    {
        try {
            Order::findByConfirmationNumber('NONEXISTENTCONFIRMATION');
        } catch(ModelNotFoundException $e) {
            return;
        }

        $this->fail('No matching order was found for the specified confirmation number, but an exception
            was not throw.');
    }

    /**
     *  @test
     */
    public function converting_to_an_array()
    {
        $order = Order::factory()->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email' => 'jane@example.com',
            'amount' => 6000
        ]);
        
        $order->tickets()->saveMany([
            Ticket::factory()->create(['code' => 'TICKETCODE1']),
            Ticket::factory()->create(['code' => 'TICKETCODE2']),
            Ticket::factory()->create(['code' => 'TICKETCODE3']),
        ]);

        $result = $order->toArray();

        $this->assertEquals([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email' => 'jane@example.com',
            'amount' => 6000,
            'tickets' => [
                ['code' => 'TICKETCODE1'],
                ['code' => 'TICKETCODE2'],
                ['code' => 'TICKETCODE3'],
            ]
        ], $result);
    }
}
