@extends('shopify-app::layouts.default')
@section('header')
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col-4">
                <h2 class="page-title">
                    Settings
                </h2>
            </div>
{{--            <div class="col-8 text-end">--}}
{{--                <a href="{{ route('products.sync') }}" class="btn btn-primary">--}}
{{--                    <i class="fa fa-sync" style="margin-right: 3px;"></i>--}}
{{--                    Sync Products--}}
{{--                </a>--}}
{{--            </div>--}}
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
                        <div class="mb-3">
                            <label class="form-label">Enter Wallet Credits</label>
                            <input type="number" required class="form-control" @if($settings) value="{{ $settings->wallet_credits }}" @endif name="wallet_credits" placeholder="Enter the wallet credits to be assigned on subscription">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Discount</label>
                            <input type="number" step="any" required class="form-control" @if($settings) value="{{ $settings->product_discount }}" @endif name="product_discount" placeholder="Enter the percentage of discount you wants to apply">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Subscription Plan</label>
                            <select name="subscription_plan" class="form-control" id="">
                                <option value="yearly">Yearly</option>
                            </select>
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
