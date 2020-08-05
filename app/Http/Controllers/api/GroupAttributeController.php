<?php

namespace App\Http\Controllers\api;

use App\Attribute;
use App\GroupAttribute;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class GroupAttributeController extends Controller{

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){

        $g=GroupAttribute::all();
        $i=0;
        foreach ($g as $item){
            $g[$i]->name=$item->name.'-'.$item->category->name;
            $item->category;
            $i++;
        }

        return response()->json($g, 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'string|required',
            'description' => 'string|required',
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        GroupAttribute::query()->create([
            "name" => $request->name,
            "category_id" => $request->category_id,
            "description" => $request->description,
            "status" => 1,
        ]);

        return response()->json(["success" => "group attribute added successfully!"], 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){
        $attribute = GroupAttribute::find($id);
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
        $attribute = GroupAttribute::query()->findOrFail($id);
        $attribute->delete();
        return response()->json("deleted");
    }

}
