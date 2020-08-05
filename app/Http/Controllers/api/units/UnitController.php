<?php

namespace App\Http\Controllers\api\units;

use App\Comment;
use App\Http\Controllers\Controller;
use App\Image;
use App\Pluck;
use App\Product;
use App\Unit;
use App\UnitDiscount;
use App\UserOrders;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();

        $m_product_id = Product::query()->
        join(
            "user_orders",
            "products.id",
            "=",
            "user_orders.product_id"
        )
            ->where('unit_id', $user->id)
            ->select('*', Product::raw('count(*) as total'))
            ->groupBy('product_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(10)
            ->get();


        if (count($m_product_id) != 0) {
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
                $p->price = number_format($prod->product_price);
                $p->count = UserOrders::where('product_id', $m_prod->product_id)->count();
                $p->total = $prod->count;
                $best_products[] = $p;
            }

            return response()->json(["success" => ["best_products" => $best_products]], 200);
        }else{
            return response()->json(["success" => ["best_products" => 'وجود ندارد']], 200);
        }
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $id = Auth::user()->id;

        $oldUnit = Unit::findOrFail($id);
        $oldUnit->slide;
        $oldUnit->image;
        $oldUnit->category;
        $oldUnit->vitrin;
        $oldUnit->pluck;
        $oldUnit->discount;


        return response()->json(["data" => $oldUnit], 200);
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
            'name' => 'string',
            'last_name' => 'string',
            'unit_status_id' => 'exists:unit_statuses,id',
            'unit_category_id' => 'exists:unit_categories,id',
            'slug' => 'unique:units,slug,' . $id,
//            'pluck_number' => 'string',
//            'pluck_floor' => 'string',
//            'phone_number' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $id = Auth::user()->id;

        $oldUnit = Unit::findOrFail($id);

        if ($request->file('slide_image')) {
            $file = $request->file('slide_image');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('app/images/unit/slide_image/'), $filename);
            $slide_image = Image::create([
                'image_path' => '/app/images/unit/slide_image/' . $filename,
                'image_type' => $file->getClientMimeType(),

            ]);
        }
        if ($request->file('vitrin_image')) {
            $file = $request->file('vitrin_image');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('app/images/unit/vitrin_image/'), $filename);
            $vitrin_image = Image::create([
                'image_path' => '/app/images/unit/vitrin_image/' . $filename,
                'image_type' => $file->getClientMimeType(),

            ]);
        }
        if ($request->file('image')) {
            $file = $request->file('image');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('app/images/unit/image/'), $filename);
            $image = Image::create([
                'image_path' => '/app/images/unit/image/' . $filename,
                'image_type' => $file->getClientMimeType(),
            ]);
        }

        $unit = Unit::updateOrCreate(['id' => $id], [
            'name' => isset($request->name) ? $request->name : $oldUnit->name,
            'last_name' => isset($request->last_name) ? $request->last_name : $oldUnit->last_name,
            'email' => isset($request->email) ? $request->email : $oldUnit->email,
            'password' => isset($request->password) ? Hash::make($request->password) : $oldUnit->password,
            'unit_status_id' => isset($request->unit_status_id) ? $request->unit_status_id : $oldUnit->unit_status_id,
            'slide_image_id' => $request->file('slide_image') ? $slide_image : $oldUnit->slide_image_id,
            'description' => isset($request->description) ? $request->description : $oldUnit->description,
            'image_id' => $request->file('image') ? $image : $oldUnit->image_id,
            'unit_category_id' => isset($request->unit_category_id) ? $request->unit_category_id : $oldUnit->unit_category_id,
            'vitrin_image_id' => $request->file('vitrin_image') ? $vitrin_image : $oldUnit->vitrin_image_id,
            'slug' => isset($request->slug) ? $request->slug : $oldUnit->slug,
            'phone_number' => isset($request->phone_number) ? $request->phone_number : $oldUnit->phone_number,
            'postal_code' => isset($request->postal_code) ? $request->postal_code : $oldUnit->postal_code,
        ]);

        $pluck = Pluck::find($unit->pluck_id);
        $pluck->number = $request->pluck_number;
        $pluck->floor = $request->pluck_floor;
        $pluck->save();

        if (isset($request->discount)) {
            UnitDiscount::where('unit_id', $id)->delete();
            UnitDiscount::create([
                'unit_id' => $unit->id,
                'discount' => $request->discount,
            ]);
        }

        return response()->json($unit, 200);
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

    public function get_comment(Request $request)
    {
        $user = Auth::user();

        $comments=Comment::where('is_active',1)->where('unit_id',$user->id)->get();

        foreach ($comments as $comment){
            $comment['product_name']=$comment->product->name;
        }
        return response()->json($comments, 200);

    }
}
