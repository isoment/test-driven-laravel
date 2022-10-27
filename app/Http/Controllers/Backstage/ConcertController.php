<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backstage;

use App\Events\ConcertAdded;
use App\Http\Controllers\Controller;
use App\Models\Concert;
use App\NullFile;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ConcertController extends Controller
{
    public function index() : View
    {
        $user = Auth::user();

        return view('backstage.concerts.list', [
            'publishedConcerts' => $user->concerts->filter->isPublished(),
            'unpublishedConcerts' => $user->concerts->reject->isPublished(),
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
            'poster_image' => ['nullable', 'image', Rule::dimensions()->minWidth(400)->ratio(8.5/11)],
        ]);

        $concert = Auth::user()->concerts()->create([
            'title' => $request['title'],
            'subtitle' => $request['subtitle'],
            'date' => Carbon::parse(vsprintf('%s %s', [
                $request['date'],
                $request['time']
            ])),
            'venue' => $request['venue'],
            'venue_address' => $request['venue_address'],
            'city' => $request['city'],
            'state' => $request['state'],
            'zip' => $request['zip'],
            'additional_information' => $request['additional_information'],
            'ticket_price' => $request['ticket_price'] * 100,
            'ticket_quantity' => (int) $request['ticket_quantity'],
            // Since this file is optional we can implement a store() method on the NullFile class
            // that will simply return null when we have no file.
            'poster_image_path' => request('poster_image', new NullFile)->store('posters', 'public')
        ]);

        ConcertAdded::dispatch($concert);

        return redirect()->route('backstage.concerts.index');
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

        abort_if($concert->isPublished(), 403);

        $concert->update([
            'title' => $request['title'],
            'subtitle' => $request['subtitle'],
            'date' => Carbon::parse(vsprintf('%s %s', [
                $request['date'],
                $request['time']
            ])),
            'venue' => $request['venue'],
            'venue_address' => $request['venue_address'],
            'city' => $request['city'],
            'state' => $request['state'],
            'zip' => $request['zip'],
            'additional_information' => $request['additional_information'],
            'ticket_price' => $request['ticket_price'] * 100,
            'ticket_quantity' => $request['ticket_quantity']
        ]);

        return redirect('/backstage/concerts');
    }
}
