@extends('layouts.master')

@section('body')
    <div class="full-height mt-4">
        <div class="container">
            {{-- @if ($concert->hasPoster())
                @include('concerts.partials.card-with-poster', ['concert' => $concert])
            @else
                @include('concerts.partials.card-no-poster', ['concert' => $concert])
            @endif --}}
            @include('concerts.partials.card-no-poster', ['concert' => $concert])
            <div class="mt-4">
                <p class="font-weight-bold text-black">Powered by MyTicket</p>
            </div>
        </div>
    </div>
@endsection

@push('beforeScripts')
    <script src="https://checkout.stripe.com/checkout.js"></script>
@endpush

{{-- <h1>{{ $concert->title }}</h1>
<h2>{{ $concert->subtitle }}</h2>
<p>{{ $concert->formatted_date }}</p>
<p>{{ $concert->formatted_start_time }}</p>
<p>Doors at {{ $concert->date->format('g:ia') }}</p>
<p>{{ $concert->ticket_price_in_dollars }}</p>
<p>{{ $concert->venue }}</p>
<p>{{ $concert->venue_address }}</p>
<p>{{ $concert->city }}, {{ $concert->state }} {{$concert->zip}}</p>
<p>{{ $concert->additional_information }}</p> --}}