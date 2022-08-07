<div>
    <div class="row">
        <div class="col-md">
            <div class="d-flex justify-content-center align-items-start h-100">
                <div class="text-center mt-4">
                    <h1 class="font-weight-bold">{{ $concert->title }}</h2>
                    <h5 class="font-weight-light text-muted">{{ $concert->subtitle }}</h5>
                    <h2 class="font-weight-bold">${{ $concert->ticket_price_in_dollars }}</h2>
                    <p class="my-4">{{ $concert->additional_information }}</p>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="card-title text-dark font-weight-bold my-2">{{ $concert->venue }}</h3>
                        <div class="text-muted">
                            <h6>{{ $concert->venue_address }}</h6>
                            <h6>{{ $concert->city }}, {{ $concert->state }} {{$concert->zip}}</h6>
                        </div>
                    </div>
                    <div class="my-4 text-primary">
                        <h5>{{ $concert->formatted_date }}, <br/> Doors at {{ $concert->date->format('g:ia') }}</h5>
                    </div>
                    <div>
                        <ticket-checkout :concert-id="{{ $concert->id }}"
                                         concert-title="{{ $concert->title }}"
                                         :price="{{ $concert->ticket_price }}"
                        ></ticket-checkout>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>