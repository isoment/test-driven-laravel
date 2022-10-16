@extends('layouts.backstage')

@section('backstageContent')
<div class="bg-light p-xs-y-4 border-b">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center py-2">
            <div class="mb-0">
                <strong class="">{{ $concert->title }}</strong>
                <span class="ml-2">/</span>
                <span class="text-secondary">
                    {{ $concert->formatted_date }}
                </span>
            </div>
            <div class="text-base">
                <a href="{{ route('backstage.published-concert-orders.index', $concert) }}"
                   class="btn btn-primary">
                    Orders
                </a>
                <a href="#"
                   class="btn btn-primary ml-2">Message Attendees</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="#" method="POST">
            @csrf
            <div class="form-group {{ $errors->first('subject', 'has-error')}}">
                <label class="form-label">Subject</label>
                <input name="subject" class="form-control" value="{{ old('subject') }}">
                {{-- @if($errors->has('subject'))
                    <p class="help-block">
                        {{ $errors->first('subject') }}
                    </p>
                @endif --}}
            </div>
            <div class="form-group {{ $errors->first('message', 'has-error')}}">
                <label class="form-label">Message</label>
                <textarea class="form-control" name="message" rows="10">{{ old('message') }}</textarea>
                {{-- @if($errors->has('message'))
                    <p class="help-block">
                        {{ $errors->first('message') }}
                    </p>
                @endif --}}
            </div>
            <div>
                <button class="btn btn-primary">Send Now</button>
            </div>
        </form>
    </div>
</div>

@endsection