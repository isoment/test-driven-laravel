<?php

declare(strict_types=1);

namespace App\Billing;

use Illuminate\Support\Collection;

class FakePaymentGateway implements PaymentGateway
{
    private Collection $charges;
    private $beforeFirstChargeCallback;

    public function __construct()
    {
        $this->charges = collect();
    }

    /**
     *  A dummy test token
     *  @return string
     */
    public function getValidTestToken() : string
    {
        return 'Valid Token';
    }

    /**
     *  Add a charge to the collection
     *  @param int $amount
     *  @param string $token
     *  @return void
     */
    public function charge(int $amount, string $token) : void
    {
        /*
            To avoid the callback being called infinitely we can assign it to a
            variable $callback and call this instead of the beforeFirstChargeCallback
            property of this class.
        */
        if ($this->beforeFirstChargeCallback !== NULL) {
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = NULL;
            $callback($this);
        }

        if ($token !== $this->getValidTestToken()) {
            throw new PaymentFailedException;
        }

        $this->charges[] = $amount;
    }

    /**
     *  Sum the individual charges
     *  @return int
     */
    public function totalCharges() : int
    {
        return $this->charges->sum();
    }

    /**
     *  A hook to call some logic before we charge a customer. We can use this
     *  to initiate a request.
     *  @param callable $callback
     */
    public function beforeFirstCharge(callable $callback)
    {
        $this->beforeFirstChargeCallback = $callback;
    }
}