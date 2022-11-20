@extends('layouts.admin')

@section('header')
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Discount Codes
                </h2>
            </div>

            @if($user->wallet_credit)
                <div class="col-8 text-end">
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create-inventory-item">
                    <i class="fa fa-gift" style="margin-right: 3px;"></i>
                    Get Discount Coupon
                </a>
                <div class="modal modal-blur fade" id="modal-create-inventory-item" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered" style="text-align: left;" role="document">
                        <form action="{{ route('generate.discount.code') }}" method="POST" class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Get Discount Coupon</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Enter Wallet Credit</label>
                                    <input type="number" class="form-control" value="{{ $user->wallet_credit }}" max="{{ $user->wallet_credit }}" name="credit" placeholder="How many wallet credits your want to use for discount coupon?">
                                    @error('number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary" >
                                    Generate
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
@endsection

@section('content')
    <div class="col-12">
        <div class="row row-cards">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body p-2 text-center">
                        <div class="h1 m-0">
                            @if($user->wallet_credit)
                                {{ $user->wallet_credit }}
                            @else
                                0
                            @endif
                        </div>
                        <div class="text-muted mb-3">Wallet Credits</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body p-2 text-center">
                        <div class="h1 m-0">
                            @if($user->wallet_credit_used)
                                {{ $user->wallet_credit_used }}
                            @else
                                0
                            @endif
                        </div>
                        <div class="text-muted mb-3">Used Wallet Credits</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body p-2 text-center">
                        <div class="h1 m-0">{{ $user->wallet_logs()->count() }}</div>
                        <div class="text-muted mb-3">Discount Codes</div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-body">
                        <div class="card-title">
                            Discount Codes
                        </div>
                    </div>
                    <div class="">
                        <div class=" table-responsive">
                            <table class="table table-vcenter card-table table-striped">
                                <thead>
                                <tr>
                                    <th>Discount Code</th>
                                    <th>Details</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($user->wallet_logs()->latest()->get() as $log)
                                    <tr>
                                        <td>
                                            <div class="d-flex py-1 align-items-center">
                                                <div class="flex-fill">
                                                    <div class="font-weight-bolder">
                                                        <strong>
                                                            {{ $log->discount_code }}
                                                        </strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $log->message }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="text-center">
                                        <td colspan="2">
                                            No Discount Code yet!
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
