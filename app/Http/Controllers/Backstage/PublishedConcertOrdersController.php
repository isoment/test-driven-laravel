<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use App\Models\Concert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PublishedConcertOrdersController extends Controller
{
    public function index(int $concertId) : View
    {
        $concert = Auth::user()->concerts()
            ->published()
            ->findOrFail($concertId);

        return view('backstage.published-concert-orders.list', [
            'concert' => $concert
        ]);
    }
}
