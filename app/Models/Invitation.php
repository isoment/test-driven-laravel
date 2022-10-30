<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $guarded = [];

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
}
