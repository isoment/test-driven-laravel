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
     *  @doesNotPerformAssertions
     *  When the payment token is invalid we will expect an exception to be thrown
     *  and we will return. If no exception is thrown the test will fail. This test
     *  does not assert anything so we can add @doesNotPerformAssertions
     */
    public function charges_with_an_invalid_payment_token_fail()
    {
        try {
            $paymentGateway = new FakePaymentGateway;
            $paymentGateway->charge(2500, 'invalid-payment-token');
        } catch(PaymentFailedException $e) {
            return;
        }

        $this->fail();
    }

    /**
     *  @test
     *  We need some way to make a sub-request so that we can test instances where there
     *  are multiple users trying to book the sam tickets. We can create a hook in the
     *  fake payment gateway.
     */
    public function running_a_hook_before_the_first_charge()
    {
        $paymentGateway = new FakePaymentGateway;
        $timesCallbackRan = 0;

        /*
            We want to have a beforeFirstCharge method on the payment gateway that accepts
            a callback. Within the callback we assert that the total charges are 0.
        */
        $paymentGateway->beforeFirstCharge(function($paymentGateway) use(&$timesCallbackRan) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
            $timesCallbackRan++;
            $this->assertEquals(2500, $paymentGateway->totalCharges());
        });

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        $this->assertEquals(1, $timesCallbackRan);
        $this->assertEquals(5000, $paymentGateway->totalCharges());
    }
}