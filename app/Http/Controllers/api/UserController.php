<?php

namespace App\Http\Controllers\api;

use App\Address;
use App\City;
use App\Http\Controllers\Controller;
use App\Image;
use App\Pluck;
use App\Product;
use App\Province;
use App\UnitCategory;
use App\UnitDiscount;
use App\UnitStatus;
use App\User;
use App\UserFavorits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $users = User::orderBy('created_at', 'DESC')->paginate(50);

        $i = 0;
        foreach($users as $user){

            if($user->image_id !== null)
                $user->image = Image::find($user->image_id)->image_path;
            if($user->default_address_id !== null)
                $user->default_address = Address::find($user->default_address_id);

            if($user["city_id"] != null)
                $user->city = City::find($user->city_id)->name;
            if($user["province_id"] != null)
                $user->province = Province::find($user->province_id)->name;
            $i++;
        }

        return response()->json($users, 200);

    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        //
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){
        $user = User::findOrFail($id);

        if($user->image_id !== null)
            $user->image = Image::find($user->image_id)->image_path;
        if($user->default_address_id !== null)
            $user->default_address = Address::find($user->default_address_id);

        if($user["city_id"] != null)
            $user->city = City::find($user->city_id)->name;
        if($user["province_id"] != null)
            $user->province = Province::find($user->province_id)->name;

        return response()->json($user, 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show1(){
        $user = Auth::user();

        if($user->image_id !== null)
            $user->image = Image::find($user->image_id)->image_path;
        if($user->default_address_id !== null)
            $user->default_address = Address::find($user->default_address_id);

        if($user["city_id"] != null)
            $user->city = City::find($user->city_id)->name;
        if($user["province_id"] != null)
            $user->province = Province::find($user->province_id)->name;

        return response()->json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'name' => 'string|nullable',
            'email' => 'email|nullable',
            'password' => 'string|nullable|min:6',
            'last_name' => 'string|nullable',
            'mobile' => 'integer|nullable|digits:10|unique:users,mobile,' . $id,
            //            'image' => 'image|nullable',
            'postal_code' => 'integer|nullable|digits:10',
             'address' => 'string|required',
            'personal_code' => 'integer|nullable',
            'home_number' => 'integer|nullable',
            'city_id' => 'integer|exists:cities,id|nullable',
            'province_id' => 'integer|exists:province,id|nullable',
            'gender' => 'integer|nullable',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }
        $old_user = User::findOrFail($id);

        //        if($request->file('image')){
        //            $file = $request->file('image');
        //            $extention = $file->getClientOriginalExtension();
        //            $filename = time() . '.' . $extention;
        //            $file->move(public_path('app/images/user/image/'), $filename);
        //            $image = Image::create([
        //                'image_path' => '/app/images/user/image/' . $filename,
        //                'image_type' => $file->getClientMimeType(),
        //            ]);
        //        }

        $address_id=Address::UpdateOrCreate([
            'address'=>$request->address
        ])->id;

        $user = User::updateOrCreate(['id' => $id], [
            'name' => isset($request->name) ? $request->name : $old_user->name,
            'email' => isset($request->email) ? $request->email : $old_user->email,
            'password' => isset($request->password) ? Hash::make($request->password) : $old_user->password,
            'last_name' => isset($request->last_name) ? $request->last_name : $old_user->last_name,
            'mobile' => isset($request->mobile) ? $request->mobile : $old_user->mobile,
            //            'image_id' => $request->file('image') ? $image->id : $old_user->image_id,
            'postal_code' => isset($request->postal_code) ? $request->postal_code : $old_user->postal_code,
             'default_address_id' => $address_id,
            'personal_code' => isset($request->personal_code) ? $request->personal_code : $old_user->personal_code,
            'home_number' => isset($request->home_number) ? $request->home_number : $old_user->home_number,
            'city_id' => isset($request->city_id) ? $request->city_id : $old_user->city_id,
            'province_id' => isset($request->province_id) ? $request->province_id : $old_user->province_id,
        ]);
        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        User::findOrFail($id)->delete();
        return response()->json('deleted', 200);
    }

    public function add_to_favorite($id){

        $product = Product::findOrFail($id);

        $user = Auth::user();

        UserFavorits::query()->updateOrCreate([
            "user_id" => $user["id"],
            "product_id" => $id,
        ]);

        return response()->json(["success" => "marked successfully"], 200);
    }

    public function delete_from_favorite($id){

        $product = Product::findOrFail($id);

        $user = Auth::user();

        UserFavorits::query()->where([
            "user_id" => $user["id"],
            "product_id" => $id,
        ])->delete();

        return response()->json(["success" => "unmarked successfully"], 200);
    }

    public function index_favorites(){

        $user = Auth::user();
        $favs=UserFavorits::where("user_id", $user["id"])->get();
        foreach ($favs as $fav){
            $fav->product;
        }
        return response()->json($favs, 200);
    }

}
