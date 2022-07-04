<?php

declare(strict_types=1);

namespace App\Models;

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

    /**
     *  @param Illuminate\Database\Eloquent\Builder $query
     *  @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable(Builder $query) : Builder
    {
        return $query->whereNull('order_id');
    }

    public function release() : void
    {
        $this->update(['order_id' => NULL]);
    }

    /**
     *  A computed property for getting the ticket price. Calling $ticket->price
     *  will run this.
     *  @return string
     */
    public function getPriceAttribute() : string
    {
        return $this->concert->ticket_price;
    }
}
