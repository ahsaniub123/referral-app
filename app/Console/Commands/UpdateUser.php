<?php

namespace App\Console\Commands;

use App\Setting;
use App\User;
use Illuminate\Console\Command;
use Osiset\BasicShopifyAPI\BasicShopifyAPI;
use Osiset\BasicShopifyAPI\Options;
use Osiset\BasicShopifyAPI\Session;

class UpdateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::whereNotNull('shopify_id')->chunkById(10, function ($users) {

            $shop = User::first();
            $options = new Options();
            $options->setVersion('2022-04');
            $api = new BasicShopifyAPI($options);
            $api->setSession(new Session($shop->name, $shop->password));
            $setting = Setting::first();

            foreach ($users as $user) {

                if($user->subscription == 1 && $user->subscription_end_at->isPast()) {
                    $user->subscription = 0;
                    $user->subscription_end_at = null;
                    $user->subscribed_at = null;
                    $user->save();

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
            }
        });
    }
}
