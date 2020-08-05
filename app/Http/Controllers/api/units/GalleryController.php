<?php

namespace App\Http\Controllers\api\units;

use App\Http\Controllers\Controller;
use App\Image;
use App\UnitGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class GalleryController extends Controller{

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){

        return response()->json(UnitGallery::where("unit_id" , Auth::user()["id"])->get(),200);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'image' => 'image|required',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }
        $count=UnitGallery::where('unit_id',Auth::user()->id)->count();
        if ((int)$count<7) {
            $file = $request->file('image');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('app/images/product/image/'), $filename);
            $image_id = Image::create([
                'image_path' => '/app/images/product/image/' . $filename,
                'image_type' => $file->getClientMimeType(),
            ])->id;

            UnitGallery::query()->create([
                "unit_id" => Auth::user()["id"],
                "image_id" => $image_id,
            ]);

            return response()->json("success", 200);
        }else{
            return response()->json("max number of images exceeded", 401);

        }
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        //
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
        UnitGallery::query()->findOrFail($id)->delete();
        return response()->json("deleted", 200);
    }

}
