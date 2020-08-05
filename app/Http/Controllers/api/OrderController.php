<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\OrderStatus;
use App\Product;
use App\UserOrders;
use Illuminate\Http\Request;

class OrderController extends Controller{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){

//        $products = Product::paginate(10);

        $MainOrder = [];
        $i = 0;
//        foreach($products as $product){
        $orders=UserOrders::orderBy('id','DESC')->paginate(20);

//            if($product["order"] != null){
//                $orders = $product["order"];
                foreach($orders as $order){
                    $order->user;
                    $order->product;
                    $order->product->unit;
//                    $order["product_name"] = $order->product->name;
//                    $order["unit_name"] = $order->product->unit->name;

//                    unset($order["user"]["image_id"]);
//                    unset($order["user"]["personal_code"]);
//                    unset($order["user"]["birthday"]);
//                    unset($order["user"]["status"]);
//
//                    $MainOrder[$i] = $order;
//
//                    $i++;
                }
//            }
//        }

        return response()->json(["data" => $orders], 200);
    }

    public function order_status(){
        return response()->json(OrderStatus::all(),200);
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
        $user_id = $id;

        $products = Product::where("unit_id", $user_id)->get();

        $MainOrder = [];
        $i = 0;
        foreach($products as $product){
            $product->order;
            if($product["order"] != null){
                $orders = $product["order"];
                foreach($orders as $order){
                    $order->user;
                    $order["product_name"] = $product["name"];

                    unset($order["user"]["image_id"]);
                    unset($order["user"]["personal_code"]);
                    unset($order["user"]["birthday"]);
                    unset($order["user"]["status"]);

                    $MainOrder[$i] = $order;

                    $i++;
                }
            }
        }

        return response()->json(["data" => $MainOrder], 200);
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
        UserOrders::find($id)->delete();
        return response()->json(["data" => 'حذف شد'], 200);
    }
}
