<?php

namespace App;

use Illuminate\Support\Str;

class RandomOrderConfirmationNumberGenerator implements OrderConfirmationNumberGenerator
{
    public function generate() : string
    {
        $pool = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 24)), 0, 24);
    }
}