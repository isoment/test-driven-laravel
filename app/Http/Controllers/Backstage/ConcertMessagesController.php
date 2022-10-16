<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ConcertMessagesController extends Controller
{
    public function create(int $id) : View
    {
        $concert = Auth::user()->concerts()->findOrFail($id);

        return view('backstage.concert-messages.new', [
            'concert' => $concert
        ]);
    }

    public function store(int $id)
    {
        $concert = Auth::user()->concerts()->findOrFail($id);

        $message = $concert->attendeeMessages()->create([
            'subject' => request('subject'),
            'message' => request('message')
        ]);

        return redirect()->route('backstage.concert-messages.new', $concert)
            ->with('flash', "Your message has been sent");
    }
}
