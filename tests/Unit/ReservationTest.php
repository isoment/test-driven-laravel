<?php

namespace Tests\Unit;

use App\Models\Concert;
use App\Models\Ticket;
use App\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function calculating_the_total_cost()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200]
        ]);

        $reservation = new Reservation($tickets, 'john@example.com');

        $this->assertEquals(3600, $reservation->totalCost());
    }

    /**
     *  @test
     */
    public function retrieving_the_reservations_tickets()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200]
        ]);

        $reservation = new Reservation($tickets, 'john@example.com');

        $this->assertEquals($tickets, $reservation->tickets());
    }

    /**
     *  @test
     */
    public function retrieving_the_customers_email()
    {
        $reservation = new Reservation(collect(), 'john@example.com');

        $this->assertEquals('john@example.com', $reservation->email());
    }

    /**
     *  @test
     */
    public function reserved_tickets_are_released_when_a_reservation_is_cancelled()
    {
        $tickets = collect([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class)
        ]);
        $reservation = new Reservation($tickets, 'john@example.com');
    
        $reservation->cancel();
    
        foreach ($tickets as $ticket) {
            $ticket->shouldHaveReceived('release')->once();
        }
    }

    /**
     *  @test
     */
    public function completing_a_reservation()
    {
        $concert = Concert::factory()->create(['ticket_price' => 1200]);
        $tickets = Ticket::factory()->count(3)->create(['concert_id' => $concert->id]);
        $reservation = new Reservation($tickets, 'john@example.com');

        $order = $reservation->complete();

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
    }
}
