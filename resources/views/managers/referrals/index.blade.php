@extends('layouts.admin')

@section('header')
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Referral Users
                </h2>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="col-12">
        <div class="row row-cards">
         <!--
            <form action="" class="mb-2" method="GET">
                <div class="form-group btn-group d-flex">
                    <input value="{{ request()->search }}" type="text" name="search" class="form-control" placeholder="Search users by name, email">
                    <button class="btn btn-primary"><i class="fa fa-search m-1"></i> Search</button>
                </div>
            </form>
           -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body p-2 text-center">
                        <div class="h1 m-0">{{ $user->referrals()->count() }}</div>
                        <div class="text-muted mb-3">Total Referral Users</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body p-2 text-center">
                        <div class="h1 m-0">{{ $user->referrals()->where('subscription', 1)->count() }}</div>
                        <div class="text-muted mb-3">Subscribed Referral Users</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body p-2 text-center">
                        <div class="h1 m-0">{{ $user->referrals()->where('subscription', 0)->count() }}</div>
                        <div class="text-muted mb-3">Non-Subscribed Referral Users</div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-body">
                        <div class="card-title">
                            Referral Users
                        </div>
                    </div>
                    <div class="">
                        <div class=" table-responsive">
                            <table class="table table-vcenter card-table table-striped">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subscription</th>
                                    <th>Date Joined</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($referrals as $referral)
                                    <tr>
                                        <td>
                                            <div class="d-flex py-1 align-items-center">
                                                <div class="flex-fill">
                                                    <div class="font-weight-medium">
                                                        {{ $referral->name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $referral->email }}
                                        </td>
                                        <td>
                                            @if($referral->subscription)
                                                <span class="badge bg-success">Subscribed</span>
                                            @else
                                                @php
                                                    $referral_subscription_history = $referral->subscription_history()->whereNotNull('ended_at')->first();
                                                @endphp
                                                @if($referral_subscription_history)
                                                    <span class="badge bg-danger">Expired</span>
                                                @else
                                                    <span class="badge bg-warning">Unsubscribed</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {{ $referral->created_at->toDateString() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="text-center">
                                        <td colspan="4">
                                            No Referrals yet!
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end">
                                {{ $referrals->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
