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
        </div>
    </div>
@endsection