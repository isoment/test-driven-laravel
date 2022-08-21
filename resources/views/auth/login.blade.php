@extends('layouts.master')

@section('body')
<div class="login-card mx-auto mt-5" style="width:23rem;">
    <div class="card">
        <div class="card-body">
            <form action="/login" method="POST" >
                @csrf
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email"
                           name="email" 
                           class="form-control" 
                           id="email" 
                           placeholder="Enter email">
                </div>
                <div class="form-group">
                    <label for="email">Password</label>
                    <input type="password"
                           name="password" 
                           class="form-control" 
                           id="password" 
                           placeholder="Password">
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    <div>
</div>
@endsection