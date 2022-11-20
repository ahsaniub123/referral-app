@extends('layouts.app')

@section('content')
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="text-center mb-4">
                <a ><img src="https://cdn.shopify.com/s/files/1/0550/4060/6257/files/21.png?v=1665085801" height="76" alt=""></a>
            </div>
            <form class="card card-md" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Login to your account</h2>
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter email">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label">
                            Password
                        </label>
                        <div class="input-group input-group-flat">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password" >
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100 mb-2">Log in</button>
                        <a href="{{ route('register') }}">Create an account?</a>

                        @if(Session::has('error'))
                            <span class="text-danger d-block"><strong>{{ Session::get('error') }}</strong></span>
                        @endif
                        <span class="text-danger d-block"><strong>Note:</strong> In case you are having issue with login, please contact admin</span>

                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
