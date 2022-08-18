<?php

declare(strict_types=1);

namespace App\Models;

use App\Billing\Charge;
use App\Facades\OrderConfirmationNumber;
use App\OrderConfirmationNumberGenerator;
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
     *  ticket thereby associating it with the users order.
     *  @param Illuminate\Database\Eloquent\Collection $tickets
     *  @param string $email
     *  @param App\Billing\Charge $charge
     *  @return self
     */
    public static function forTickets(Collection $tickets, string $email, Charge $charge) : self
    {
        $order = self::create([
            'confirmation_number' => OrderConfirmationNumber::generate(),
            'email' => $email,
            'amount' => $charge->amount(),
            'card_last_four' => $charge->cardLastFour()
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
            'confirmation_number' => $this->confirmation_number,
            'email' => $this->email,
            'amount' => $this->amount,
            'tickets' => $this->tickets->map(function($ticket) {
                return ['code' => $ticket->code];
            })->all(),
        ];
    }
}
