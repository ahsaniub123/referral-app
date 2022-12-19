@extends('shopify-app::layouts.default')
@section('header')
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Users
                </h2>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="row row-cards">
        <div class="col-12">
            <form action="" class="mb-4" method="GET">
                <div class="form-group btn-group d-flex">
                    <input value="{{ request()->search }}" type="text" name="search" class="form-control" placeholder="Search users by name, email">
                    <button class="btn btn-primary"><i class="fa fa-search m-1"></i> Search</button>
                </div>
            </form>

            <div class="card">
                <div class="table-responsive">
                    <table class="table-responsive table table-vcenter card-table table-striped">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Referrer</th>
                            <th class="text-center">Referrals</th>
                            <th class="text-center">Discount Codes</th>
                            <th class="text-center">Subscription</th>
                            <th class="text-center">Date Joined</th>
                            <th class="text-center">Subscription History</th>
                            <th class="text-center">Wallet Credits</th>
                            <th class="text-center">Wallet Credits Used</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($shopify_users as $shopify_user)
                            <tr>
                                <td>
                                    <div class="d-flex py-1 align-items-center">
                                        <div class="flex-fill">
                                            <div class="font-weight-medium">
                                                {{ $shopify_user->name }}
                                                <span class="d-block" style="font-size: 12px;">{{ $shopify_user->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($referrer = $shopify_user->referrer)
                                        <div class="d-flex py-1 align-items-center">
                                            <div class="flex-fill">
                                                <div class="font-weight-medium">
                                                    {{ $referrer->name }}
                                                    <span class="d-block" style="font-size: 12px;">{{ $referrer->email }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        None
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $referral_count = $shopify_user->referrals()->count();
                                    @endphp

                                    @if($referral_count)
                                        <span style="text-decoration: underline; cursor:pointer;" class="text-primary" data-bs-toggle="modal" data-bs-target="#user-referrals-{{ $shopify_user->id }}">
                                            {{ $referral_count }}
                                        </span>
                                        <div class="modal modal-blur fade" id="user-referrals-{{ $shopify_user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered" style="text-align: left;" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Referrals</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <table class="table table-striped table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Name</th>
                                                                    <th class="text-center">Subscription</th>
                                                                    <th class="text-center">Date Joined</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($shopify_user->referrals as $referral)
                                                                    <tr>
                                                                        <td>
                                                                            <div class="d-flex py-1 align-items-center">
                                                                                <div class="flex-fill">
                                                                                    <div class="font-weight-medium">
                                                                                        {{ $referral->name }}
                                                                                        <span class="d-block" style="font-size: 12px;">{{ $referral->email }}</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            @if($referral->subscription)
                                                                                <span class="badge bg-success">Completed</span>
                                                                            @else
                                                                                <span class="badge bg-danger">Not Completed</span>
                                                                            @endif
                                                                        </td>
                                                                        <td class="text-center">
                                                                            {{ $referral->created_at->toDateString() }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                                                            Close
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        None
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $wallet_logs_count = $shopify_user->wallet_logs()->count();
                                    @endphp

                                    @if($wallet_logs_count)
                                        <span style="text-decoration: underline; cursor:pointer;" class="text-primary" data-bs-toggle="modal" data-bs-target="#user-code-{{ $shopify_user->id }}">
                                            {{ $wallet_logs_count }}
                                        </span>
                                        <div class="modal modal-blur fade" id="user-code-{{ $shopify_user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered" style="text-align: left;" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Discount Codes</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <table class="table table-striped table-bordered">
                                                            <thead>
                                                            <tr>
                                                                <th>Code</th>
                                                                <th>Details</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($shopify_user->wallet_logs()->latest()->get() as $log)
                                                                <tr>
                                                                    <td>
                                                                        <div class="d-flex py-1 align-items-center">
                                                                            <div class="flex-fill">
                                                                                <div class="font-weight-medium">
                                                                                    <strong>{{ $log->discount_code }}</strong>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        {{ $log->message }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                                                            Close
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        None
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($shopify_user->subscription)
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-danger">Not Completed</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $shopify_user->created_at->toDateString() }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $subscription_history_count = $shopify_user->subscription_history()->count();
                                    @endphp

                                    @if($subscription_history_count)
                                        <span style="text-decoration: underline; cursor:pointer;" class="text-primary" data-bs-toggle="modal" data-bs-target="#user-history-{{ $shopify_user->id }}">
                                            {{ $subscription_history_count }}
                                        </span>
                                        <div class="modal modal-blur fade" id="user-history-{{ $shopify_user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered" style="text-align: left;" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Subscription History</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <table class="table table-striped table-bordered">
                                                            <thead>
                                                            <tr>
                                                                <th>Details</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($shopify_user->subscription_history()->latest()->get() as $subscription_history)
                                                                <tr>
                                                                    <td>
                                                                        {{ $subscription_history->message }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                                                            Close
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        None
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($shopify_user->wallet_credit)
                                        {{ $shopify_user->wallet_credit }}
                                    @else
                                        0
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($shopify_user->wallet_credit_used)
                                        {{ $shopify_user->wallet_credit_used }}
                                    @else
                                        0
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-edit-manager-password-{{ $shopify_user->id }}">
                                            Reset Password
                                        </a>
                                        <div class="modal modal-blur fade" id="modal-edit-manager-password-{{ $shopify_user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered" style="text-align: left;" role="document">
                                                <form action="{{ route('shopify.user.update', $shopify_user->id) }}" method="POST" class="modal-content">
                                                    @sessionToken
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit User</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <div class="mb-3">
                                                            <label class="form-label">Password</label>
                                                            <input type="text" class="form-control" name="password" placeholder="Set a password for the user">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                                                            Cancel
                                                        </button>
                                                        <button type="submit" class="btn btn-primary" >
                                                            Update
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modal-delete-user-{{ $shopify_user->id }}">
                                            Delete
                                        </a>
                                        <div class="modal modal-blur fade" id="modal-delete-user-{{ $shopify_user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered" style="text-align: left;" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Delete User?</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <h3>Are you sure you want to remove this user?</h3>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                                                            Cancel
                                                        </button>
                                                        <a href="{{ route('shopify.user.delete', $shopify_user->id) }}" class="btn btn-danger" >
                                                            Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <a href="{{ route('shopify.user.change.status', $shopify_user->id) }}" class="btn btn-sm @if($shopify_user->deactive) btn-success @else btn-info @endif">
                                            @if($shopify_user->deactive)
                                                Activate
                                            @else
                                                De-activate
                                            @endif
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="text-center">
                                <td colspan="9">
                                    No Users yet!
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end">
                        {{ $shopify_users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
