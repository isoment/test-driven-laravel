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

    public function getValidTestToken() : string
    {
        return 'Valid Token';
    }

    public function charge($amount, $token) : void
    {
        $this->charges[] = $amount;
    }

    public function totalCharges()
    {
        return $this->charges->sum();
    }
}