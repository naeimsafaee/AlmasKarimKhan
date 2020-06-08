<?php

namespace App\Http\Controllers\api;

use App\Category;
use App\Http\Controllers\Controller;
use App\Image;
use Illuminate\Http\Request;
use Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $categories = Category::parentsOnly()->orderBy('created_at', 'ASC')->get();
        $i = 0;
        foreach ($categories as $category) {
            $categories[$i]->childs = Category::where('parent_id', $category->id)->orderBy('created_at', 'ASC')->get();
            if ($category->image_id !== null) $categories[$i]->image_url = Image::find($category->image_id)->image_path;
            $i++;
        }

        return response()->json($categories, 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|required',
            'slug' => 'string',
            'image' => 'image',
            'parent_id' => 'integer|exists:categories,id',
            'group_category_id' => 'integer|exists:group_categories,id'
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
            $file->move(public_path('app/images/category/'), $filename);
            $image = Image::create([
                'image_path' => '/app/images/category/' . $filename,
                'image_type' => $file->getClientMimeType(),

            ]);
            $category = Category::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'image_id' => $image->id,
                'parent_id' => $request->parent_id,
                'group_category_id' => $request->group_category_id,

            ]);
        } else {
            $category = Category::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'parent_id' => $request->parent_id,
                'group_category_id' => $request->group_category_id,

            ]);
        }
        return response()->json($category, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::findOrFail($id);

        if ($category->parent_id != null) {
            $category->parent = Category::find($category->parent_id);
        }
        unset($category['parent_id']);
        return response()->json($category, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|required',
            'slug' => 'string',
            'image' => 'image',
            'parent_id' => 'integer|exists:categories,id',
            'group_category_id' => 'integer|exists:group_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $category = Category::findOrFail($id);
        if ($category->image_id !== null) {
            $category->image_url = Image::find($category->image_id)->image_path;
            unlink(public_path($category->image_url));

        }
        if ($request->file('image')) {
            $file = $request->file('image');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('app/images/category/'), $filename);
            $image = Image::create([
                'image_path' => '/app/images/category/' . $filename,
                'image_type' => $file->getClientMimeType(),

            ]);
            $category = Category::updateOrCreate(
                ['id' => $id],
                [
                    'name' => $request->name,
                    'slug' => $request->slug,
                    'image_id' => $image->id,
                    'parent_id' => $request->parent_id,
                    'group_category_id' => $request->group_category_id,

                ]);
        } else {
            $category = Category::updateOrCreate(
                ['id' => $id],
                [
                    'name' => $request->name,
                    'slug' => $request->slug,
                    'image_id' => null,
                    'parent_id' => $request->parent_id,
                    'group_category_id' => $request->group_category_id,

                ]);
        }

        return response()->json($category, 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        $category=new \stdClass();
        $category->message='deleted';
        return response()->json($category, 200);

    }
}
