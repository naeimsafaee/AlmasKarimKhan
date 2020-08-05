<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Pluck;
use App\UnitCategory;
use Illuminate\Http\Request;
use Validator;
class PluckController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'integer|required'
        ]);

//        if ($validator->fails()) {
//            return response()->json([
//                "responseCode" => 401,
//                "errorCode" => 'incomplete data',
//                'message' => $validator->errors(),
//
//            ], 401);
//        }


        if( isset($request->q))
            $plucks = Pluck::query()->where('floor' , $request->q)->orWhere('number' , $request->q)->get();
        else{
            $plucks = Pluck::all();
        }





        return response()->json($plucks, 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'integer|required',
            'floor' => 'integer|required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }
        $pluck = Pluck::create([
            'number' => $request->number,
            'floor' => $request->floor,
        ]);
        return response()->json($pluck, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $pluck=Pluck::findOrFail($id);
        return response()->json($pluck, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'integer|required',
            'floor' => 'integer|required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }
        $pluck = Pluck::findOrFail($id);
        $pluck->number=$request->number;
        $pluck->floor=$request->floor;
        $pluck->save();

        return response()->json($pluck, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Pluck::findOrFail($id)->delete();
        return response()->json('deleted', 200);

    }
}
