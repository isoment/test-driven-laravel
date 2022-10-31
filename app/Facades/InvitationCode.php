<?php

declare(strict_types=1);

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\InvitationCodeGenerator;

class InvitationCode extends Facade
{
    protected static function getFacadeAccessor()
    {
        return InvitationCodeGenerator::class;
    }
}