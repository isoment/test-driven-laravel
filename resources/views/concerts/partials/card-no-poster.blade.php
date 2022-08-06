<div>
    <div class="row">
        <div class="col-md">
            <div class="d-flex justify-content-center align-items-center h-100">
                <div class="text-center">
                    <h1 class="font-weight-bold">{{ $concert->title }}</h2>
                    <h5 class="font-weight-light text-muted">{{ $concert->subtitle }}</h5>
                    <p class="my-4">{{ $concert->additional_information }}</p>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-dark font-weight-bold">{{ $concert->venue }}</h3>
                    <div class="my-4 text-primary">
                        <h5>{{ $concert->formatted_date }}</h5>
                        <h5>{{ $concert->formatted_start_time }}</h5>
                        <h5>Doors at {{ $concert->date->format('g:ia') }}</h5>
                    </div>
                    <div class="text-muted">
                        <h6>{{ $concert->venue_address }}</h6>
                        <h6>{{ $concert->city }}, {{ $concert->state }} {{$concert->zip}}</h6>
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