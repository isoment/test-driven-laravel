<?php

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    use RefreshDatabase;
    use PaymentGatewayContractTests;

    protected function getPaymentGateway()
    {
        return new FakePaymentGateway;
    }

    /**
     *  @test
     *  We need some way to make a sub-request so that we can test instances where there
     *  are multiple users trying to book the same tickets. We can create a hook in the
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