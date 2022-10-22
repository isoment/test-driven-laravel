<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendeeMessage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    /**
     *  @return Illuminate\Support\Collection
     */
    public function recipients() : Collection
    {
        return $this->concert->orders()->pluck('email');
    }
}
