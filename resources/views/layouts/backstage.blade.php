@extends('layouts.master')

@section('body')
    <div class="full-height flex-col">
        <nav class="navbar navbar-dark bg-dark justify-content-between">
            <a class="navbar-brand font-weight-bold" href="/">Navbar</a>
            <form class="inline-block" action="/logout" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-light">Log out</button>
            </form>
        </nav>
        <div class="flex-fit">
            @yield('backstageContent')
        </div>
    </div>
@endsection