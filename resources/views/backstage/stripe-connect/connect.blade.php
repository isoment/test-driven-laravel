@extends('layouts.master')

@section('body')
    <div class="container">
        <div class="mt-5 d-flex justify-content-center">
            <div class="card" style="max-width: 35rem">
                <div class="card-body">
                    <h3 class="text-center">
                        Connect your Stripe account
                    </h3>
                    <p class="mt-2">
                        Good news, TicketBeast now integrates directly with your Stripe account!
                        To continue, connect your Stripe account by clicking the button below:
                    </p>
                    <div>
                        <a href="{{ route('backstage.stripe-connect.authorize') }}" 
                           class="btn btn-block btn-primary">
                            Connect with Stripe
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection