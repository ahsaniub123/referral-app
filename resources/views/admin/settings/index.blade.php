@extends('shopify-app::layouts.default')
@section('header')
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col-4">
                <h2 class="page-title">
                    Settings
                </h2>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="row row-cards">
        <div class="col-12">

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        Settings
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.save') }}" method="POST">
                        @sessionToken
                        <div class="mb-3">
                            <label class="form-label">Enter Subscription Amount</label>
                            <input type="number" required step="any" class="form-control" @if($settings) value="{{ $settings->subscription_amount }}" @endif name="subscription_amount" placeholder="Enter the subscription amount">
                        </div>
                        <div class="mb-3 text-end">
                            <button class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
