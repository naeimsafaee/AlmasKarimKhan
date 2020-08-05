<?php

namespace App\Http\Controllers\api;

use App\Attribute;
use App\GroupAttribute;
use App\AttributeOption;
use App\AttributePrice;
use App\GroupCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeCollection;
use App\Http\Resources\AttributeGroupCollection;
use Illuminate\Http\Request;
use Validator;

class AttributeController extends Controller{

    /**
     * Display a listing of the resource.
     * @return AttributeCollection
     */
    public function index(){
        $attr=Attribute::all();

        return new AttributeCollection($attr);
    }

    public function get_attr_with_category_id(Request $request)
    {
        $category_id=$request->category_id;
        $results= GroupAttribute::where('category_id', $category_id)->get();
        return new AttributeGroupCollection($results);

    }

    public function get_attr_with_group_attr_id(Request $request)
    {
        $group_attribute_id=$request->group_attribute_id;
        $results= Attribute::where('group_attribute_id', $group_attribute_id)->get();
        $res= new AttributeCollection($results);
        return response()->json($res, 200);

    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'string|required',
            'type' => 'integer|required',//0 for input , 1 for dropdown list , 2 for checkbox
            'description' => 'string',
            'group_attribute_id' => 'required|integer|exists:group_attributes,id',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        Attribute::create([
            "name" => $request->name,
            "type" => $request->type,
            "description" => $request->description,
            "group_attribute_id" => $request->group_attribute_id,
        ]);

        return response()->json(["success" => "attribute added successfully!"], 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){
        $attribute = Attribute::find($id);
        return response()->json($attribute, 200);
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        $attribute = Attribute::query()->findOrFail($id);
        $attribute->delete();
        return response()->json("deleted");
    }

}
