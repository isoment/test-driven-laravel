<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Models\Concert;
use App\OrderConfirmationNumberGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Mockery;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{
    use RefreshDatabase;

    /*
        When we pass the PaymentGateway interface into the ConcertOrderController the service container 
        does not know what to resolve. Here we can specify that we resolve the PaymentGateway interface
        to the FakePaymentGateway
    */
    protected function setUp() : void
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    private function orderTickets(int $concertId, array $params) : TestResponse
    {
        return $this->json('POST', "/concerts/{$concertId}/orders", $params);
    }

    /**
     *  @test
     */
    public function customer_can_purchase_tickets_to_a_published_concert()
    {
        $this->withExceptionHandling();

        // Create a mock of OrderConfirmationNumberGenerator that will always return ORDERCONFIRMATION1234.
        $orderConfirmationNumberGenerator = Mockery::mock(OrderConfirmationNumberGenerator::class, [
            'generate' => 'ORDERCONFIRMATION1234'
        ]);

        // Resolve our mock on the service container.
        $this->app->instance(OrderConfirmationNumberGenerator::class, $orderConfirmationNumberGenerator);

        $concert = Concert::factory()
            ->published()
            ->create([
                'ticket_price' => 3250
            ])->addTickets(3);

        $response = $this->orderTickets($concert->id, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'amount' => 9750
        ]);

        // Make sure the customer was charged the correct amount
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());

        // Ensure that the order exists fro the customer and that there are 3 tickets
        $this->assertTrue($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(3, $concert->ordersFor('john@example.com')->first()->ticketQuantity());
    }

    /**
     *  @test
     */
    public function an_order_is_not_created_if_payment_fails()
    {
        $concert = Concert::factory()
            ->published()
            ->create(['ticket_price' => 3250])
            ->addTickets(3);

        $response = $this->orderTickets($concert->id, [
            'email' => 'test@user.com',
            'ticket_quantity' => 3,
            'payment_token' => 'invalid-payment-token'
        ]);

        $response->assertStatus(422);
        $this->assertFalse($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(3, $concert->ticketsRemaining());
    }

    /**
     *  @test
     */
    public function cannot_purchase_tickets_to_an_unpublished_concert()
    {
        $concert = Concert::factory()
            ->unpublished()
            ->create()
            ->addTickets(3);

        $response = $this->orderTickets($concert->id, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(404);
        $this->assertFalse($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    /**
     *  @test
     */
    public function cannot_purchase_more_tickets_than_remain()
    {
        $concert = Concert::factory()
            ->published()
            ->create()
            ->addTickets(50);

        $response = $this->orderTickets($concert->id, [
            'email' => 'john@example.com',
            'ticket_quantity' => 51,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        /*
            Assert that there is no order created, the customer was not charged
            and the concert tickets are still available.
        */
        $response->assertStatus(422);
        $this->assertFalse($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /**
     *  @test
     */
    public function cannot_purchase_tickets_another_customer_is_already_trying_to_purchase()
    {
        $this->withoutExceptionHandling();

        $concert = Concert::factory()
            ->published()
            ->create([
                'ticket_price' => 1200
            ])
            ->addTickets(3);

        $this->paymentGateway->beforeFirstCharge(function($paymentGateway) use($concert) {

            $requestA = $this->app['request'];

            $responseB = $this->orderTickets($concert->id, [
                'email' => 'personB@example.com',
                'ticket_quantity' => 1,
                'payment_token' => $this->paymentGateway->getValidTestToken()
            ]);

            $this->app['request'] = $requestA;

            $responseB->assertStatus(422);
            $this->assertFalse($concert->hasOrderFor('personB@example.com'));
            $this->assertEquals(0, $this->paymentGateway->totalCharges());
        });

        $responseA = $this->orderTickets($concert->id, [
            'email' => 'personA@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertEquals(3600, $this->paymentGateway->totalCharges());
        $this->assertTrue($concert->hasOrderFor('personA@example.com'));
        $this->assertEquals(3, $concert->ordersFor('personA@example.com')->first()->ticketQuantity());
    }

    /**
     *  @test
     */
    public function email_is_required_to_purchase_tickets()
    {
        $concert = Concert::factory()->published()->create();

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
        $concert = Concert::factory()->published()->create();

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
        $concert = Concert::factory()->published()->create();

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
        $concert = Concert::factory()->published()->create();

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
        $concert = Concert::factory()->published()->create();

        $response = $this->orderTickets($concert->id, [
            'email' => 'test@test.com',
            'ticket_quantity' => 3
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['The payment token field is required.']);
    }
}
