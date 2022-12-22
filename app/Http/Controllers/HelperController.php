<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class HelperController extends Controller
{
    public function register(Request $request) {

        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails())
            return response()->json(['status' => 'error', 'errors' => $validator->getMessageBag()->toArray()], 400);

        $referrer = User::where('referral_token', $request->referral_token)->where('id', '!=', 1)->first();
        $referrer_id = null;

        if($referrer && $referrer->id != 1 && $referrer->subscription && $referrer->deactive == 0)
            $referrer_id = $referrer->id;

        $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'referral_token' => Str::random(8),
            'referrer_id' => $referrer_id,
        ]);

        $user->assignRole('shopify');

        Auth::login($user);

        return response()->json(['status' => 'success'], 201);
    }

    public function login(Request $request) {

        $data = $request->all();

        $validator = Validator::make($data, [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails())
            return response()->json(['status' => 'error', 'errors' => $validator->getMessageBag()->toArray()], 400);

        $user = User::where('email', $request->email)->first();

        if(!$user)
            return response()->json(['status' => 'error', 'errors' => [ 'email' => 'The email provided do not exists.']]);

        if (!Hash::check($request->password, $user->password))
            return response()->json(['status' => 'error', 'errors' => [ 'password' => 'These credentials do not match our records.']]);

        Auth::login($user);

        return response()->json(['status' => 'success', 'user_id' => $user->id], 200);
    }

}
