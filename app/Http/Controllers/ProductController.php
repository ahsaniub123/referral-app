<?php

namespace App\Http\Controllers;

use App\Product;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    public function syncProducts($next = null){

        $user = Auth::user();
        $products = $user->api()->rest('GET', '/admin/products.json', [
            'limit' => 250,
            'page_info' => $next
        ]);
        $products = json_decode(json_encode($products));


        if(isset($products->body->products))
        {
            foreach ($products->body->products as $product) {
                $this->createProduct($product, $user);
            }
        }

        if (isset($products->link->next)) {
            $this->syncProducts($products->link->next);
        }

        return Redirect::tokenRedirect('settings.index', ['notice' => 'Products Synced Successfully']);
    }

    public function createProduct($product, $user)
    {
        $prod = Product::where('shopify_id', $product->id)->first();

        if ($prod === null) {
            $prod = new Product();
        }

        $prod->shopify_id = $product->id;
        $prod->title = $product->title;
        $prod->save();

        $product_ids = Product::pluck('shopify_id')->toArray();
        $settings = Setting::first();

        if($settings && $settings->price_rule_id) {

            $user_ids = User::where('subscription', 1)->where('deactive', 0)->whereNotNull('shopify_id')->pluck('shopify_id')->toArray();

            $data = [
                "price_rule" => [
                    "entitled_product_ids" => $product_ids,
                ]
            ];

            if(count($user_ids)) {
                $data['price_rule']['starts_at'] = now();
                $data['price_rule']['ends_at'] = null;
            } else {
                $data['price_rule']['ends_at'] = now();
            }

            $user->api()->rest('PUT', '/admin/price_rules/' . $settings->price_rule_id . '.json', $data);
        }

        return $prod;
    }

}
