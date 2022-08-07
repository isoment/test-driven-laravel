<?php

namespace Tests\Unit\Billing;

use App\Billing\PaymentFailedException;
use App\Billing\StripePaymentGateway;
use Tests\TestCase;

/**
 *  @group integration
 */
class StripePaymentGatewayTest extends TestCase
{
    protected function setUp() : void
    {
        parent::setUp();
        $this->stripe = new \Stripe\StripeClient(); 
        $this->lastCharge = $this->lastCharge();
    }

    protected function getPaymentGateway()
    {
        return new StripePaymentGateway(config('services.stripe.secret'));
    }

    /**
     *  @test
     */
    public function charges_with_a_valid_payment_token_are_successful()
    {
        // Create a new stripe payment gateway instance
        $paymentGateway = $this->getPaymentGateway();

        $newCharges = $paymentGateway->newChargesDuring(function() {
            // Create a new charge for an amount using a valid token
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        });

        // Assert that there is a new charge created
        $this->assertCount(1, $newCharges);

        // Assert that the most recent charge is for the correct amount
        $this->assertEquals(2500, $newCharges->sum());
    }

    /**
     *  @test
     */
    public function charges_with_an_invalid_payment_token_fail()
    {
        try {
            $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));
            $paymentGateway->charge(2500, 'invalid-payment-token');
        } catch(PaymentFailedException $e) {
            $this->assertCount(0, $this->newCharges());
            return;
        }

        $this->fail("Charging with an invalid payment token did not throw a PaymentFailedException");
    }

    /**
     *  We want to get the last charge in order to determine our starting point when asserting
     *  against the new charge.
     */
    private function lastCharge()
    {
        return $this->stripe->charges->all(
            ['limit' => 1],
            ['api_key' => config('services.stripe.secret')]
        )['data'][0];
    }

    /**
     *  Get all the new charges after the specified charge id
     */
    private function newCharges()
    {
        return $this->stripe->charges->all(
            [
                'ending_before' => $this->lastCharge ? $this->lastCharge->id : null,
            ],
            ['api_key' => config('services.stripe.secret')]
        )['data'];
    }
}