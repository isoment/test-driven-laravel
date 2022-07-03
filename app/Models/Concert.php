<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'datetime'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     *  Display the date in the fromat of... 'December 13, 2016'
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
     *  An order is created with the buyers email. We get a collection of
     *  the ticket quantity the user wants and then iterate over it assigning
     *  each ticket to the order.
     *  @param string $email
     *  @param int $ticketQuantity
     *  @return App\Models\Order
     */
    public function orderTickets(string $email, int $ticketQuantity) : Order
    {
        $order = $this->orders()->create([
            'email' => $email
        ]);

        $tickets = $this->tickets()->take($ticketQuantity)->get();

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    /**
     *  Add tickets for customers to purchase, in our design a fixed number of
     *  ticket rows for a concert are created and when a ticket is ordered, an order
     *  id is assigned to the ticket. This method will add 'blank' tickets.
     *  @param int $quantity
     */
    public function addTickets(int $quantity)
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }
    }

    /**
     *  Get the remaining tickets
     */
    public function ticketsRemaining()
    {
        return $this->tickets()->whereNull('order_id')->count();
    }
}
