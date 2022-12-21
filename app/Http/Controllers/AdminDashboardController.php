<?php

namespace App\Http\Controllers;

use App\Product;
use App\Setting;
use App\SubscriptionPlan;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Osiset\BasicShopifyAPI\BasicShopifyAPI;
use Osiset\BasicShopifyAPI\Options;
use Osiset\BasicShopifyAPI\Session;

class AdminDashboardController extends Controller
{
    public function index() {

        $user = Auth::user();
        $user->assignRole('admin');

        $subscribed_users = User::role('shopify')->where('subscription', 1)->count();
        $non_subscribed_users = User::role('shopify')->where('subscription', 0)->count();

        return view('admin.dashboard')->with([
            'user' => $user,
            'subscribed_users' => $subscribed_users,
            'non_subscribed_users' => $non_subscribed_users
        ]);
    }

    public function users(Request $request) {

        $user = Auth::user();
        $shopify_users = User::role('shopify')->newQuery();

        if($request->filled('search'))
            $shopify_users->where('name', 'LIKE', '%'.$request->search."%")->orWhere('email', 'LIKE', '%'.$request->search."%");

        $shopify_users = $shopify_users->latest()->paginate(30);

        return view('admin.shopify.index')->with([
            'user' => $user,
            'shopify_users' => $shopify_users
        ]);

    }

    public function settings() {

        $user = Auth::user();
        $settings = Setting::first();

        return view('admin.settings.index')->with([
            'user' => $user,
            'settings' => $settings
        ]);
    }

    public function save_settings(Request $request) {

        $settings = Setting::first();

        if($settings == null)
            $settings = new Setting();

        //$settings->subscription_amount = $request->subscription_amount;
        //$settings->subscription_plan = $request->subscription_plan;
        $settings->wallet_credits = $request->wallet_credits;
        $settings->product_discount = $request->product_discount;
        $settings->subscription_text = $request->subscription_text;
        $settings->save();

        foreach ($request->plan_id as $index => $plan_id) {
            $plan = SubscriptionPlan::find($plan_id);
            $plan->price = $request->plan_pricep[$index];
            $plan->save();
        }

        $shop = User::first();
        $options = new Options();
        $options->setVersion('2022-04');
        $api = new BasicShopifyAPI($options);
        $api->setSession(new Session($shop->name, $shop->password));
        $product_ids = Product::pluck('shopify_id')->toArray();

        if($settings->price_rule_id == null) {

            $data = [
                "price_rule" => [
                    "title" => $settings->product_discount .' PERCENT OFF',
                    "target_type" => "line_item",
                    "target_selection" => "entitled",
                    "allocation_method" => "each",
                    "value_type" => "percentage",
                    "value" => '-' . $settings->product_discount,
                    "customer_selection"=> "all",
                    'starts_at' => now()
                ]
            ];

            if(count($product_ids))
                $data['price_rule']["entitled_product_ids"] = $product_ids;
            else
                $data['price_rule']["entitled_collection_ids"] = [263036764209];

            $response = $api->rest('POST', '/admin/price_rules.json', $data);
            $price_rule = $response['body']['container']['price_rule'];

            $data = [
                "discount_code" => [
                    "code" => $price_rule['title']
                ]
            ];

            $discount_response = $api->rest('POST', '/admin/price_rules/' . $price_rule['id'] . '/discount_codes.json', $data);
            $discount_id = $discount_response['body']['container']['discount_code']['id'];
            $discount_code = $price_rule['title'];

            $settings->discount_code = $discount_code;
            $settings->discount_id = $discount_id;
            $settings->price_rule_id = $price_rule['id'];
            $settings->save();

        }
        else {

            $user_ids = User::where('subscription', 1)->where('deactive', 0)->whereNotNull('shopify_id')->pluck('shopify_id')->toArray();

            $data = [
                "price_rule" => [
                    "title" => $settings->product_discount .' PERCENT OFF',
                    "value_type" => "percentage",
                    "value" => '-' . $settings->product_discount,
                ]
            ];

            if(count($user_ids)) {
                $data['price_rule']['starts_at'] = now();
                $data['price_rule']['ends_at'] = null;
            } else {
                $data['price_rule']['ends_at'] = now();
            }


            $api->rest('PUT', '/admin/price_rules/'.$settings->price_rule_id.'.json', $data);

            $data = [
                "discount_code" => [
                    "code" => $settings->product_discount .' PERCENT OFF'
                ]
            ];

            $api->rest('PUT', '/admin/price_rules/'.$settings->price_rule_id.'/discount_codes/'.$settings->discount_id.'.json', $data);

            $settings->discount_code = $settings->product_discount .' PERCENT OFF';
            $settings->save();
        }

        return Redirect::tokenRedirect('settings.index', ['notice' => 'Settings Saved Successfully']);
    }

    public function delete_user($id) {

        $user = User::find($id);

        if($user->shopify_id) {

            $shop = User::first();
            $options = new Options();
            $options->setVersion('2022-04');
            $api = new BasicShopifyAPI($options);
            $api->setSession(new Session($shop->name, $shop->password));

            $api->rest('DELETE', '/admin/customers/'.$user->shopify_id.'.json');

            $user->forceDelete();

            $setting = Setting::first();
            if($setting && $setting->price_rule_id) {
                $user_ids = User::where('subscription', 1)->where('deactive', 0)->whereNotNull('shopify_id')->pluck('shopify_id')->toArray();

                if (count($user_ids)) {
                    $data = [
                        "price_rule" => [
                            "prerequisite_customer_ids" => $user_ids,
                            "customer_selection" => 'prerequisite',
                            'starts_at' => now(),
                            'ends_at' => null
                        ]
                    ];
                } else {
                    $data = [
                        "price_rule" => [
                            "customer_selection" => "all",
                            "prerequisite_customer_ids" => [],
                            "ends_at" => now()
                        ]
                    ];
                }

                $api->rest('PUT', '/admin/price_rules/' . $setting->price_rule_id . '.json', $data);
            }
        }

        return Redirect::tokenRedirect('shopify.index', ['notice' => 'User Deleted Successfully']);
    }

    public function change_user_status($id) {

        $user = User::find($id);
        $user->deactive = !$user->deactive;
        $user->save();

        $setting = Setting::first();

        if($setting && $setting->price_rule_id) {

            $shop = User::first();
            $options = new Options();
            $options->setVersion('2022-04');
            $api = new BasicShopifyAPI($options);
            $api->setSession(new Session($shop->name, $shop->password));

            $user_ids = User::where('subscription', 1)->where('deactive', 0)->whereNotNull('shopify_id')->pluck('shopify_id')->toArray();

            if (count($user_ids)) {
                $data = [
                    "price_rule" => [
                        "prerequisite_customer_ids" => $user_ids,
                        "customer_selection" => 'prerequisite',
                        'starts_at' => now(),
                        'ends_at' => null
                    ]
                ];
            } else {
                $data = [
                    "price_rule" => [
                        "customer_selection" => "all",
                        "prerequisite_customer_ids" => [],
                        "ends_at" => now()
                    ]
                ];
            }

            $api->rest('PUT', '/admin/price_rules/' . $setting->price_rule_id . '.json', $data);
        }

        return Redirect::tokenRedirect('shopify.index', ['notice' => 'User Status Changed Successfully']);
    }

    public function update_user(Request $request, $id) {

        $user = User::find($id);

        $this->validate($request, [
            'password' => ['required'],
        ]);

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return Redirect::tokenRedirect('shopify.index', ['notice' => 'User Password Updated Successfully']);

    }

}
