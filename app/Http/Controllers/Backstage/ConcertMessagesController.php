<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use App\Jobs\SendAttendeeMessage;
use Illuminate\Http\RedirectResponse;
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

    public function store(int $id) : RedirectResponse
    {
        $concert = Auth::user()->concerts()->findOrFail($id);

        $this->validate(request(), [
            'subject' => ['required'],
            'message' => ['required']
        ]);

        $message = $concert->attendeeMessages()->create([
            'subject' => request('subject'),
            'message' => request('message')
        ]);

        SendAttendeeMessage::dispatch($message);

        return redirect()->route('backstage.concert-messages.new', $concert)
            ->with('flash', "Your message has been sent");
    }
}
