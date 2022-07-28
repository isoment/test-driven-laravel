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
        // Create a new stripe payment gateway instance
        $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));

        $stripe = new \Stripe\StripeClient();

        $token = $stripe->tokens->create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '123',
            ],
        ], ['api_key' => config('services.stripe.secret')])->id;

        // Create a new charge for an amount using a valid token
        $paymentGateway->charge(2500, $token);

        // Verify that the charge was completed successfully
        $lastCharge = $stripe->charges->all(
            ['limit' => 1],
            ['api_key' => config('services.stripe.secret')]
        )['data'][0];

        $this->assertEquals(2500, $lastCharge->amount);
    } 
}