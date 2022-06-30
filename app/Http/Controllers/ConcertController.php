<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Concert;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ConcertController extends Controller
{
    public function show(int $id) : View
    {
        $concert = Concert::find($id);

        return view('concerts.show', [
            'concert' => $concert
        ]);
    }
}
