
@extends('layouts.admin')

@section('header')
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Subscribe a plan
                </h2>
            </div>
        </div>
    </div>
@endsection


@section('content')
    <div class="col-12">
        <div class="row">
                <div class="col-md-12">
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
@endsection

@section('script')
    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">
        // Create a Stripe client.
        //var stripe = Stripe('pk_test_v3Q9XVLEYLCmm2YbBehuLAQL00W52okI7i'); // test keys
        var stripe = Stripe('pk_live_51M5z01HnqCyR2JEruCtT6i4DlHkyZs7yXViecOR42N2KlP50c7Flji2z1oXs6KND4yxhLAC1fqKrRY87VBCfVtCx00p2vXM1ab');
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
