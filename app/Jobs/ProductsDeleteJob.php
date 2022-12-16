<?php namespace App\Jobs;

use App\Product;
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

class ProductsDeleteJob implements ShouldQueue
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
        $product = json_decode(json_encode($this->data), false);

        $local_product = Product::where('shopify_id', $product->id)->first();

        if($local_product) {
            $local_product->delete();

            $settings = Setting::first();

            if($settings && $settings->price_rule_id) {

                $shop = User::first();
                $options = new Options();
                $options->setVersion('2022-04');
                $api = new BasicShopifyAPI($options);
                $api->setSession(new Session($shop->name, $shop->password));
                $product_ids = Product::pluck('shopify_id')->toArray();

                if (count($product_ids)) {

                    $data = [
                        "price_rule" => [
                            "entitled_product_ids" => $product_ids,
                        ]
                    ];

                    $api->rest('PUT', '/admin/price_rules/'.$settings->price_rule_id.'.json', $data);
                }
                else {
                    $data = [
                        "price_rule" => [
                            "entitled_collection_ids" => [263036764209],
                        ]
                    ];

                    $api->rest('PUT', '/admin/price_rules/'.$settings->price_rule_id.'.json', $data);
                }

            }
        }
    }
}
