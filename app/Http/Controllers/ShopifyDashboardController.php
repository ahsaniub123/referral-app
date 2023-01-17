<?php

namespace App\Http\Controllers;

use App\Setting;
use App\SubscriptionHistory;
use App\SubscriptionPlan;
use App\User;
use App\Wallet;
use App\WalletLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Osiset\BasicShopifyAPI\BasicShopifyAPI;
use Osiset\BasicShopifyAPI\Options;
use Osiset\BasicShopifyAPI\Session;

class ShopifyDashboardController extends Controller
{
    public function index() {

        $user = Auth::user();
        $setting = Setting::first();
        $admin = User::first();
        $subscription_plans = SubscriptionPlan::all();
        $options = new Options();
        $options->setVersion('2022-04');
        $api = new BasicShopifyAPI($options);
        $api->setSession(new Session($admin->name, $admin->password));

        if($user->deactive) {
            Auth::logout();
            return \redirect()->route('login')->with('error', 'Your access has been disabled, please contact admin');
        }

        if($user->shopify_id == null) {

            $payload = [
                'customer' => [
                    'first_name ' => $user->name,
                    'email' => $user->email
                ]
            ];

            $response = $api->rest('POST', '/admin/customers.json', $payload);
            $response = json_decode(json_encode($response));

            if (!$response->errors) {
                $user->shopify_id = $response->body->customer->id;
                $user->save();

                $response = $api->rest('POST', '/admin/customers/'.$user->shopify_id.'/send_invite.json');
            }
        }

        if($user->subscription == 1 && $user->subscription_end_at->isPast()) {
            $user->subscription = 0;
            $user->subscription_end_at = null;
            $user->subscribed_at = null;
            $user->save();

            $subscription_history = new SubscriptionHistory();
            $subscription_history->message = 'Subscription has been ended on '.now()->format('d M, Y h:i a');
            $subscription_history->ended_at = now();
            $subscription_history->user_id = $user->id;
            $subscription_history->save();

            $user_ids = User::where('subscription', 1)->where('deactive', 0)->whereNotNull('shopify_id')->pluck('shopify_id')->toArray();

            if(count($user_ids)) {
                $data = [
                    "price_rule" => [
                        "prerequisite_customer_ids" => $user_ids,
                        "customer_selection" => 'prerequisite',
                        'starts_at' => now(),
                        'ends_at' => null
                    ]
                ];
            }
            else {
                $data = [
                    "price_rule" => [
                        "customer_selection"=> "all",
                        "prerequisite_customer_ids" => [],
                        "ends_at" => now()
                    ]
                ];
            }

            $api->rest('PUT', '/admin/price_rules/'.$setting->price_rule_id.'.json', $data);
        }

        if($user->shopify_id) {
            $api->rest('POST', '/admin/customers/' . $user->shopify_id . '/metafields.json', [
                'metafield' => [
                    'namespace' => 'referral_app',
                    'key' => 'subscribed',
                    'type' => 'boolean',
                    'value' => $user->subscription
                ]
            ]);

            $api->rest('POST', '/admin/customers/' . $user->shopify_id . '/metafields.json', [
                'metafield' => [
                    'namespace' => 'referral_app',
                    'key' => 'deactive',
                    'type' => 'boolean',
                    'value' => $user->deactive
                ]
            ]);

            $api->rest('POST', '/admin/customers/' . $user->shopify_id . '/metafields.json', [
                'metafield' => [
                    'namespace' => 'referral_app',
                    'key' => 'subscription_end_at',
                    'type' => 'single_line_text_field',
                    'value' => $user->subscription_end_at ? $user->subscription_end_at->toDateString() : 'not subscribed'
                ]
            ]);

            if ($setting->product_discount) {
                $api->rest('POST', '/admin/customers/' . $user->shopify_id . '/metafields.json', [
                    'metafield' => [
                        'namespace' => 'referral_app',
                        'key' => 'discount_on_products',
                        'type' => 'number_integer',
                        'value' => $setting->product_discount
                    ]
                ]);
            }
        }

        return view('managers.dashboard')->with([
            'user' => $user,
            'setting' => $setting,
            'subscription_plans' => $subscription_plans
        ]);
    }

    public function markSubscriptionAsComplete(Request $request) {

        $user = Auth::user();
        $setting = Setting::first();

        $subscription_plan = SubscriptionPlan::find($request->plan_id);

        $amount_to_be_paid = $subscription_plan->price * 100;
        $amount_to_be_paid = (int) $amount_to_be_paid;

        try {
            auth()->user()->charge($amount_to_be_paid, $request->paymentMethod);
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error','Payment can not be proceed');
        }

        $user->subscription = 1;
        $user->subscribed_at = now();

        if($subscription_plan->name == 'Yearly') {
            $user->subscription_plan = 'Yearly';
            $user->subscription_end_at = now()->addYear();
        }
        else {
            $user->subscription_end_at = now()->addMonth();
            $user->subscription_plan = 'Monthly';
        }

        $user->save();

        $subscription_history = new SubscriptionHistory();
        $subscription_history->message = 'Subscription has been purchased on '.now()->format('d M, Y h:i a');
        $subscription_history->started_at = now();
        $subscription_history->user_id = $user->id;
        $subscription_history->subscription_plan_id = $subscription_plan->id;
        $subscription_history->save();

        if($referrer = $user->referrer) {
            $referrer->wallet_credit += $setting->wallet_credits;
            $referrer->save();
        }

        $shop = User::first();
        $options = new Options();
        $options->setVersion('2022-04');
        $api = new BasicShopifyAPI($options);
        $api->setSession(new Session($shop->name, $shop->password));

        $user_ids = User::where('subscription', 1)->where('deactive', 0)->whereNotNull('shopify_id')->pluck('shopify_id')->toArray();

        if(count($user_ids)) {
            $data = [
                "price_rule" => [
                    "prerequisite_customer_ids" => $user_ids,
                    "customer_selection" => 'prerequisite',
                    'starts_at' => now(),
                    'ends_at' => null
                ]
            ];
        }
        else {
            $data = [
                "price_rule" => [
                    "customer_selection"=> "all",
                    "prerequisite_customer_ids" => [],
                    "ends_at" => now()
                ]
            ];
        }

        $api->rest('PUT', '/admin/price_rules/'.$setting->price_rule_id.'.json', $data);

        return redirect()->back()->with('success','Subscription Completed Successfully');
    }

    public function showReferralUsers(Request $request) {

        $user = Auth::user();

        if(!$user->subscription)
            return redirect()->route('shopify.home')->with('error', 'Please complete subscription first');

        $referrals = $user->referrals()->newQuery();

        if($request->filled('search'))
            $referrals->where('name', 'LIKE', '%'.$request->search.'%')->orWhere('email', 'LIKE', '%'.$request->search.'%');

        $referrals = $referrals->latest()->paginate(20);

        return view('managers.referrals.index')->with([
            'user' => $user,
            'referrals' => $referrals
        ]);
    }

    public function showWalletDetails(Request $request) {

        $user = Auth::user();

        if(!$user->subscription)
            return redirect()->route('shopify.home')->with('error', 'Please complete subscription first');

        return view('managers.wallets.index')->with([
            'user' => $user,
        ]);
    }

    public function generateDiscountCode(Request $request) {

        $user = Auth::user();

        if($request->credit > $user->wallet_credit)
            return \redirect()->back()->with('error', 'Please enter a valid wallet credit amount');

        $shop = User::first();
        $options = new Options();
        $options->setVersion('2022-04');
        $api = new BasicShopifyAPI($options);
        $api->setSession(new Session($shop->name, $shop->password));


        $data = [
            "price_rule"=> [
                "title"=> Str::random(5).rand(1,1000),
                "target_type"=> "line_item",
                "target_selection"=> "all",
                "allocation_method"=> "across",
                "value_type"=> "fixed_amount",
                "value"=> '-'.$request->credit,
                "prerequisite_customer_ids" => [$user->shopify_id],
                "customer_selection" => 'prerequisite',
                "once_per_customer"=> true,
                'starts_at'=> now()
            ]
        ];

        $response = $api->rest('POST', '/admin/price_rules.json', $data);
        $price_rule = $response['body']['container']['price_rule'];

        $data = [
            "discount_code"=> [
                "code"=> $price_rule['title']
            ]
        ];

        $api->rest('POST', '/admin/price_rules/'.$price_rule['id'].'/discount_codes.json', $data);

        $discount_code = $price_rule['title'];

        $user->wallet_credit = $user->wallet_credit - $request->credit;
        $user->wallet_credit_used = $user->wallet_credit_used + $request->credit;
        $user->save();


        $wallet_log = new WalletLog();
        $wallet_log->discount_code = $discount_code;
        $wallet_log->user_id = $user->id;
        $wallet_log->price_rule_id = $price_rule['id'];
        $wallet_log->message = 'A Discount Code '. $discount_code .' is been generated for an amount of '. $request->credit .' on '. now()->format('d M, Y h:i a');
        $wallet_log->save();

        return redirect()->back()->with('success', 'Discount code generated successfully!');
    }
    
    public function showSubscriptionPage(Request $request) {

        $user = Auth::user();

        if(!$user->subscription)
            return redirect()->route('shopify.home')->with('error', 'Please complete subscription first');

        return view('managers.subscription.index')->with([
            'user' => $user,
        ]);
    }
}
