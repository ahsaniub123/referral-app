<?php

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Osiset\BasicShopifyAPI\BasicShopifyAPI;
use Osiset\BasicShopifyAPI\Options;
use Osiset\BasicShopifyAPI\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('https://www.21spirit.com/account/logout');
});

Route::get('testing', function () {
   foreach (\App\User::get() as $user)
        $user->forceDelete();

   \App\Setting::truncate();
});

Route::group(['middleware' => ['verify.shopify'] ], function () {

    Route::group(['prefix' => 'admins'], function () {
        Route::get('/', "AdminDashboardController@index")->name('home');
        Route::get('/shopify', "AdminDashboardController@users")->name('shopify.index');
        Route::get('/shopify/{id}/delete', "AdminDashboardController@delete_user")->name('shopify.user.delete');
        Route::get('/shopify/{id}/change-status', "AdminDashboardController@change_user_status")->name('shopify.user.change.status');
        Route::post('/shopify/{id}/update', "AdminDashboardController@update_user")->name('shopify.user.update');
        Route::get('/settings', 'AdminDashboardController@settings')->name('settings.index');
        Route::post('/settings-save', 'AdminDashboardController@save_settings')->name('settings.save');
        Route::get('/sync-products','ProductController@syncProducts')->name('products.sync');
    });
});


//Route::group(['prefix' => 'shopify'], function() {
    Auth::routes();
//});

Route::group([
    'middleware' => [
        'auth', 'role:shopify'
    ]
], function()
{
    Route::get('/home', 'ShopifyDashboardController@index')->name('shopify.home');
    Route::get('/referrals', 'ShopifyDashboardController@showReferralUsers')->name('shopify.referral.users');
    Route::get('/wallet-details', 'ShopifyDashboardController@showWalletDetails')->name('shopify.user.wallet');
    Route::post('/complete-subscription', 'ShopifyDashboardController@markSubscriptionAsComplete')->name('shopify.subscription.complete');
    Route::post('/generate-discount-code', 'ShopifyDashboardController@generateDiscountCode')->name('generate.discount.code');
    Route::post('/subscription', 'ShopifyDashboardController@showSubscriptionPage')->name('shopify.subscription.page');

});

Route::get('/handle-discount', 'WidgetController@index');

Route::post('/shopify-register', 'HelperController@register');
Route::post('/shopify-login', 'HelperController@login');

Route::get('trigger-login/{id}', function ($id) {
    $user = User::find($id);

    Auth::login($user);

    return redirect()->route('shopify.home');
});

//Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::get('customer-webhook', function () {
    $shop = User::first();
    $options = new Options();
    $options->setVersion('2022-04');
    $api = new BasicShopifyAPI($options);
    $api->setSession(new Session($shop->name, $shop->password));

    $response = $api->rest('GET', '/admin/webhooks.json');

    $response = json_decode(json_encode($response));
    dd($response);
});

Route::get('/login', function () {
    return redirect('https://www.21spirit.com/account/logout');
})->name('login');
