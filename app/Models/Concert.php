<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\NotEnoughTicketsException;
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

    /**
     *  The tickets table is a pivot. Many to many relationship between
     *  the Order and Concert models.
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'tickets');
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
     *  Order the tickets the user wants or throw an exception if the tickets
     *  are not available.
     *  @param string $email
     *  @param int $ticketQuantity
     *  @throws NotEnoughTicketsException
     *  @return App\Models\Order
     */
    public function orderTickets(string $email, int $ticketQuantity) : Order
    {
        $tickets = $this->findTickets($ticketQuantity);

        return $this->createOrder($email, $tickets);
    }

    /**
     *  @param int $quantity
     */
    public function reserveTickets(int $quantity) : Collection
    {
        return $this->findTickets($quantity)->each(function($ticket) {
            $ticket->reserve();
        });
    }

    /**
     *  We need to check if there are tickets available for the customer to order.
     *  If there are we will return a collection of the tickets.
     *  @param int $quantity
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
     *  Create an order by calling the forTickets() static method on the Order class.
     *  @param string $email
     *  @param Illuminate\Database\Eloquent\Collection $tickets
     *  @return App\Models\Order
     */
    public function createOrder(string $email, Collection $tickets) : Order
    {
        return Order::forTickets($tickets, $email, $tickets->sum('price'));
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
}
