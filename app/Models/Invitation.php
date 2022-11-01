<?php

declare(strict_types=1);

namespace App\Models;

use App\Mail\InvitationEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Invitation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     *  @param string $code
     *  @return self
     */
    public static function findByCode(string $code) : self
    {
        return self::where('code', $code)->firstOrFail();
    }

    /**
     *  @return bool
     */
    public function hasBeenUsed() : bool
    {
        return $this->user_id !== NULL;
    }

    public function send() : void
    {
        Mail::to($this->email)->send(new InvitationEmail($this));
    }
}
