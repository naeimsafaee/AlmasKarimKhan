<?php

namespace App\Http\Controllers\api\units;

use App\Http\Controllers\Controller;
use App\Image;
use App\Unit;
use Validator;
use App\UnitArticle;
use App\UnitArticleKeyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitArticleController extends Controller{

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){

        $unit_id = Auth::user()->id;

        $articles = UnitArticle::where('unit_id', $unit_id)->orderBy('created_at', 'DESC')->get();
        $i = 0;
        foreach($articles as $article){
            $articles[$i]->image = Image::find($article->image_id)->image_path;
            $articles[$i]->unit = Unit::find($article->unit_id);
            $articles[$i]->keywords = UnitArticleKeyword::where('unit_article_id', $article->id)->get();

            $i++;
        }
        return response()->json($articles, 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'title' => 'string|required',
            'body' => 'string|required',
            'seo_desc' => 'string',
            'image' => 'image|required',
            'thumbnail_image' => 'image',
            'status' => 'integer',
            'keywords' => 'string',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $unit_id = Auth::user()->id;


        $thumbnail_image_id = null;
        if($request->has("thumbnail_image")){
            $file_1 = $request->file('thumbnail_image');
            $extention = $file_1->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file_1->move(public_path('app/images/unit_article/image/'), $filename);
            $thumbnail_image_id = Image::create([
                'image_path' => '/app/images/unit_article/image/' . $filename,
                'image_type' => $file_1->getClientMimeType(),
            ])->id;
        }


        $file = $request->file('image');
        $extention = $file->getClientOriginalExtension();
        $filename = time() . '.' . $extention;
        $file->move(public_path('app/images/unit_article/image/'), $filename);
        $image_id = Image::create([
            'image_path' => '/app/images/unit_article/image/' . $filename,
            'image_type' => $file->getClientMimeType(),
        ])->id;

        $status = 1;
        if($request->has("status")){
            $status = $request->status;
        }

        $unit_article_id = UnitArticle::create([
            "unit_id" => $unit_id,
            "title" => $request->title,
            "body" => $request->body,
            "image_id" => $image_id,
            "thumbnail_image" => $thumbnail_image_id,
            "seo_desc" => $request->seo_desc,
            "status" => $status,
        ])->id;

        $keywords = explode(",", $request->keywords);
        foreach($keywords as $keyword){
            UnitArticleKeyword::create([
                "unit_article_id" => $unit_article_id,
                "keyword" => $keyword,
            ]);
        }

        return response()->json([
            "success" => "article added successfully!",
            "unit_article_id" => $unit_article_id,
        ], 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){

        $article = UnitArticle::findOrFail($id);
        $article->image = Image::find($article->image_id)->image_path;
        $article->unit = Unit::find($article->unit_id);
        $article->keywords = UnitArticleKeyword::where('unit_article_id', $article->id)->get();

        return response()->json($article, 200);
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'title' => 'string',
            'body' => 'string',
            'seo_desc' => 'string',
            'status' => 'integer',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $unit_article = UnitArticle::find($id);

        if($unit_article == null)
            return response()->json(["error" => "article not found!"], 404);


        if(isset($request->image)){
            $file = $request->file('image');
            if($file != null){
                $extention = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extention;
                $file->move(public_path('app/images/unit_article/image/'), $filename);
                $image_id = Image::create([
                    'image_path' => '/app/images/unit_article/image/' . $filename,
                    'image_type' => $file->getClientMimeType(),
                ])->id;
                $unit_article->image_id = $image_id;
            }

        }

        if($request->has("title"))
            $unit_article->title = $request->title;
        if($request->has("body"))
            $unit_article->body = $request->body;
        if($request->has("seo_desc"))
            $unit_article->seo_desc = $request->seo_desc;
        if($request->has("status"))
            $unit_article->status = $request->status;

        $unit_article->save();

        return response()->json(["success" => "article updated successfully!"], 200);
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
