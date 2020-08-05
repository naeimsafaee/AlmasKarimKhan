<?php

namespace App\Http\Controllers\api;

use App\Attribute;
use App\AttributeOption;
use App\AttributePrice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class AttributeOptionController extends Controller{

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(){
        //
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'attribute_id' => 'integer|required|exists:attributes,id',
            'attribute_option_value' => 'string|required',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        AttributeOption::query()->create([
            "attribute_id" => $request->attribute_id,
            "attribute_option_value" => $request->attribute_option_value,
        ]);

        return response()->json(["success" => "attribute option added successfully!"], 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){
        $attribute = AttributeOption::find($id);
        return response()->json($attribute, 200);
    }

    public function show_with_attribute_id($id){
        $attribute = AttributeOption::where("attribute_id", $id)->get();
        $i=0;
        foreach ($attribute as $item){
            $attribute[$i]->price=AttributePrice::where('attribute_option_id',$item->id)->get(['price' ,'id'])->toArray();
            $i++;
        }
        return response()->json($attribute, 200);
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        $attribute = AttributeOption::query()->findOrFail($id);
        $attribute->delete();
        return response()->json("deleted");
    }

}
