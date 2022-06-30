<?php

declare(strict_types=1);

namespace App\Models;

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
     *  A computed property. We can call $concert->formatted_date to run this.
     *  @return string
     */
    public function getFormattedDateAttribute() : string
    {
        return $this->date->format('F j, Y');
    }

    public function getFormattedStartTimeAttribute() : string
    {
        return $this->date->format('g:ia');
    }

    public function getTicketPriceInDollarsAttribute()
    {
        return number_format($this->ticket_price / 100, 2);
    }
}
