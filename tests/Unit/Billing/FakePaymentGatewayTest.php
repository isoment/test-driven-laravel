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
     */
    public function can_get_total_charges_for_a_specific_account()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(1000, $paymentGateway->getValidTestToken(), 'test_account_0000');
        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken(), 'test_account_1234');
        $paymentGateway->charge(4000, $paymentGateway->getValidTestToken(), 'test_account_1234');

        $this->assertEquals(6500, $paymentGateway->totalChargesFor('test_account_1234'));
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