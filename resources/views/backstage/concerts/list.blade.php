@extends('layouts.backstage')

@section('backstageContent')
    <div class="mt-4">
        <div class="container">
            <h4>Published</h4>
            <div class="mt-3">
                @foreach ($concerts as $concert)
                    @if ($concert->published_at !== NULL)
                        <div class="card mb-2" style="max-width: 25rem;">
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
                        <div class="card mb-2" style="max-width: 25rem;">
                            <div class="card-body">
                            <h5 class="card-title">{{ $concert->title }}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{ $concert->subtitle }}</h6>
                            <p class="card-text">{{ $concert->venue }} - {{ $concert->city }}, {{ $concert->state }}</p>
                            <p class="card-text">{{ $concert->formatted_date }} @ {{ $concert->formatted_start_time }}</p>
                            <a href="{{ route('backstage.concerts.edit', $concert) }}" class="card-link">Edit</a>
                            <a href="#" class="card-link">Publish</a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection