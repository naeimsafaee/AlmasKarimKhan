<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Unit;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class PassportController extends Controller{

    public function add_admin(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'string|required|unique:admins',
            'name' => 'string|required',
            'phonenumber' => 'integer|required|unique:admins',
            'password' => 'string|required',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }
        Admin::Create([
            'email' => $request->email,
            'phonenumber' => $request->phonenumber,
            'name' => $request->name,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['admin created'], 200);


    }

    public function admin_login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:admins,email',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 400,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 400);
        }

        $admin = Admin::where('email', $request->email)->first();
        if(!Hash::check(request()->password, $admin->password)){
            return response()->json(["error" => ["message" => "password is wrong!"]], 400);
        }
        $token = $admin->createToken('Admin')->accessToken;
        $admin->remember_token = $token;
        $admin->save();

        return response()->json(["token" => $token], 200);

    }

    public function unit_login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:units,email',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 400,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 400);
        }

        $unit = Unit::where('email', $request->email)->first();
        if(!Hash::check(request()->password, $unit->password)){
            return response()->json(["error" => ["message" => "password is wrong!"]], 400);
        }
        $token = $unit->createToken('Unit')->accessToken;
        //        $unit->remember_token=$token;
        //        $unit->save();

        return response()->json(["token" => $token], 200);

    }

    /*public function login_user(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 400,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 400);
        }

        $admin = User::where('email', $request->email)->first();
        if(!Hash::check(request()->password, $admin->password)){
            return response()->json(["error" => ["message" => "password is wrong!"]], 403);
        }

        $token = $admin->createToken('User')->accessToken;
        $admin->remember_token = $token;
        $admin->save();

        return response()->json(["token" => $token], 200);
    }*/

    public function user_register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'last_name' => 'required',
            'mobile' => 'required|iran_mobile',
            'email' => 'required|email:rfc,dns',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 400,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 400);
        }

        $password = Hash::make($request->password);

        $old_user = User::where("mobile", $request->mobile)->get();
        if($old_user->count() > 0)
            return response()->json(["error" => "user already exists!"], 401);

        $user = User::create([
            "name" => $request->name,
            "last_name" => $request->last_name,
            "mobile" => $request->mobile,
            "email" => $request->email,
            "password" => $password,
        ]);

        $token = $user->createToken('User')->accessToken;

        return response()->json(["token" => $token], 200);
    }

    public function user_login(Request $request){

        $validator = Validator::make($request->all(), [
            'mobile' => 'iran_mobile',
            'email' => 'email:rfc,dns',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        if(!$request->has("mobile") && !$request->has("email")){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => "Enter at least one of mobile or email!",
            ], 401);
        }

        if($request->has("mobile"))
            $user = User::where('mobile', $request->mobile)->first();
        else
            $user = User::where('email', $request->email)->first();

        if($user == null)
            return response()->json([
                'message' => "user not found!",
            ], 404);

        if(!Hash::check($request->password, $user->password))
            return response()->json(["error" => ["message" => "password is wrong!"]], 400);

        $token = $user->createToken('User')->accessToken;

        return response()->json(["token" => $token], 200);
    }
}
