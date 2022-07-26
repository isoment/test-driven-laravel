<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;

class StripePaymentGatewayTest extends TestCase
{
    /**
     *  @test
     */
    public function charges_with_a_valid_payment_token_are_successful()
    {
        // Create a new stripe payment gateway instance
        // $paymentGateway = new StripePaymentGateway;

        $stripe = new \Stripe\StripeClient();

        $token = $stripe->tokens->create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '123',
            ],
        ], ['api_key' => config('services.stripe.secret')]);

        dd($token);

        // Create a new charge for an amount using a valid token
        // $paymentGateway->charge(2500, 'valid-token');

        // Verify that the charge was completed successfully
    } 
}