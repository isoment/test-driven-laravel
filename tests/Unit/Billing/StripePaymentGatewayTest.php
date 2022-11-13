<?php

namespace Tests\Unit\Billing;

use App\Billing\StripePaymentGateway;
use Tests\TestCase;

/**
 *  @group integration
 */
class StripePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;

    protected function getPaymentGateway()
    {
        return new StripePaymentGateway(config('services.stripe.secret'));
    }

    /**
     *  @test
     */
    public function ninety_percent_of_the_payment_is_transferred_to_the_destination_account()
    {
        // Init a new StripeClient
        $stripe = new \Stripe\StripeClient();
        $stripeSecret = config('services.stripe.secret');

        // We need to pass the secret key into the payment gateway
        $paymentGateway = new StripePaymentGateway($stripeSecret);

        // Create a $50 charge
        $paymentGateway->charge(5000, $paymentGateway->getValidTestToken(), env('STRIPE_TEST_PROMOTER_ID'));

        // Get the last charge
        $lastStripeCharge = $stripe->charges->all(
            ['limit' => 1],
            ['api_key' => $stripeSecret]
        )['data'][0];

        // Assert that $50 was charged, and that the destination account for the charge is the promoter
        $this->assertEquals(5000, $lastStripeCharge['amount']);
        $this->assertEquals(env('STRIPE_TEST_PROMOTER_ID'), $lastStripeCharge['destination']);

        // Stripe has a Transfer Object that is created whenever we move funds between accounts using 
        // connect, see... https://stripe.com/docs/api/transfers/create?lang=php
        $transfer = \Stripe\Transfer::retrieve($lastStripeCharge['transfer'], $stripeSecret);
        $this->assertEquals(4500, $transfer['amount']);
    }
}