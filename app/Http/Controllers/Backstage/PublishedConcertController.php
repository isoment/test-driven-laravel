<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use App\Models\Concert;
use Illuminate\Http\Request;

class PublishedConcertController extends Controller
{
    public function store(Request $request)
    {
        $concert = Concert::find($request['concert_id']);

        if ($concert->isPublished()) {
            abort(422);
        }

        $concert->publish();

        return redirect()->route('backstage.concerts.index');
    }
}
