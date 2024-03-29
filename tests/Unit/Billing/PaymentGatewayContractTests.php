<?php

namespace Tests\Unit\Billing;

use App\Billing\PaymentFailedException;

use function PHPUnit\Framework\assertEquals;

trait PaymentGatewayContractTests
{
    abstract protected function getPaymentGateway();

    /**
     *  @test
     */
    public function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = $this->getPaymentGateway();

        // Create a new charge for an amount using a valid token
        $newCharges = $paymentGateway->newChargesDuring(function() use($paymentGateway) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken(), env('STRIPE_TEST_PROMOTER_ID'));
        });

        // Assert that there is a new charge created
        $this->assertCount(1, $newCharges);

        // Assert that the most recent charge is for the correct amount
        $this->assertEquals(2500, $newCharges->map->amount()->sum());
    }

    /**
     *  @test
     */
    public function can_get_details_about_a_successful_charge()
    {
        $paymentGateway = $this->getPaymentGateway();

        $charge = $paymentGateway->charge(
            2500, 
            $paymentGateway->getValidTestToken($paymentGateway::TEST_CARD_NUMBER),
            env('STRIPE_TEST_PROMOTER_ID')
        );

        $this->assertEquals(
            substr($paymentGateway::TEST_CARD_NUMBER, -4), 
            $charge->cardLastFour()
        );
        $this->assertEquals(2500, $charge->amount());
        $this->assertEquals(env('STRIPE_TEST_PROMOTER_ID'), $charge->destination());
    }

    /**
     *  @test
     *  When the payment token is invalid we will expect an exception to be thrown
     *  and we will return. If no exception is thrown the test will fail.
     */
    public function charges_with_an_invalid_payment_token_fail()
    {
        $paymentGateway = $this->getPaymentGateway();

        $newCharges = $paymentGateway->newChargesDuring(function() use($paymentGateway) {
            try {
                $paymentGateway->charge(2500, 'invalid-payment-token', env('STRIPE_TEST_PROMOTER_ID'));
            } catch(PaymentFailedException $e) {
                return;
            }

            $this->fail("Charging with an invalid payment token did not throw a PaymentFailedException");
        });

        $this->assertCount(0, $newCharges);
    }

    /**
     *  @test
     */
    public function can_fetch_charges_created_during_a_callback()
    {
        $paymentGateway = $this->getPaymentGateway();

        $paymentGateway->charge(2000, $paymentGateway->getValidTestToken(), env('STRIPE_TEST_PROMOTER_ID'));
        $paymentGateway->charge(3000, $paymentGateway->getValidTestToken(), env('STRIPE_TEST_PROMOTER_ID'));

        $newCharges = $paymentGateway->newChargesDuring(function() use($paymentGateway) {
            $paymentGateway->charge(4000, $paymentGateway->getValidTestToken(), env('STRIPE_TEST_PROMOTER_ID'));
            $paymentGateway->charge(5000, $paymentGateway->getValidTestToken(), env('STRIPE_TEST_PROMOTER_ID'));
        });

        $this->assertCount(2, $newCharges);
        $this->assertEquals([5000, 4000], $newCharges->map->amount()->all());
    }
}