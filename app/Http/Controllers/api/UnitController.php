<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Image;
use App\Pluck;
use App\Unit;
use App\UnitCategory;
use App\UnitDiscount;
use App\UnitStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class UnitController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $units = Unit::where('name', 'like', '%' . $request->q . '%')->get();
        $i = 0;
        foreach ($units as $unit) {
            $units[$i]->image = Image::find($unit->image_id)->image_path;

            @$units[$i]->slider_image = Image::find($unit->slide_image_id)->image_path;

            @$units[$i]->vitrin_image = Image::find($unit->vitrin_image_id)->image_path;

            $units[$i]->pluck = Pluck::find($unit->pluck_id);
            $units[$i]->unit_category = UnitCategory::find($unit->unit_category_id);
            $units[$i]->unit_status = UnitStatus::find($unit->unit_status_id);
            $discount = UnitDiscount::where('unit_id', $unit->id)->count();
            $units[$i]->discount = $discount != 0 ? UnitDiscount::where('unit_id', $unit->id)->first()->discount : null;
            $i++;
        }

        return response()->json($units, 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|required',
            'last_name' => 'string|required',
            'email' => 'required',
            'password' => 'required',
            'discount' => 'integer',
            'unit_status_id' => 'required|exists:unit_statuses,id',
            //            'slide_image' => 'required',
            'description' => 'required',
            'image' => 'required',
            'unit_category_id' => 'required|exists:unit_categories,id',
            //            'vitrin_image' => 'required',
            'slug' => 'required|unique:units',
            'pluck_number' => 'required',
            'pluck_floor' => 'required',
            'phone_number' => 'required',
            'postal_code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $slide_image_id = null;
        if (isset($request->slide_image)) {
            $file = $request->file('slide_image');
            if ($file != null) {
                $extention = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extention;
                $file->move(public_path('app/images/unit/slide_image/'), $filename);
                $slide_image_id = Image::create([
                    'image_path' => '/app/images/unit/slide_image/' . $filename,
                    'image_type' => $file->getClientMimeType(),
                ])->id;
            }
        }

        $vitrin_image_id = null;
        if (isset($request->slide_image)) {
            $file = $request->file('vitrin_image');
            if ($file != null) {
                $extention = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extention;
                $file->move(public_path('app/images/unit/vitrin_image/'), $filename);
                $vitrin_image_id = Image::create([
                    'image_path' => '/app/images/unit/vitrin_image/' . $filename,
                    'image_type' => $file->getClientMimeType(),

                ])->id;
            }
        }

        $file = $request->file('image');
        $extention = $file->getClientOriginalExtension();
        $filename = time() . '.' . $extention;
        $file->move(public_path('app/images/unit/image/'), $filename);
        $image = Image::create([
            'image_path' => '/app/images/unit/image/' . $filename,
            'image_type' => $file->getClientMimeType(),

        ]);

        $pluck_id = Pluck::UpdateOrCreate([
            "number" => $request->pluck_number,
            "floor" => $request->pluck_floor,
        ])->id;

        $unit = Unit::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'unit_status_id' => $request->unit_status_id,
            'slide_image_id' => $slide_image_id,
            'description' => $request->description,
            'image_id' => $image->id,
            'unit_category_id' => $request->unit_category_id,
            'vitrin_image_id' => $vitrin_image_id,
            'slug' => $request->slug,
            'pluck_id' => $pluck_id,
            'phone_number' => $request->phone_number,
            'postal_code' => $request->postal_code,
        ]);

        if (isset($request->discount)) {
            UnitDiscount::create([
                'unit_id' => $unit->id,
                'discount' => $request->discount,

            ]);
        }

        return response()->json($unit, 200);


    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $unit = Unit::findOrFail($id);
        $i = 0;
        $unit->image = Image::find($unit->image_id)->image_path;
        $unit->slider_image = Image::find($unit->slide_image_id)->image_path;
        $unit->vitrin_image = Image::find($unit->vitrin_image_id)->image_path;
        $unit->pluck = Pluck::find($unit->pluck_id);
        $unit->unit_category = UnitCategory::find($unit->unit_category_id);
        $unit->unit_status = UnitStatus::find($unit->unit_status_id);
        $discount = UnitDiscount::where('unit_id', $unit->id)->count();
        $unit->discount = $discount != 0 ? UnitDiscount::where('unit_id', $unit->id)->first()->discount : null;


        return response()->json($unit, 200);
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'last_name' => 'string',
            'unit_status_id' => 'exists:unit_statuses,id',
            'unit_category_id' => 'exists:unit_categories,id',
            'slug' => 'unique:units,slug,' . $id,
//            'pluck_number' => 'required',
//            'pluck_floor' => 'required',
            'phone_number' => 'integer',

        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $oldUnit = Unit::findOrFail($id);

        if ($request->file('slide_image')) {
            $file = $request->file('slide_image');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('app/images/unit/slide_image/'), $filename);
            $slide_image = Image::create([
                'image_path' => '/app/images/unit/slide_image/' . $filename,
                'image_type' => $file->getClientMimeType(),

            ])->id;
        }

        if ($request->file('vitrin_image')) {
            $file = $request->file('vitrin_image');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('app/images/unit/vitrin_image/'), $filename);
            $vitrin_image = Image::create([
                'image_path' => '/app/images/unit/vitrin_image/' . $filename,
                'image_type' => $file->getClientMimeType(),

            ])->id;
        }

        if ($request->file('image')) {
            $file = $request->file('image');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('app/images/unit/image/'), $filename);
            $image = Image::create([
                'image_path' => '/app/images/unit/image/' . $filename,
                'image_type' => $file->getClientMimeType(),
            ])->id;
        }
        $pluck_id = 0;
        if (isset($request->pluck_number) && isset($request->pluck_floor)) {
            $pluck_id = Pluck::UpdateOrCreate([
                "number" => $request->pluck_number,
                "floor" => $request->pluck_floor,
            ])->id;
        }


        $unit = Unit::updateOrCreate(['id' => $id], [
            'name' => isset($request->name) ? $request->name : $oldUnit->name,
            'last_name' => isset($request->last_name) ? $request->last_name : $oldUnit->last_name,
            'email' => isset($request->email) ? $request->email : $oldUnit->email,
            'password' => isset($request->password) ? Hash::make($request->password) : $oldUnit->password,
            'unit_status_id' => isset($request->unit_status_id) ? $request->unit_status_id : $oldUnit->unit_status_id,
            'slide_image_id' => $request->file('slide_image') ? $slide_image : $oldUnit->slide_image_id,
            'description' => isset($request->description) ? $request->description : $oldUnit->description,
            'image_id' => $request->file('image') ? $image : $oldUnit->image_id,
            'unit_category_id' => isset($request->unit_category_id) ? $request->unit_category_id : $oldUnit->unit_category_id,
            'vitrin_image_id' => $request->file('vitrin_image') ? $vitrin_image : $oldUnit->vitrin_image_id,
            'slug' => isset($request->slug) ? $request->slug : $oldUnit->slug,
            'pluck_id' => $pluck_id===0?$oldUnit->pluck_id:$pluck_id,
            'phone_number' => isset($request->phone_number) ? $request->phone_number : $oldUnit->phone_number,
            'postal_code' => isset($request->postal_code) ? $request->postal_code : $oldUnit->postal_code,

        ]);
        if (isset($request->discount)) {
            UnitDiscount::where('unit_id', $id)->delete();
            UnitDiscount::create([
                'unit_id' => $unit->id,
                'discount' => $request->discount,

            ]);
        }
        return response()->json($unit, 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id)->delete();
        return response()->json('deleted', 200);

    }

    public function get_units(Request $request)
    {

        $units = Unit::paginate(12);

        foreach ($units as $unit) {
            $unit->slide;
            $unit->image;
            $unit->vitrin;
            $unit->category;
            $unit->pluck;
        }

        $collection = $units->getCollection();

        return response()->json([
            "data_count" => count($collection),
            "current_page" => (int)$request->page,
            "total_count" => $units->total(),
            "total_pages" => ceil($units->total() / 10),
            "data" => $collection,
        ], 200);
    }

    public function get_unit($id)
    {

        $unit = Unit::find($id);
        if ($unit == null)
            return response()->json(["error" => ["message" => "unit not found!"]], 404);

        $unit->slide;
        $unit->image;
        $unit->vitrin;
        $unit->gallery;
        $unit->category;
        $unit->pluck;
//        $unit->article;
        $unit["product"] = $unit->product()->where('product_status_id', 2)->where('status', 1)->get();
        $unit["article"] = $unit->article()->where('status', 1)->get();
        $unit->top_product;

        $articles = $unit["article"];

        foreach ($articles as $article) {
            $article["body"] = mb_substr($article["body"], 0, 400);
        }

        //        $products = $unit["products"];
        //        foreach($products as $product){
        //            unset($product["desc"]);
        //        }

        return response()->json($unit, 200);
    }

}
