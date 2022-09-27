@extends('layouts.backstage')

@section('backstageContent')
    <div>
        <div class="bg-light mb-4 py-3">
            <div class="container">
                <h3 class="mb-0 text-success font-weight-bold">Add a concert</h3>
            </div>
        </div>
        <form action="/backstage/concerts" method="POST">
            <div>
                <div class="container">
                    <div class="row">
                        <div class="col col-lg-4">
                            <div>
                                <h2>Concert Details</h2>
                                <p>Tell us who's playing! <em>(Please be
                                        Slayer!)</em></p>
                                <p>Include the headliner in the concert name, use the
                                    subtitle section to list any opening bands, and add any important information to the
                                    description.</p>
                            </div>
                        </div>
                        <div class="col col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="form-label">Title</label>
                                        <input name="title" class="form-control" value="{{ old('title') }}"
                                               placeholder="The Headliners">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Subtitle</label>
                                        <input name="subtitle" class="form-control" value="{{ old('subtitle') }}"
                                               placeholder="with The Openers (optional)">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Additional Information</label>
                                        <textarea name="additional_information" class="form-control" rows="4"
                                                  placeholder="This concert is 19+ (optional)">{{ old('additional_information') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <div class="container">
                    <div class="row">
                        <div class="col col-lg-4">
                            <div>
                                <h2>Date &amp; Time</h2>
                                <p>True metalheads really only care about the obscure
                                    openers, so make sure they don't get there late!</p>
                            </div>
                        </div>
                        <div class="col col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Date</label>
                                                <input type="date" name="date" class="form-control" placeholder="yyyy-mm-dd"
                                                       value="{{ old('date') }}">
                                            </div>
                                        </div>
                                        <div class="col col-md-6">
    
                                            <div class="form-group">
                                                <label class="form-label">Start Time</label>
                                                <input name="time" class="form-control" placeholder="7:00pm"
                                                       value="{{ old('time') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <div class="container">
                    <div class="row">
                        <div class="col col-lg-4">
                            <div>
                                <h2>Tickets &amp; Pricing</h2>
                                <p>Set your ticket price and availability, but don't forget,
                                    metalheads are cheap so keep it reasonable.</p>
                            </div>
                        </div>
                        <div class="col col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Price</label>
                                                <div class="input-group">
                                                <span class="input-group-addon text-dark-muted">
                                                    $
                                                </span>
                                                    <input name="ticket_price" class="form-control" placeholder="0.00"
                                                           value="{{ old('ticket_price') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Ticket Quantity</label>
                                                <input name="ticket_quantity" class="form-control" placeholder="250"
                                                       value="{{ old('ticket_quantity') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <div class="container">
                    <div class="row">
                        <div class="col col-lg-4">
                            <div>
                                <h2>Concert Poster</h2>
                                <p>
                                    Have a sweet poster for this concert? Upload it here and it'll be included on the
                                    checkout page.
                                </p>
                            </div>
                        </div>
                        <div class="col col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="form-label m-xs-b-2">Poster Image</label>
                                        <input type="file" name="poster_image" class="form-control-file">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container text-right mt-3">
                <button type="submit" class="btn btn-primary">Add Concert</button>
            </div>
        </form>
    </div>
@endsection