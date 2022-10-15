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
            </div>
        </div>
    </div>
</div>
<div class="bg-soft p-xs-y-5">
    <div class="container m-xs-b-4">
        <div class="m-xs-b-6">
            <h5 class="mt-5 mb-2 font-weight-light text-secondary">Overview</h5>
            <div class="card">
                <div class="card-body">
                    <div class="pb-2">
                        <p class="mb-1">This show is {{ $concert->percentSoldOut() }}% sold out.</p>
                        <progress class="progress w-100" 
                                  value="{{ $concert->ticketsSold() }}" 
                                  max="{{ $concert->totalTickets() }}">
                            {{ $concert->percentSoldOut() }}%
                        </progress>
                    </div>
                    <hr>
                    <div class="row mt-4">
                        <div class="col col-md-4">
                            <div class="card-section p-md-r-2 text-center text-md-left">
                                <h3 class="text-base wt-normal m-xs-b-1">Total Tickets Remaining</h3>
                                <h2 class="font-weight-bold">
                                    {{ $concert->ticketsRemaining() }}
                                </h2>
                            </div>
                        </div>
                        <div class="col col-md-4">
                            <div class="card-section p-md-x-2 text-center text-md-left">
                                <h3 class="text-base wt-normal m-xs-b-1">Total Tickets Sold</h3>
                                <h2 class="font-weight-bold">
                                    {{ $concert->ticketsSold() }}
                                </h2>
                            </div>
                        </div>
                        <div class="col col-md-4">
                            <div class="card-section p-md-l-2 text-center text-md-left">
                                <h3 class="text-base wt-normal m-xs-b-1">Total Revenue</h3>
                                <h2 class="font-weight-bold">
                                    $10,555
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div>
            <h2 class="m-xs-b-2 text-lg">Recent Orders</h2>
            <div class="card">
                <div class="card-section">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="text-left">Email</th>
                            <th class="text-left">Tickets</th>
                            <th class="text-left">Amount</th>
                            <th class="text-left">Card</th>
                            <th class="text-left">Purchased</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                            <?php /** @var \App\Order $order */ ?>
                            <tr>
                                <td>{{ $order->email }}</td>
                                <td>{{ $order->ticketQuantity() }}</td>
                                <td>${{ number_format($order->amount / 100, 2) }}</td>
                                <td><span class="text-dark-soft">****</span> {{ $order->card_last_four }}</td>
                                <td class="text-dark-soft">{{ $order->created_at->format('M j, Y @ g:ia') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div> --}}
    </div>
</div>
@endsection