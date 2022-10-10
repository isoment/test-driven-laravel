@extends('layouts.backstage')

@section('backstageContent')
    <div class="bg-light">
        <div class="container d-flex justify-content-between align-items-center py-2">
            <h3 class="mb-0">Concert List</h3>
            <a href="{{ route('backstage.concerts.new') }}" class="btn btn-primary">Add Concert</a>
        </div>
    </div>
    <div class="mt-4">
        <div class="container">
            <h4>Published</h4>
            <div class="mt-3">
                @foreach ($concerts as $concert)
                    @if ($concert->published_at !== NULL)
                        <div class="card mb-2" style="max-width: 35rem;">
                            <div class="card-body">
                            <h5 class="card-title">{{ $concert->title }}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{ $concert->subtitle }}</h6>
                            <p class="card-text">{{ $concert->venue }} - {{ $concert->city }}, {{ $concert->state }}</p>
                            <p class="card-text">{{ $concert->formatted_date }} @ {{ $concert->formatted_start_time }}</p>
                            <a href="{{ route('concerts.show', $concert) }}" class="card-link">Get Ticket Link</a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="container mt-5">
            <h4>Drafts</h4>
            <div class="mt-3">
                @foreach ($concerts as $concert)
                    @if ($concert->published_at === NULL)
                        <div class="card mb-2" style="max-width: 35rem;">
                            <div class="card-body">
                            <h5 class="card-title">{{ $concert->title }}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{ $concert->subtitle }}</h6>
                            <p class="card-text">{{ $concert->venue }} - {{ $concert->city }}, {{ $concert->state }}</p>
                            <p class="card-text">{{ $concert->formatted_date }} @ {{ $concert->formatted_start_time }}</p>
                            <div class="d-flex align-items-center justify-content-between">
                                <a href="{{ route('backstage.concerts.edit', $concert) }}" class="card-link">Edit</a>
                                <form action="{{ route('backstage.published-concerts.store') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="concert_id" value="{{ $concert->id }}">
                                    <button type="submit"
                                            class="bg-transparent border-0 text-primary">Publish</button>
                                </form>
                            </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection