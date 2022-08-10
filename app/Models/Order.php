<?php

declare(strict_types=1);

namespace App\Models;

use App\Reservation;
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
     *  @return self
     */
    public static function forTickets(Collection $tickets, string $email, int $amount = null) : self
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

    public static function findByConfirmationNumber($confirmationNumber)
    {
        return self::where('confirmation_number', $confirmationNumber)
            ->firstOrFail();
    }

    public function ticketQuantity() : int
    {
        return $this->tickets()->count();
    }

    /**
     *  Override the default toArray method for the Order Model
     */
    public function toArray() : array
    {
        return [
            'email' => $this->email,
            'ticket_quantity' => $this->ticketQuantity(),
            'amount' => $this->amount
        ];
    }
}
