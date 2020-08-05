<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Image;
use App\Unit;
use App\UnitArticle;
use App\UnitArticleKeyword;
use Illuminate\Http\Request;
use Validator;

class UnitArticleController extends Controller{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request){
        $validator = Validator::make($request->all(), [
            'unit_id' => 'integer|required',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $articles = UnitArticle::where('unit_id', $request->unit_id)->orderBy('created_at', 'DESC')->get();
        $i = 0;
        foreach($articles as $article){
            $articles[$i]->image = Image::find($article->image_id)->image_path;
            @$articles[$i]->thumbnail_image_id = Image::find($article->thumbnail_image_id)->image_path;
            $articles[$i]->unit = Unit::find($article->unit_id);
            $articles[$i]->keywords = UnitArticleKeyword::where('unit_article_id', $article->id)->get();

            $i++;
        }
        return response()->json($articles, 200);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){

        $article = UnitArticle::findOrFail($id);
        $article->image = Image::find($article->image_id)->image_path;
        @$article->thumbnail_image_id = Image::find($article->thumbnail_image_id)->image_path;
        $article->unit = Unit::find($article->unit_id);
        $article->keywords = UnitArticleKeyword::where('unit_article_id', $article->id)->get();


        return response()->json($article, 200);
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
        UnitArticle::findOrFail($id)->delete();
        return response()->json('deleted', 200);

    }
}
