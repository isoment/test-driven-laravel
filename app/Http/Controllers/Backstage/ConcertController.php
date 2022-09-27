<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ConcertController extends Controller
{
    public function create() : View
    {
        return view('backstage.concerts.create');
    }
}
