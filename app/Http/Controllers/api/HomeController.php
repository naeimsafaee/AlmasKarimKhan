<?php

namespace App\Http\Controllers\api;

use App\Address;
use App\Brands;
use App\Control;
use App\daily_visit;
use App\Http\Controllers\Controller;
use App\Product;
use App\Unit;
use App\UnitDiscount;
use App\User;
use Carbon\Carbon;
use Validator;
use App\UserOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        $slides = Control::where("title", "Slide")->get();
        foreach ($slides as $slide) {
            $slide["is_video"] = $slide["opt_1"];
            unset($slide["opt_1"]);
        }

        $brands = Brands::all();

        $discounted_units = UnitDiscount::where('discount','!=',0)->get();
        $MainDiscountedUnit = [];
        foreach ($discounted_units as $discounted_unit) {
            $discounted_unit->unit;

            $unit = $discounted_unit["unit"];
            $unit->status;
            $unit->slide;
            $unit->image;
            $unit->vitrin;
            $unit->category;
            $unit->pluck;
            $unit["discount"] = $discounted_unit["discount"];

            $MainDiscountedUnit[] = $unit;
        }

        $units = Unit::where("is_top", 1)->get();
        foreach ($units as $unit) {
            $unit->slide;
            $unit->image;
            $unit->vitrin;
            $unit->category;
            $unit->pluck;
        }

        $main_slide = [];
        $main_slide[] = "http://almaskarimkhan.com/images/main-page-slider.jpg";
        foreach ($slides as $slide) {
            $main_slide[] = $slide;
        }

        return response()->json([
            "slide" => $main_slide,
            "brands" => $brands,
            "discounted_unit" => $MainDiscountedUnit,
            "top_units" => $units,
        ], 200);
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function order(Request $request, $id)
    {

        $user_id = Auth::user()->id;

        $user = User::query()->find($user_id);
        if ($user["personal_code"] === null)
            return response()->json("need", 200);

        $product = Product::find($id);

        if ($product == null)
            return response()->json(["error" => ["message" => "product not found!"]], 404);

        UserOrders::create([
            "product_id" => $id,
            "user_id" => $user_id,
            "order_status_id" => 1,
        ]);

        return response()->json(["success" => "محصول با موفقیت سفارش داده شد!"], 200);
    }

    public function order_of_user()
    {
        $user = Auth::user();

        $orders = UserOrders::query()->where("user_id", $user["id"])->get();
        foreach ($orders as $order) {
            $order->product;
        }
        return response()->json($orders, 200);
    }

    public function complete_info(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'postal_code' => 'string|required',
            'address' => 'string|required',
            'name' => 'string|required',
            'last_name' => 'string|required',
            'personal_code' => 'required|string',
            'home_number' => 'required|string',
            'city_id' => 'required|integer|exists:cities,id',
            'province_id' => 'required|integer|exists:province,id',
            'birthday' => 'string',
            'gender' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $user_id = Auth::user()["id"];
        $user = User::query()->find($user_id);

        $address_id = Address::create([
            "address" => $request->address
        ])->id;

        $user->postal_code = $request->postal_code;
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->personal_code = $request->personal_code;
        $user->home_number = $request->home_number;
        $user->city_id = $request->city_id;
        $user->province_id = $request->province_id;
//        $user->birthday = $request->birthday;
//        $user->gender = $request->gender;
        $user->default_address_id = $address_id;
        $user->save();

        return response()->json("success", 200);
    }

    public function set_home_unit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'unit_ids' => 'required|array',
            'unit_ids.*' => 'exists:units,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $unit_ids = $request->unit_ids;

        if (count($unit_ids) > 4)
            return response()->json("units are more than 4", 403);

        Unit::query()->update(['is_top' => 0]);

        foreach ($unit_ids as $unit_id) {
            $unit = Unit::query()->find($unit_id);
            $unit->is_top = 1;
            $unit->save();
        }
        return response()->json(["success" => "successful"], 200);
    }

    public function delete_home_unit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'unit_ids' => 'required|array',
            'unit_ids.*' => 'exists:units,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $unit_ids = $request->unit_ids;

        foreach ($unit_ids as $unit_id) {
            $unit = Unit::query()->find($unit_id);
            $unit->is_top = 0;
            $unit->save();
        }
        return response()->json(["success" => "successful"], 200);
    }

    public function get_most_sell_product()
    {

        $user_orders = UserOrders::query()->groupBy('product_id')->orderByRaw('COUNT(*) DESC')->limit(5)->get();

        foreach ($user_orders as $user_order) {
            $user_order->product;
            $user_order->product->unit;
        }

        return response()->json($user_orders, 200);
    }


    public function visits_track()
    {
        $best_units=[];
       $best_products=[];
        $all_visits = daily_visit::query()->sum('count');
        $today_visits = daily_visit::query()->whereDate('created_at', Carbon::today())->sum('count');
        $yesterday_visits = daily_visit::query()->whereDate('created_at', Carbon::yesterday())->sum('count');

        $m_product_id = UserOrders::select('product_id')
            ->groupBy('product_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->get();
        $m_unit_id = UserOrders::join('products', 'user_orders.product_id', '=', 'products.id')
            ->select('unit_id')
            ->groupBy('unit_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->get();

        foreach ($m_product_id as $m_prod) {
            $p = new \stdClass();
            $prod = Product::find($m_prod->product_id);
            $p->id = $m_prod->product_id;
            $p->name = $prod->name;
            $p->product_count = $prod->count;
            $p->status = $prod->status;
            $p->discounted = $prod->discounted;
            $p->image = $prod->image_url;
            $p->unit = $prod->unit->name;
            $p->price = $prod->product_price;
            $p->count = UserOrders::where('product_id', $m_prod->product_id)->count();
            $best_products[] = $p;
        }
        foreach ($m_unit_id as $m_unit) {
            $u = new \stdClass();
            $u->id = $m_unit->unit_id;
            $u->name = Unit::find($m_unit->unit_id)->name;
            $u->last_name = Unit::find($m_unit->unit_id)->last_name;
            $u->count = UserOrders::join('products', 'user_orders.product_id', '=', 'products.id')->where('unit_id', $m_unit->unit_id)->count();
            $best_units[] = $u;
        }

        $start = Carbon::now()->subDays(30);

        for ($i = 0; $i <= 30; $i++) {
            $date = $start->copy()->addDays($i)->format('Y-m-d');
            $date = explode("-", $date);
            $dates[] = gregorian_to_jalali($date[0], $date[1], $date[2], "/");
            $datas[] = daily_visit::query()->whereDate('created_at', $start->copy()->addDays($i))->sum('count');
        }


        return response()->json(["success" => ["dates" => $dates, "datas" => $datas, "best_units" => $best_units, "best_products" => $best_products, "all_visits" => $all_visits, 'today_visit' => $today_visits, 'yesterday_visit' => $yesterday_visits]], 200);

    }

    public function vitrin()
    {
        $units = Unit::all();
        $un = array();
        $fl0=array();
        $fl1=array();
        foreach ($units as $unit) {
            $unit->pluck;
            if ($unit->pluck->floor == "0") {
                if ($unit->vitrin_image_id != null) {
                    $fl0[] = $unit;
                }
            } elseif ($unit->pluck->floor == "1") {
                if ($unit->vitrin_image_id != null) {
                    $fl1[] = $unit;
                }
            }

//            if ($unit->vitrin_image==null)
//                unset($unit);
        }

        $un[]=$fl0;
        $un[]=$fl1;

        return response()->json($un, 200);


    }

}
