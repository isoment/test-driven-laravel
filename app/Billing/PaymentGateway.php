<?php

declare(strict_types=1);

namespace App\Billing;

interface PaymentGateway
{
    public function charge(int $amount, string $token);

    public function getValidTestToken() : string;

    public function newChargesDuring(callable $callback);
}