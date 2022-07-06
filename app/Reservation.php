<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class Reservation
{
    private SupportCollection|Collection $tickets;

    /**
     *  We need to set this as a union type since for the test we are stubbing the eloquent
     *  collection with just a plain collection
     *  @param SupportCollection|Collection
     */
    public function __construct(SupportCollection|Collection $tickets)
    {
        $this->tickets = $tickets;
    }

    public function totalCost()
    {
        return $this->tickets->sum('price');
    }
}