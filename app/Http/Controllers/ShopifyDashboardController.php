<?php

namespace App\Http\Controllers;

use App\Setting;
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

        if($user->deactive) {
            Auth::logout();
            return \redirect()->route('login')->with('error', 'Your access has been disabled, please contact admin');
        }

        return view('managers.dashboard')->with([
            'user' => $user,
            'setting' => $setting
        ]);
    }

    public function markSubscriptionAsComplete(Request $request) {

        $user = Auth::user();
        $setting = Setting::first();

        $amount_to_be_paid = $setting->subscription_amount * 100;
        $amount_to_be_paid = (int) $amount_to_be_paid;

        try {
            auth()->user()->charge($amount_to_be_paid, $request->paymentMethod);
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error','Payment can not be proceed');
        }

        $user->subscription = 1;
        $user->save();

        if($referrer = $user->referrer) {
            $referrer->wallet_credit += $setting->subscription_amount;
            $referrer->save();
        }

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
                "customer_selection"=> "all",
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
        $wallet_log->message = 'A Discount Code '. $discount_code .' is been generated for an amount of '. $request->credit .' on '. now()->format('d M, Y h:i a');
        $wallet_log->save();

        return redirect()->back()->with('success', 'Discount code generated successfully!');
    }
}
