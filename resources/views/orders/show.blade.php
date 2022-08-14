@extends('layouts.master')

@section('body')
    <div class="container">
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <h3>Order Summary</h3>
            <h6 class="text-secondary">{{$order->confirmation_number}}</h6>
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
            <div class="card">
                <div class="card-header bg-secondary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-left">
                        <h4 class="mb-0">Concert Title</h4>
                        <p class="mb-0">Opening acts</p>
                    </div>
                    <div class="text-right">
                        <h6 class="font-weight-bold mb-0">General Admission</h6>
                        <p class="mb-0">Admit One</p>
                    </div>
                </div>
                </div>
                <div class="card-body d-flex justify-content-around align-items-start">
                    <div>
                        <h4 class="mb-3">Music Hall of Sound</h4>
                        <p class="mb-0">123 Main St. W</p>
                        <p>City, State 47678</p>
                    </div>
                    <div>
                        Sunday, September 15, 2022
                        <p>Doors ar 8:00PM</p>
                    </div>
                </div>
                <div class="card-footer text-muted d-flex justify-content-between align-items-center">
                <div>
                    {{ $ticket->code }}
                </div>
                <div>
                    test@example.com
                </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection