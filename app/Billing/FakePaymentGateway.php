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
        return 'valid-token';
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
     *  We are passing in a callback where we are performing some charges. We only want
     *  to return the charges performed in the callback. We can slice the charges collection
     *  using the count of the charges before the callback. This returns only the charges
     *  done in the callback. We need to call values() on the collection since slice() will
     *  not rekey the new collection. We also want to reverse it since this is how Stripe
     *  is returning the charges, newest first.
     *  @param callable $callback
     *  @return Illuminate\Support\Collection
     */
    public function newChargesDuring(callable $callback) : Collection
    {
        $chargesFrom = $this->charges->count();

        $callback();

        return $this->charges->slice($chargesFrom)->reverse()->values();
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
     *  to initiate a request for testing.
     *  @param callable $callback
     */
    public function beforeFirstCharge(callable $callback) : void
    {
        $this->beforeFirstChargeCallback = $callback;
    }
}