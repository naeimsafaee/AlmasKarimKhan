<?php

namespace App\Http\Controllers\api\units;

use App\Category;
use App\Http\Controllers\Controller;
use App\Image;
use Illuminate\Http\Request;

class CategoryController extends Controller{

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $categories = Category::parentsOnly()->orderBy('created_at', 'ASC')->get();
        $i = 0;
        foreach($categories as $category){
            $categories[$i]->childs = Category::where('parent_id', $category->id)->orderBy('created_at', 'ASC')->get();
            if($category->image_id !== null)
                $categories[$i]->image_url = Image::find($category->image_id)->image_path;
            $category->group;
            $category["group_category_name"] = $category["group"]["name"];
            unset($category["group_category_id"]);
            unset($category["group"]);
            $i++;
        }

        return response()->json($categories, 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        //
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
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        //
    }
}
