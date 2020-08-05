<?php

namespace App\Http\Controllers\api;

use App\Article;
use App\ArticleKeyword;
use App\Http\Controllers\Controller;
use App\Image;
use App\Keyword;
use App\Unit;
use App\UnitArticle;
use App\UnitArticleKeyword;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller{

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){

        $articles = Article::orderBy('created_at', 'DESC')->get();
        $i = 0;
        foreach($articles as $article){
            $article->keywords;
            $article->image;
            $article->article_category;
            $article->thumbnailImage;

            /* $article->image = Image::find($article->image_id)->image_path;
             $article->unit = Unit::find($article->unit_id);
             $article->keywords = UnitArticleKeyword::where('unit_article_id', $article->id)->get();*/

        }
        return response()->json($articles, 200);
    }

    public function index1(){

        $articles = Article::orderBy('created_at', 'DESC')->paginate(10);
        $i = 0;
        foreach($articles as $article){
            $article->keywords;
            $article->image;
            $article->article_category;
            $article->thumbnailImage;

            /* $article->image = Image::find($article->image_id)->image_path;
             $article->unit = Unit::find($article->unit_id);
             $article->keywords = UnitArticleKeyword::where('unit_article_id', $article->id)->get();*/

        }
        return response()->json($articles, 200);
    }

    public function get_article(){

        $slide_article = Article::query()->where('status',1)->orderBy("created_at", "DESC")->limit(6)->get();
        $article = Article::query()->where('status',1)->orderBy("created_at", "DESC")->paginate(10);


        return response()->json(["slide_article" => $slide_article, "article" => $article], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'article_category_id' => 'integer|required|exists:article_categories,id',
            'title' => 'string|required',
            'body' => 'string|required',
            'image' => 'image',
            'thumbnail_image' => 'image',
            'slug' => 'string',
            'seo_desc' => 'string',
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

        $admin_id = Auth::user()->id;

        $thumbnail_image_id = null;
        if($request->has("thumbnail_image")){
            $file_1 = $request->file('thumbnail_image');
            $extention = $file_1->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file_1->move(public_path('app/images/article/image/'), $filename);
            $thumbnail_image_id = Image::create([
                'image_path' => '/app/images/article/image/' . $filename,
                'image_type' => $file_1->getClientMimeType(),
            ])->id;
        }

        $image_id = null;
        if($request->has("image")){
            $file = $request->file('image');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('app/images/article/image/'), $filename);
            $image_id = Image::create([
                'image_path' => '/app/images/article/image/' . $filename,
                'image_type' => $file->getClientMimeType(),
            ])->id;
        }

        $status = 1;
        if($request->has("status"))
            $status = $request->status;

        $slug = null;
        if($request->has("slug"))
            $slug = $request->slug;

        $seo_desc = null;
        if($request->has("seo_desc"))
            $seo_desc = $request->seo_desc;

        $article_id = Article::create([
            "admin_id" => $admin_id,
            "article_category_id" => $request->article_category_id,
            "title" => $request->title,
            "body" => $request->body,
            "image_id" => $image_id,
            "thumbnail_image" => $thumbnail_image_id,
            "slug" => $slug,
            "seo_desc" => $seo_desc,
            "status" => $status,
        ])->id;

        $keywords = explode(",", $request->keywords);
        foreach($keywords as $keyword){
            $keyword_id = Keyword::create([
                "name" => $keyword,
            ])->id;

            ArticleKeyword::create([
                "article_id" => $article_id,
                "keyword_id" => $keyword_id,
            ]);
        }

        return response()->json([
            "success" => "article added successfully!",
            "unit_article_id" => $article_id,
        ], 200);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){

        $article = Article::find($id);
        if($article == null)
            return response()->json(["error" => ["message" => "article not found!"]], 404);

        $article->article_category;
        $article->keywords;
        $article->image;
        $article->thumbnailImage;

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
            'article_category_id' => 'integer|exists:article_categories,id',
            'title' => 'string',
            'body' => 'string',
            'slug' => 'string',
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

        $article = Article::query()->find($id);

//        $admin_id = Auth::user()->id;

        if($request->has("thumbnail_image")){
            $file_1 = $request->file('thumbnail_image');
            $extention = $file_1->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file_1->move(public_path('app/images/article/image/'), $filename);
            $thumbnail_image_id = Image::create([
                'image_path' => '/app/images/article/image/' . $filename,
                'image_type' => $file_1->getClientMimeType(),
            ])->id;
            $article->thumbnail_image = $thumbnail_image_id;
        }


        if($request->exists("image")){
            $file = $request->file('image');
            if(!empty($file)){
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('app/images/article/image/'), $filename);
            $image_id = Image::create([
                'image_path' => '/app/images/article/image/' . $filename,
                'image_type' => $file->getClientMimeType(),
            ])->id;
            $article->image_id = $image_id;
            }
        }

        if($request->has("status"))
            $article->status = $request->status;

        if($request->has("slug"))
            $article->slug = $request->slug;

        if($request->has("seo_desc"))
            $article->seo_desc = $request->seo_desc;

        if($request->has("title"))
            $article->title = $request->title;

        if($request->has("body"))
            $article->body = $request->body;

        if($request->has("article_category_id"))
            $article->article_category_id = $request->article_category_id;

        $article->save();
       /*
        $article_id = Article::create([
            "admin_id" => $admin_id,
            "article_category_id" => $request->article_category_id,
            "title" => $request->title,
            "body" => $request->body,
            "image_id" => $image_id,
            "thumbnail_image" => $thumbnail_image_id,
            "slug" => $slug,
            "seo_desc" => $seo_desc,
            "status" => $status,
        ])->id;*/

        return response()->json([
            "success" => "article updated successfully!",
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        Article::findOrFail($id)->delete();

        return response()->json('deleted', 200);
    }
}
