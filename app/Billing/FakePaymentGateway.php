<?php

declare(strict_types=1);

namespace App\Billing;

use Illuminate\Support\Collection;

class FakePaymentGateway implements PaymentGateway
{
    private Collection $charges;

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
}