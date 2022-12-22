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
            background-color: #fefde5 !important;
            }
         .tooltips {
  position: relative;
}

.tooltips .tooltiptext {
  visibility: hidden;
  width: 140px;
  background-color: #555;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px;
  position: absolute;
  z-index: 1;
  bottom: 150%;
  left: 50%;
  margin-left: -75px;
  opacity: 0;
  transition: opacity 0.3s;
}

.tooltips .tooltiptext::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #555 transparent transparent transparent;
}

.tooltips:hover .tooltiptext {
  visibility: visible;
  opacity: 1;
}
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
                        <span class="alert-title">
                            @php
                                $subscription_history = $user->subscription_history()->whereNotNull('ended_at')->first();
                            @endphp
                            @if($subscription_history)
                                Dear customer, your subscription has ended on {{ $subscription_history->ended_at->toDateString() }}, please re-subscribe to <strong>{!! $setting->subscription_text !!}</strong>
                            @else
                                {!! $setting->subscription_text !!}
                            @endif
                        </span>
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
                                            <label class="form-label" for="">Subscription Plans</label>
                                            <select name="plan_id" class="js-plan-select form-control" id="">
                                                @foreach($subscription_plans as $subscription_plan)
                                                    <option value="{{ $subscription_plan->id }}">{{ $subscription_plan->name }} - {{ $subscription_plan->price }}$</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <br>
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
                                            <button type="submit" id="pay-btn" class="btn btn-success waves-effect pay-btn">Pay now</button>
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
                    <span class="alert-title">Please share your referral link <span class="text-primary text-decoration-underline">https://www.21spirit.com/account/register?ref={{ $user->referral_token }}</span> with others in order to earn {{ $setting->wallet_credits }} wallet credits</span>
                    <div>
                        <a target="_blank" class="btn btn-sm btn-success discount-share-btn cp-btn" href="https://api.whatsapp.com/send?&text=https://www.21spirit.com/account/register?ref={{ $user->referral_token }}"><i class="fab fa-whatsapp"></i></a>
                        <a target="_blank" class="btn btn-sm btn-primary fb-btn" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https://www.21spirit.com/account/register?ref={{ $user->referral_token }}"><i class="fab fa-facebook"></i></a>
                        <a target="_blank" class="btn btn-sm btn-info discount-share-btn tw-btn" href="https://twitter.com/intent/tweet?url=https://www.21spirit.com/account/register?ref={{ $user->referral_token }}"><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-sm btn-success discount-share-btn copy-btn tooltips" href="javascript:void(0);" onclick="copyClipboard()" onmouseout="outFunc()"><span class="tooltiptext" id="myTooltip">Copy to clipboard</span><i class="fa fa-clone"></i></a>
                    </div>
                </div>
            </div>
            @endif
            <input type="test" value="https://www.21spirit.com/account/register?ref={{ $user->referral_token }}" id="myInput" style="display: none;">
            <script>
             function copyClipboard() {
                  // Get the text field
                  var copyText = document.getElementById("myInput");

                  // Select the text field
                  copyText.select();
                  copyText.setSelectionRange(0, 99999); // For mobile devices

                   // Copy the text inside the text field
                  navigator.clipboard.writeText(copyText.value);
                  var tooltip = document.getElementById("myTooltip");
                  tooltip.innerHTML = "Copied: " + copyText.value;
                }
                function outFunc() {
                  var tooltip = document.getElementById("myTooltip");
                  tooltip.innerHTML = "Copy to clipboard";
                }
            </script>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body p-2 text-center">
                        <div class="h1 m-0">{{ $user->referrals()->count() }}</div>
                        <div class="text-muted mb-3">Referral Users</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
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

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body p-2 text-center">
                        <div class="h1 m-0">{{ $user->wallet_logs()->count() }}</div>
                        <div class="text-muted mb-3">Discount Codes</div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body p-2 text-center">
                        <div class="h1 m-0">
                            @if($user->subscription_end_at)
                                {{ $user->subscription_end_at->toDateString() }}
                            @else
                                <span style="font-size: 18px; font-weight: bold;">Not Subscribed Yet!</span>
                            @endif
                        </div>
                        <div class="text-muted mb-3">Subscription Ends On</div>
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
                                @forelse($user->referrals()->latest()->limit(500)->get() as $referral)
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

            var hiddenInput = document.createElement('input');
            var hiddenPlanInput = document.createElement('input');

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
