@extends('layouts.master')

@section('body')
    <div class="container">
        <div>
            <div class="card mt-4 mx-auto" style="max-width: 35rem">
                <h4 class="card-header">Join TicketBeast</h4>
                <div class="card-body">
                    <form action="/register" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="invitation_code" value="{{ $invitation->code }}">
                        <div class="form-group {{ $errors->first('email', 'has-error') }}">
                            <label>Email address</label>
                            <input type="email" name="email" class="form-control" placeholder="Email address"
                                    value="{{ old('email') }}">
                            @if ($errors->has('email'))
                                <p class="text-danger m-xs-t-2">{{ $errors->first('email') }}</p>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->first('password', 'has-error') }}">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Password">
                            @if ($errors->has('password'))
                                <p class="text-danger m-xs-t-2">{{ $errors->first('password') }}</p>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-block btn-primary">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection