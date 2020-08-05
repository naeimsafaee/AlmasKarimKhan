<?php

namespace App\Http\Controllers\api;

use App\Attribute;
use App\AttributeOption;
use App\AttributePrice;
use App\GroupAttribute;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class AttributePriceController extends Controller{

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        return response()->json(GroupAttribute::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            //            'attribute_id' => 'integer|required|exists:attributes,id',
            'price' => 'integer|required',
            'api_url' => 'string',
            'attribute_option_id' => 'required|integer|exists:attribute_options,id',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        AttributePrice::create([
            "attribute_id" => $request->attribute_id,
            "price" => $request->price,
            "api_url" => $request->api_url,
            "attribute_option_id" => $request->attribute_option_id,
        ]);

        return response()->json(["success" => "attribute price added successfully!"], 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){
        $attribute = AttributePrice::find($id);
        return response()->json($attribute, 200);
    }

    public function show_with_attribute_id($id){
        $attribute = AttributePrice::where("attribute_id", $id)->get();
        return response()->json($attribute, 200);
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'price' => 'integer',
            'attribute_option_id' => 'integer|exists:attribute_options,id',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $attribute = AttributePrice::query()->findOrfail($id);
        
        if($request->has("price"))
            $attribute->price = $request->price;
            
        if($request->has("attribute_option_id"))
            $attribute->attribute_option_id = $request->attribute_option_id;
            
        if($request->has("attribute_id"))
            $attribute->attribute_id = $request->attribute_id;

        $attribute->save();
        
        return response()->json(["success" => "attribute changed successfully!"], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        $attribute = AttributePrice::query()->findOrFail($id);
        $attribute->delete();
        return response()->json("deleted");
    }
}
