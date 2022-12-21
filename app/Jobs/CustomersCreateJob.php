<?php namespace App\Jobs;

use App\Setting;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Osiset\BasicShopifyAPI\BasicShopifyAPI;
use Osiset\BasicShopifyAPI\Options;
use Osiset\BasicShopifyAPI\Session;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use stdClass;

class CustomersCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var ShopDomain|string
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param string   $shopDomain The shop's myshopify domain.
     * @param stdClass $data       The webhook data (JSON decoded).
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Convert domain
        $this->shopDomain = ShopDomain::fromNative($this->shopDomain);

        $shopify_customer = json_decode(json_encode($this->data), false);

        $user = User::where('email', $shopify_customer->email)->first();

        if($user) {
            $user->shopify_id = $shopify_customer->id;
            $user->save();
        }

        if($user->shopify_id) {

            $admin = User::first();
            $options = new Options();
            $options->setVersion('2022-04');
            $api = new BasicShopifyAPI($options);
            $api->setSession(new Session($admin->name, $admin->password));
            $setting = Setting::first();

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
}
