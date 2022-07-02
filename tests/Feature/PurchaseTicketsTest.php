<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Models\Concert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp();

        /*
            When we pass the PaymentGateway interface into the ConcertOrderController laravel does
            not know what to resolve. Here we can specify that we resolve the PaymentGateway interface
            to the FakePaymentGateway
        */
        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    private function orderTickets($concert, array $params) : TestResponse
    {
        return $this->json('POST', "/concerts/{$concert}/orders", $params);
    }

    /**
     *  @test
     */
    public function customer_can_purchase_concert_tickets()
    {
        $concert = Concert::factory()->create([
            'ticket_price' => 3250
        ]);

        $response = $this->orderTickets($concert->id, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(201);

        // Make sure the customer was charged the correct amount
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());

        // Ensure that the order exists fro the customer and that there are 3 tickets
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets->count());
    }

    /**
     *  @test
     */
    public function email_is_required_to_purchase_tickets()
    {
        $concert = Concert::factory()->create();

        $response = $this->orderTickets($concert->id, [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['The email field is required.']);
    }

    /**
     *  @test
     */
    public function email_must_be_valid_to_purchase_tickets()
    {
        $concert = Concert::factory()->create();

        $response = $this->orderTickets($concert->id, [
            'email' => 'not_an_email',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['The email must be a valid email address.']);
    }

    /**
     *  @test
     */
    public function ticket_quantity_is_required_to_purchase_tickets()
    {
        $concert = Concert::factory()->create();

        $response = $this->orderTickets($concert->id, [
            'email' => 'john@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['The ticket quantity field is required.']);
    }

    /**
     *  @test
     */
    public function the_ticket_quantity_must_be_at_lease_1_to_purchase_tickets()
    {
        $concert = Concert::factory()->create();

        $response = $this->orderTickets($concert->id, [
            'email' => 'test@test.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['The ticket quantity must be at least 1.']);
    }

    /**
     *  @test
     */
    public function the_payment_token_is_required()
    {
        $concert = Concert::factory()->create();

        $response = $this->orderTickets($concert->id, [
            'email' => 'test@test.com',
            'ticket_quantity' => 3
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['The payment token field is required.']);
    }
}
