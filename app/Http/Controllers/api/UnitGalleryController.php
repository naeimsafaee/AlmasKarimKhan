<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Image;
use App\Unit;
use App\UnitGallery;
use Illuminate\Http\Request;
use Validator;

class UnitGalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unit_id' => 'integer|required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }
        $galleries = UnitGallery::where('unit_id' , $request->unit_id)->get();
        $i=0;
        foreach ($galleries as $gallery){
            $galleries[$i]->image=Image::find($gallery->image_id)->image_path;
            $galleries[$i]->unit=Unit::find($gallery->unit_id);
            $i++;
        }
        return response()->json($galleries, 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        UnitGallery::findOrFail($id)->delete();
        return response()->json('deleted', 200);

    }
}
