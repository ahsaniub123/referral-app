@extends('layouts.app')

@section('content')
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="text-center mb-4">
                <a><img src="https://cdn.shopify.com/s/files/1/0550/4060/6257/files/21.png?v=1665085801" height="76" alt=""></a>
            </div>

            <form class="card card-md" method="POST" action="{{ route('register') }}">
                @csrf

                <div class="card-body">
                    <h2 class="card-title text-center mb-2">Register your account</h2>

                    <div class="mb-2">
                        <label for="name" class="form-label">Name</label>

                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                    </div>

                    <div class="mb-2">
                        <label for="email" class="form-label">{{ __('E-Mail Address') }}</label>

                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="mb-2">
                        <label for="password" class="form-label">{{ __('Password') }}</label>

                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                    </div>

                    <div class="mb-2">
                        <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>

                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary w-100 mb-2">Register</button>
                        <a href="{{ route('login') }}">Already have an account?</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
