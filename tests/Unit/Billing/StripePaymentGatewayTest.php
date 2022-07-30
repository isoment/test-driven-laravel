<?php

namespace Tests\Unit\Billing;

use App\Billing\StripePaymentGateway;
use Tests\TestCase;

class StripePaymentGatewayTest extends TestCase
{
    /**
     *  @test
     */
    public function charges_with_a_valid_payment_token_are_successful()
    {
        $stripe = new \Stripe\StripeClient();

        $lastCharge = $this->lastCharge($stripe);

        // Create a new stripe payment gateway instance
        $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));

        // Create a new charge for an amount using a valid token
        $paymentGateway->charge(2500, $this->validToken($stripe));

        // Assert that there is a new charge created
        $this->assertCount(1, $this->newCharges($stripe, $lastCharge));
        // Assert that the most recent charge is for the correct amount
        $this->assertEquals(2500, $this->lastCharge($stripe)->amount);
    }

    /**
     *  We want to get the last charge in order to determine our starting point when asserting
     *  against the new charge.
     *  @param \Stripe\StripeClient $stripe
     *  @return \Stripe\Charge
     */
    private function lastCharge(\Stripe\StripeClient $stripe) : \Stripe\Charge
    {
        return $stripe->charges->all(
            ['limit' => 1],
            ['api_key' => config('services.stripe.secret')]
        )['data'][0];
    }

    /**
     *  Verify that the charge was completed successfully, since stripe keeps a record
     *  of all the previous charges we want to ensure that we are checking against this
     *  new charge not one of the older ones.
     *  @param \Stripe\StripeClient $stripe
     *  @param \Stripe\Charge $endingBefore
     *  @return array
     */
    private function newCharges(\Stripe\StripeClient $stripe, \Stripe\Charge $endingBefore) : array
    {
        return $stripe->charges->all(
            [
                'limit' => 1,
                'ending_before' => $endingBefore->id
            ],
            ['api_key' => config('services.stripe.secret')]
        )['data'];
    }

    /**
     *  We need to get a valid stripe payment token in order to make charges charge
     *  @param \Stripe\StripeClient $stripe
     *  @return string
     */
    private function validToken(\Stripe\StripeClient $stripe) : string
    {
        return $stripe->tokens->create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '123',
            ],
        ], ['api_key' => config('services.stripe.secret')])->id;
    }
}