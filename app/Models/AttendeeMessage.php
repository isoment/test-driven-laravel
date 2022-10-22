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

    public function orders()
    {
        return $this->concert->orders();
    }

    /**
     *  We chunk 20 orders at a time and execute the callback passing
     *  in the email from the order.
     *  @param int $chunkSize
     *  @param callable $callback
     *  @return void
     */
    public function withChunkedRecipients(int $chunkSize, callable $callback) : void
    {
        $this->orders()->chunk($chunkSize, function($orders) use($callback) {
            $callback($orders->pluck('email'));
        });
    }
}
