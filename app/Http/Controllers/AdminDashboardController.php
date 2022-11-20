<?php

namespace App\Http\Controllers;

use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

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

        $settings->subscription_amount = $request->subscription_amount;
        $settings->save();

        return Redirect::tokenRedirect('settings.index', ['notice' => 'Settings Saved Successfully']);
    }

    public function delete_user($id) {

        $user = User::find($id);
        $user->delete();

        return Redirect::tokenRedirect('shopify.index', ['notice' => 'User Deleted Successfully']);
    }

    public function change_user_status($id) {

        $user = User::find($id);
        $user->deactive = !$user->deactive;
        $user->save();

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
