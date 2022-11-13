<?php

declare(strict_types=1);

namespace App\Billing;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FakePaymentGateway implements PaymentGateway
{
    const TEST_CARD_NUMBER = '4242424242424242';

    private Collection $charges;
    private Collection $tokens;
    private $beforeFirstChargeCallback;

    public function __construct()
    {
        $this->charges = collect();
        $this->tokens = collect();
    }

    /**
     *  We provide a fake card number and get back a fake token
     *  In our tokens collection the token will be the key and the card number
     *  will be the value.
     *  @param string $cardNumber
     *  @return string
     */
    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER) : string
    {
        $token = 'fake-tok_' . Str::random(24);
        $this->tokens[$token] = $cardNumber;
        return $token;
    }

    /**
     *  Add a charge to the collection
     *  @param int $amount
     *  @param string $token
     *  @param string $destinationAccountId
     *  @return App\Billing\Charge
     */
    public function charge(int $amount, string $token, string $destinationAccountId) : Charge
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

        if (!$this->tokens->has($token)) {
            throw new PaymentFailedException;
        }

        return $this->charges[] = new Charge([
            'amount' => $amount,
            'card_last_four' => substr($this->tokens[$token], -4),
            'destination' => $destinationAccountId
        ]);
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
        return $this->charges->map->amount()->sum();
    }

    /**
     *  Get the total charges for a given account
     *  @param string $accountId
     *  @return int
     */
    public function totalChargesFor(string $accountId) : int
    {
        return $this->charges->filter(function($charge) use($accountId) {
            return $charge->destination() === $accountId;
        })->map->amount()->sum();
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