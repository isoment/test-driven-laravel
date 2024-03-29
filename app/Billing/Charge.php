<?php

declare(strict_types=1);

namespace App\Billing;

class Charge 
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function cardLastFour() : string
    {
        return $this->data['card_last_four'];
    }

    public function amount() : int
    {
        return $this->data['amount'];
    }

    public function destination() : string
    {
        return $this->data['destination'];
    }
}