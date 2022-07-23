<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class Reservation
{
    private SupportCollection|Collection $tickets;
    private string $email;

    /**
     *  We need to set this as a union type since for the test we are stubbing the eloquent
     *  collection with just a plain collection
     *  @param SupportCollection|Collection
     */
    public function __construct(SupportCollection|Collection $tickets, string $email)
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    public function totalCost() : int
    {
        return $this->tickets->sum('price');
    }

    public function tickets() : SupportCollection|Collection
    {
        return $this->tickets;
    }

    public function email() : string
    {
        return $this->email;
    }

    public function cancel() : void
    {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }
    }
}