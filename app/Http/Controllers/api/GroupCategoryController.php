<?php

namespace App\Http\Controllers\api;

use App\Category;
use App\GroupCategory;
use App\Http\Controllers\Controller;
use App\Image;
use Illuminate\Http\Request;
use Validator;
class GroupCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $categories = GroupCategory::orderBy('created_at', 'ASC')->get();
        $i = 0;
        foreach ($categories as $category) {
            if ($category->image_id !== null) $categories[$i]->image_url = Image::find($category->image_id)->image_path;
            $i++;
        }

        return response()->json($categories, 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|required',
            'slug' => 'string|required|unique:group_categories',
            'image' => 'image',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        if ($request->file('image')) {
            $file = $request->file('image');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('app/images/group_category/'), $filename);
            $image = Image::create([
                'image_path' => '/app/images/group_category/' . $filename,
                'image_type' => $file->getClientMimeType(),

            ]);
            $category = GroupCategory::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'image_id' => $image->id,

            ]);
        } else {
            $category = GroupCategory::create([
                'name' => $request->name,
                'slug' => $request->slug,
            ]);
        }
        return response()->json($category, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = GroupCategory::findOrFail($id);
        if ($category->image_id !== null) $category->image_url = Image::find($category->image_id)->image_path;

        return response()->json($category, 200);
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
        $validator = Validator::make($request->all(), [
            'name' => 'string|required',
            'slug' => 'string|required|unique:group_categories',
            'image' => 'image',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $category = GroupCategory::findOrFail($id);
        if ($category->image_id !== null) {
            $category->image_url = Image::find($category->image_id)->image_path;
            unlink(public_path($category->image_url));

        }
        if ($request->file('image')) {
            $file = $request->file('image');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('app/images/group_category/'), $filename);
            $image = Image::create([
                'image_path' => '/app/images/group_category/' . $filename,
                'image_type' => $file->getClientMimeType(),

            ]);
            $category = GroupCategory::updateOrCreate(
                ['id' => $id],
                [
                    'name' => $request->name,
                    'slug' => $request->slug,
                    'image_id' => $image->id,

                ]);
        } else {
            $category = GroupCategory::updateOrCreate(
                ['id' => $id],
                [
                    'name' => $request->name,
                    'slug' => $request->slug,
                    'image_id' => null,

                ]);
        }

        return response()->json($category, 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        GroupCategory::findOrFail($id)->delete();
        $category=new \stdClass();
        $category->message='deleted';
        return response()->json($category, 200);
    }
}
