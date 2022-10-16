<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\NotEnoughTicketsException;
use App\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendeeMessages()
    {
        return $this->hasMany(AttendeeMessage::class);
    }

    /**
     *  Get the orders for a concert. Get a collection of order_id's
     *  from  all the tickets belonging to this concert.
     */
    public function orders()
    {
        return Order::whereIn('id', $this->tickets()->pluck('order_id'));
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     *  Determine if the concert has an order from the customer.
     *  @param string $customerEmail
     *  @return bool
     */
    public function hasOrderFor(string $customerEmail) : bool
    {
        return $this->orders()->where('email', $customerEmail)->count() > 0;
    }

    /**
     *  @param string $customerEmail
     *  @return Illuminate\Database\Eloquent\Collection
     */
    public function ordersFor(string $customerEmail) : Collection
    {
        return $this->orders()->where('email', $customerEmail)->get();
    }

    /**
     *  Display the date in the format of... 'December 13, 2016'
     *  A computed property. We can call $concert->formatted_date now.
     *  @return string
     */
    public function getFormattedDateAttribute() : string
    {
        return $this->date->format('F j, Y');
    }

    /**
     *  Display the time in the format of... '8:00pm'
     *  @return string
     */
    public function getFormattedStartTimeAttribute() : string
    {
        return $this->date->format('g:ia');
    }

    /**
     *  Display the ticket price formatted... '65.76'
     *  @return string
     */
    public function getTicketPriceInDollarsAttribute() : string
    {
        return number_format($this->ticket_price / 100, 2);
    }

    /**
     *  Query scope to return published concerts
     *  @param Illuminate\Database\Eloquent\Builder $query
     *  @return Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished(Builder $query) : Builder
    {
        return $query->whereNotNull('published_at');
    }

    /**
     *  @return bool
     */
    public function isPublished() : bool
    {
        return $this->published_at !== NULL;
    }

    /**
     *  @return void
     */
    public function publish() : void
    {
        $this->update(['published_at' => Carbon::now()]);
        $this->addTickets((int) $this->ticket_quantity);
    }

    /**
     *  @param int $quantity
     *  @param string $email
     *  @return App\Reservation
     */
    public function reserveTickets(int $quantity, string $email) : Reservation
    {
        $tickets =  $this->findTickets($quantity)->each(function($ticket) {
            $ticket->reserve();
        });

        return new Reservation($tickets, $email);
    }

    /**
     *  We need to check if there are tickets available for the customer to order.
     *  If there are we will return a collection of the tickets.
     *  @param int $quantity
     *  @throws App\Exceptions\NotEnoughTicketsException
     *  @return Illuminate\Database\Eloquent\Collection
     */
    public function findTickets(int $quantity) : Collection
    {
        $tickets = $this->tickets()->available()->take($quantity)->get();

        if ($tickets->count() < $quantity) {
            throw new NotEnoughTicketsException;
        }

        return $tickets;
    }

    /**
     *  Add tickets for customers to purchase, in our design a fixed number of
     *  ticket rows for a concert are created and when a ticket is ordered, an order
     *  id is assigned to the ticket. This method will add 'blank' tickets. We also want
     *  to return the model so we can chain it.
     *  @param int $quantity
     *  @return App\Models\Concert
     */
    public function addTickets(int $quantity) : Concert
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }

        return $this;
    }

    /**
     *  Get the remaining tickets
     *  @return int
     */
    public function ticketsRemaining() : int
    {
        return $this->tickets()->available()->count();
    }

    /**
     *  Get the quantity of tickets that have been sold
     *  @return int
     */
    public function ticketsSold() : int
    {
        return $this->tickets()->sold()->count();
    }

    /**
     *  Get the total amount of tickets for the concert
     *  @return int
     */
    public function totalTickets() : int
    {
        return $this->tickets()->count();
    }

    /**
     *  Get the percentage of tickets that have been sold out
     *  @return string
     */
    public function percentSoldOut() : string
    {
        return number_format(($this->ticketsSold() / $this->totalTickets()) * 100, 2);
    }

    /**
     *  Get the concerts revenue in dollars
     *  @return int|float
     */
    public function revenueInDollars() : int|float
    {
        return $this->orders()->sum('amount') / 100;
    }
}
