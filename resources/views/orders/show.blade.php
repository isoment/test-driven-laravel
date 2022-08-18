@extends('layouts.master')

@section('body')
    <div class="container">
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <h3>Order Summary</h3>
            <a class="text-secondary" 
               href="{{ url("/orders/{$order->confirmation_number}") }}">
               {{$order->confirmation_number}}
            </a>
        </div>
        <hr>
        <div class="my-2">
            <h6 class="font-weight-bold">Order Total: ${{ number_format($order->amount / 100, 2) }}</h6>
            <p class="text-secondary">Billed to Card #: **** **** **** {{ $order->card_last_four }}</p>
        </div>
        <hr>
        <h4 class="mt-5 mb-3">Your Tickets</h4>
        {{-- Tickets --}}
        @foreach ($order->tickets as $ticket)
            <div class="card my-4">
                <div class="card-header bg-secondary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-left">
                        <h4 class="mb-0">{{ $ticket->concert->title }}</h4>
                        <p class="mb-0">{{ $ticket->concert->subtitle }}</p>
                    </div>
                    <div class="text-right">
                        <h6 class="font-weight-bold mb-0">General Admission</h6>
                        <p class="mb-0">Admit One</p>
                    </div>
                </div>
                </div>
                <div class="card-body d-flex justify-content-around align-items-start">
                    <div>
                        <h4 class="mb-3">{{ $ticket->concert->venue }}</h4>
                        <p class="mb-0">{{ $ticket->concert->venue_address }}</p>
                        <p>{{ $ticket->concert->city }}, {{ $ticket->concert->state }} {{ $ticket->concert->zip }}</p>
                    </div>
                    <div>
                        <time datetime="{{ $ticket->concert->date->format('Y-m-d H:i') }}">
                            {{ $ticket->concert->date->format('l, F j, Y') }}
                        </time>
                        <p>Doors at {{ $ticket->concert->date->format('g:ia') }}</p>
                    </div>
                </div>
                <div class="card-footer text-muted d-flex justify-content-between align-items-center">
                <div>
                    {{ $ticket->code }}
                </div>
                <div>
                    {{ $ticket->order->email }}
                </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection