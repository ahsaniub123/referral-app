@extends('layouts.admin')

@section('styles')
    <style>
        /**
        * The CSS shown here will not be introduced in the Quickstart guide, but shows
        * how you can use CSS to style your Element's container.
        */
        .StripeElement {
            width: 100%;
            box-sizing: border-box;
            height: 40px;
            padding: 10px 12px;
            border: 1px solid transparent;
            border-radius: 4px;
            background-color: white;
            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }
        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }
        .StripeElement--invalid {
            border-color: #fa755a;
        }
        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;}
    </style>
@endsection

@section('header')
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Dashboard
                </h2>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="col-12">

        <div class="row">

            @if(!$user->subscription)
                <div class="col-md-12">
                    <div class="alert alert-success d-flex justify-content-between align-items-center">
                        <span class="alert-title">{!! $setting->subscription_text !!}</span>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-subscribe">Subscribe now</button>

                        <div class="modal modal-blur fade" id="modal-subscribe" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" style="text-align: left;" role="document">
                                <form class="modal-content" action="{{ route('shopify.subscription.complete') }}" method="post" id="payment-form">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">{!! $setting->subscription_text !!}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-inline">
                                        <label class="form-label" for="cardholder-name">Cardholder's Name</label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" id="cardholder-name" class="form-control">
                                            </div>
                                        </div>
                                        <br>

                                        <div class="form-inline my-2">
                                            <label class="form-label" for="card-element">Credit or debit card</label>
                                        </div>


                                        <div id="card-element">
                                            <!-- A Stripe Element will be inserted here. -->
                                        </div>

                                        <!-- Used to display form errors. -->
                                        <div id="card-errors" role="alert"></div>
                                    </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="text-end mt-3">
                                            <button type="submit" id="pay-btn" class="btn btn-success waves-effect pay-btn">Pay Now - {{ $setting->subscription_amount }}$</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            @endif


            @if($user->subscription)
                <div class="col-md-12">
                <div class="alert d-flex justify-content-between">
                    <span class="alert-title">Please share your referral link <span class="text-primary text-decoration-underline">{{ route('register') }}?ref={{ $user->referral_token }}</span> with others in order to earn {{ $setting->wallet_credits }} wallet credits</span>
                    <div>
                        <a class="btn btn-sm btn-primary fb-btn" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u={{ route('register') }}?ref={{ $user->referral_token }}"><i class="fab fa-facebook"></i></a>
                        <a class="btn btn-sm btn-info discount-share-btn tw-btn" target="_blank" href="https://twitter.com/intent/tweet?url={{ route('register') }}?ref={{ $user->referral_token }}"><i class="fab fa-twitter"></i></a>
                        <a target="_blank" class="btn btn-sm btn-success discount-share-btn cp-btn" href="https://api.whatsapp.com/send?&text={{ route('register') }}?ref={{ $user->referral_token }}"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            @endif

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body p-2 text-center">
                        <div class="h1 m-0">{{ $user->referrals()->count() }}</div>
                        <div class="text-muted mb-3">Referral Users</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
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
                        <div class="h1 m-0">{{ $user->wallet_logs()->count() }}</div>
                        <div class="text-muted mb-3">Discount Codes</div>
                    </div>
                </div>
            </div>

            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-body">
                            <div class="card-title">
                                Recent Referral Users
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
                                @forelse($user->referrals()->latest()->limit(5)->get() as $referral)
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
                                                <span class="badge bg-success">Completed</span>
                                            @else
                                                <span class="badge bg-danger">Not Completed</span>
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
                        </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@section('script')
    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">
        // Create a Stripe client.
        var stripe = Stripe('pk_test_v3Q9XVLEYLCmm2YbBehuLAQL00W52okI7i');
        // Create an instance of Elements.
        var elements = stripe.elements();
        // Custom styling can be passed to options when creating an Element.
        // (Note that this demo uses a wider set of styles than the guide below.)
        var style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };
        // Create an instance of the card Element.
        var card = elements.create('card', {style: style});
        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');
        // Handle real-time validation errors from the card Element.
        card.on('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
        // Handle form submission.
        var form = document.getElementById('payment-form');
        var cardHolderName = document.getElementById('cardholder-name');


        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            const { paymentMethod, error } = await stripe.createPaymentMethod(
                'card', card, {
                    billing_details: { name: cardHolderName.value }
                }
            );
            if (error) {
                // Inform the user if there was an error.
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = error.message;
            } else {
                // Send the token to your server.
                stripeTokenHandler(paymentMethod);
            }
        });
        // Submit the form with the token ID.
        function stripeTokenHandler(paymentMethod) {
            // Insert the token ID into the form so it gets submitted to the server
            var payBtn = document.getElementById('pay-btn');
            var form = document.getElementById('payment-form');
            var amount = document.getElementById('amount');
            var hiddenInput = document.createElement('input');

            // payBtn.setAttribute('disabled', true);
            payBtn.innerText = 'Processing';
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'paymentMethod');
            hiddenInput.setAttribute('value', paymentMethod.id);


            form.appendChild(hiddenInput);
            // Submit the form
            form.submit();
        }
    </script>
@endsection
