<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use App\Models\Concert;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConcertController extends Controller
{
    public function index() : View
    {
        $user = Auth::user();

        return view('backstage.concerts.list', [
            'concerts' => $user->concerts
        ]);
    }

    public function create() : View
    {
        return view('backstage.concerts.create');
    }

    public function store(Request $request) : RedirectResponse
    {
        $request->validate([
            'title' => ['required'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:g:ia'],
            'venue' => ['required'],
            'venue_address' => ['required'],
            'city' => ['required'],
            'state' => ['required'],
            'zip' => ['required'],
            'ticket_price' => ['required', 'numeric', 'min:5'],
            'ticket_quantity' => ['required', 'numeric', 'min:1'],
        ]);

        $concert = Auth::user()->concerts()->create([
            'title' => $request['title'],
            'subtitle' => $request['subtitle'],
            'date' => Carbon::parse(vsprintf('%s %s', [
                $request['date'],
                $request['time']
            ])),
            'ticket_price' => $request['ticket_price'] * 100,
            'venue' => $request['venue'],
            'venue_address' => $request['venue_address'],
            'city' => $request['city'],
            'state' => $request['state'],
            'zip' => $request['zip'],
            'additional_information' => $request['additional_information']
        ]);

        $concert->addTickets((int) $request['ticket_quantity']);

        $concert->publish();

        return redirect()->route('concerts.show', ['id' => $concert->id]);
    }

    public function edit(int $id) : View
    {
        $concert = Auth::user()->concerts()->findOrFail($id);

        abort_if($concert->isPublished(), 403);

        return view('backstage.concerts.edit', [
            'concert' => $concert
        ]);
    }

    public function update(Request $request, int $id) : RedirectResponse
    {
        $request->validate([
            'title' => ['required'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:g:ia'],
            'venue' => ['required'],
            'venue_address' => ['required'],
            'city' => ['required'],
            'state' => ['required'],
            'zip' => ['required'],
            'ticket_price' => ['required', 'numeric', 'min:5'],
            'ticket_quantity' => ['required', 'numeric', 'min:1'],
        ]);

        $concert = Auth::user()->concerts()->findOrFail($id);

        $concert->update([
            'title' => $request['title'],
            'subtitle' => $request['subtitle'],
            'date' => Carbon::parse(vsprintf('%s %s', [
                $request['date'],
                $request['time']
            ])),
            'ticket_price' => $request['ticket_price'] * 100,
            'venue' => $request['venue'],
            'venue_address' => $request['venue_address'],
            'city' => $request['city'],
            'state' => $request['state'],
            'zip' => $request['zip'],
            'additional_information' => $request['additional_information']
        ]);

        return redirect('/backstage/concerts');
    }
}
