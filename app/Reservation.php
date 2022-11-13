<?php

declare(strict_types=1);

namespace App;

use App\Billing\PaymentGateway;
use App\Models\Order;
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

    /**
     *  @return SupportCollection|Collection
     */
    public function tickets() : SupportCollection|Collection
    {
        return $this->tickets;
    }

    public function email() : string
    {
        return $this->email;
    }

    /**
     *  In order to complete the reservation we charge the customer and create
     *  an order for the tickets.
     *  @param App\Billing\PaymentGateway $paymentGateway
     *  @param string $paymentToken
     *  @param string $destinationAccountId
     *  @return App\Models\Order
     */
    public function complete(PaymentGateway $paymentGateway, string $paymentToken, string $destinationAccountId) : Order
    {
        $charge = $paymentGateway->charge(
            $this->totalCost(), 
            $paymentToken,
            $destinationAccountId
        );

        return Order::forTickets(
            $this->tickets(), 
            $this->email(), 
            $charge
        );
    }

    public function cancel() : void
    {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }
    }
}