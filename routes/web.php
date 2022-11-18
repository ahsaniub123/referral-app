<?php

use Illuminate\Support\Facades\Route;

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


Route::group(['middleware' => ['verify.shopify'] ], function () {

    Route::group(['prefix' => 'admin'], function () {
        Route::get('/', "AdminDashboardController@index")->name('home');
        Route::get('/shopify', "AdminDashboardController@users")->name('shopify.index');
        Route::get('/settings', 'AdminDashboardController@settings')->name('settings.index');
        Route::post('/settings-save', 'AdminDashboardController@save_settings')->name('settings.save');

    });
});


Route::group(['prefix' => 'shopify'], function() {
    Auth::routes();
});

Route::group(['prefix' => 'shopify',
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

});
