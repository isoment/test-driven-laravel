<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Billing\PaymentFailedException;

class FakePaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }

    /**
     *  @test
     */
    public function charges_with_an_invalid_payment_token_fail()
    {
        /*
            When the payment token is invalid we will expect an exception to be thrown
            and we will return. If no exception is thrown the test will fail.
        */
        try {
            $paymentGateway = new FakePaymentGateway;
            $paymentGateway->charge(2500, 'invalid-payment-token');
        } catch(PaymentFailedException $e) {
            return;
        }

        $this->fail();
    }
}