<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Models\Concert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

    /**
     *  @test
     */
    public function customer_can_purchase_concert_tickets()
    {
        $concert = Concert::factory()->create([
            'ticket_price' => 3250
        ]);

        $response = $this->json('POST', "/concerts/{$concert->id}/orders", [
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

        $response = $this->json('POST', "/concerts/{$concert->id}/orders", [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['The email field is required.']);
    }
}
