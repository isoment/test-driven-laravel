<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use App\Models\Concert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublishedConcertController extends Controller
{
    public function store(Request $request) : RedirectResponse
    {
        $concert = Auth::user()->concerts()->findOrFail($request['concert_id']);

        if ($concert->isPublished()) {
            abort(422);
        }

        $concert->publish();

        return redirect()->route('backstage.concerts.index');
    }
}
