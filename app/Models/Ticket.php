<?php

declare(strict_types=1);

namespace App\Models;

use App\Facades\TicketCode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     *  @param Illuminate\Database\Eloquent\Builder $query
     *  @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable(Builder $query) : Builder
    {
        return $query->whereNull('order_id')->whereNull('reserved_at');
    }

    /**
     *  @param Illuminate\Database\Eloquent\Builder $query
     *  @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeSold(Builder $query) : Builder
    {
        return $query->whereNotNull('order_id');
    }

    public function reserve() : void
    {
        $this->update(['reserved_at' => Carbon::now()]);
    }

    public function release() : void
    {
        $this->update(['reserved_at' => NULL]);
    }

    public function claimFor(Order $order) : void
    {
        $this->code = TicketCode::generateFor($this);
        $order->tickets()->save($this);
    }

    /**
     *  A computed property for getting the ticket price. Calling $ticket->price
     *  will run this.
     */
    public function getPriceAttribute()
    {
        return $this->concert->ticket_price;
    }
}
