<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     *  Create an order and loop over the tickets the customer wants updating the blank
     *  ticket associating it with the users order.
     *  @param Illuminate\Database\Eloquent\Collection $tickets
     *  @param string $email
     *  @param int|null $amount
     *  @return App\Models\Order
     */
    public static function forTickets(Collection $tickets, string $email, int $amount = null) : Order
    {
        $order = self::create([
            'email' => $email,
            'amount' => $amount === null ? $tickets->sum('price') : $amount,
        ]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    public function cancel() : void
    {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }

        $this->delete();
    }

    public function ticketQuantity() : int
    {
        return $this->tickets()->count();
    }

    /**
     *  Override the default toArray method for the Order Model
     */
    public function toArray()
    {
        return [
            'email' => $this->email,
            'ticket_quantity' => $this->ticketQuantity(),
            'amount' => $this->amount
        ];
    }
}
