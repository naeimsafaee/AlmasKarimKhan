<?php

namespace App\Http\Controllers\api\units;

use App\Http\Controllers\Controller;
use App\Product;
use App\UserOrders;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user_id = Auth::user()->id;


//        $products = Product::paginate(10);

        $MainOrder = [];
        $i = 0;
//        foreach($products as $product){
        $orders = UserOrders::join('products', 'products.id', '=', 'user_orders.product_id')->where('unit_id',$user_id)
            ->select('*','user_orders.id as me_id')
->
        orderBy('user_orders.id', 'DESC')->paginate(20);

//            if($product["order"] != null){
//                $orders = $product["order"];
        foreach ($orders as $order) {
            $order->user;
            $order->product;
            $order->product->unit;
            $order['id']=$order->me_id;
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

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
            'status' => 'integer|exists:order_statuses,id',
        ]);

        $unit_id = Auth::user()->id;

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $order = UserOrders::find($id);
        if ($order == null)
            return response()->json(["error" => "order not found!"], 404);

        $order->product;

        if ($order["product"]["unit_id"] != $unit_id)
            return response()->json(["error" => "u do not have access to this order!"], 403);

        $order->order_status_id = $request->status;
        $order->save();

        return response()->json(["success" => "order chnaged successfully!"], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {

        $order = UserOrders::find($id);
        if ($order == null)
            return response()->json(["error" => "order not found!"], 404);
        $order->delete();
        return response()->json(["success" => "order deleted successfully"], 200);
    }

}
