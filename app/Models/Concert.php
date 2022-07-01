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
}
