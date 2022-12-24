@extends('shopify-app::layouts.default')
@section('header')
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Dashboard | Home
                </h2>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="col-12">
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body p-2 text-center">
                        <div class="h1 m-0">{{ $subscribed_users }}</div>
                        <div class="text-muted mb-3">Subscribed Users</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body p-2 text-center">
                        <div class="h1 m-0">{{ $non_subscribed_users }}</div>
                        <div class="text-muted mb-3">Non Subscribed Users</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
@endsection
