<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function findByCode(string $code)
    {
        return self::where('code', $code)->first();
    }
}
