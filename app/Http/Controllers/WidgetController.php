<?php

namespace App\Http\Controllers;

use App\Setting;
use App\User;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function index(Request $request) {

        if(!$request->filled('customer_id'))
            return response()->json(['status' => 'error']);

        $user = User::where('shopify_id', $request->customer_id)->first();

        if(!$user)
            return response()->json(['status' => 'error']);


        if($user && $user->subscription == 1 && !$user->subscription_end_at->isPast() && $user->deactive == 0) {

            $setting = Setting::first();
            return response()->json(['status' => 'success', 'discount_code' => $setting->discount_code]);
        }

        return response()->json(['status' => 'error']);
    }
}
