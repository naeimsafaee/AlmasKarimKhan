<?php

namespace App\Http\Controllers\api;

use App\Comment;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class CommentController extends Controller{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){

        $comments = Comment::paginate(10);

        foreach($comments as $comment){
            $comment->product;
            $comment->user;
            $comment->product->unit;
        }

        return response()->json($comments, 200);
    }

    public function index1(){

        $user = Auth::user();

        $comments = Comment::query()->where("user_id" , $user["id"])
            ->orderBy("created_at" , "DESC")->get();
        foreach($comments as $comment){
            $comment->product;
        }
        return response()->json($comments , 200);
    }


    public function index3(){

        $user = Auth::user();

        $comments = Comment::query()->where("unit_id" , $user["id"])->get();


        foreach ($comments as $comment){
            $comment->product;
        }

            //->orderBy("created_at" , "DESC")->get();
        return response()->json($comments , 200);
    }

    public function index2(){

        $comments = Comment::query()->where("is_active" , "1")->get();

        foreach($comments as $comment){
            $comment->product;
            $comment->user;
        }

        return response()->json($comments, 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'text' => 'string|required',
            'product_id' => 'integer|required|exists:products,id',
            'rate' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $comments = Comment::query()->where("product_id" , $request->product_id)->get();

        Comment::create([
            "text" => $request->text,
            "product_id" => $request->product_id,
            "unit_id" => Product::find($request->product_id)->unit_id,
            "user_id" => Auth::user()["id"] ,
            "rate" => $request->rate
        ]);

        $product = Product::query()->find($request->product_id);
        $product->rate = (($product->rate * $comments->count()) + $request->rate) / ($comments->count() + 1);
        $product->save();

        return response()->json("success" , 200);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){

        $comment = Comment::query()->find($id);
        $comment->is_active = 1;
        $comment->save();

        return response()->json("success" , 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        Comment::query()->find($id)->delete();
        return response()->json("success" , 200);
    }

    public function reply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'string|required',
            'reply_to' =>'integer|exists:comments,id',
            'product_id' => 'integer|required|exists:products,id',
            'rate' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }


        Comment::create([
            "text" => $request->text,
            "product_id" => $request->product_id,
            "reply_to" => $request->reply_to,
            "user_id" => Auth::user()["id"] ,
            "rate" => $request->rate
        ]);


        return response()->json("success" , 200);
    }
}
