<?php

namespace App\Http\Controllers\api\units;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeCollection;
use App\Http\Resources\ProductAttributeCollection;
use App\Image;
use App\Product;
use App\ProductAttribute;
use App\Attribute;
use App\ProductStatus;
use App\ProductToImage;
use App\UnitDiscount;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        $products = Product::where("unit_id", Auth::user()->id)->orderBy('id','DESC')->paginate(10);

        return response()->json($products, 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'string|required',
            'count' => 'integer',
            'price' => 'integer|nullable',
            'dollar_price' => 'integer|nullable',
            'gold_18' => 'integer|nullable',
            'gold_24' => 'integer|nullable',
            'discount' => 'integer',
            'category_id' => 'exists:categories,id|required',
            'desc' => 'string|required',
            'product_status_id' => 'integer|required|exists:product_statuses,id',

        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $count = 0;
        if ($request->has("count"))
            $count = $request->count;
        $discount = 0;
        if ($request->has("discount"))
            $discount = $request->discount;


        $product_id = Product::create([
            "name" => $request->name,
            "count" => $count,
            "price" => $request->price,
            "dollar_price" => $request->dollar_price,
            "gold_18" => $request->gold_18,
            "gold_24" => $request->gold_24,
            "discount" => $discount,
            "desc" => $request->desc,
            "category_id" => $request->category_id,
            "unit_id" => Auth::user()->id,
            "product_status_id" => $request->product_status_id,
        ])->id;

        $attribute_ids = explode(',', $request->attribute_ids);
        $attribute_values = explode(',', $request->attribute_values);


        return response()->json(["success" => "product successfully added!", "product_id" => $product_id], 200);
    }


    public function add_attribute(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'product_id' => 'integer|required|exists:products,id',
            'attribute_id' => 'integer|required|exists:attributes,id',
            'value' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        ProductAttribute::create([
            'attribute_id' => $request->attribute_id,
            'product_id' => $request->product_id,
            'value' => $request->value,
            'group_attribute_id' => Attribute::find($request->attribute_id)->group_attribute_id
        ]);

        return response()->json(["success" => "attribute successfully added to product!"], 200);

    }



    public function delete_attribute(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'product_id' => 'integer|required|exists:products,id',
            'attribute_id' => 'integer|required|exists:attributes,id',

        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        ProductAttribute::query()->where('product_id',$request->product_id)->where('attribute_id',$request->attribute_id)->delete();


        return response()->json(["success" => "attribute successfully deleted from product!"], 200);

    }

    public function attribute_by_product_id(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'product_id' => 'integer|required|exists:products,id',

        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $results= ProductAttribute::where('product_id', $request->product_id)->get();
        return new ProductAttributeCollection($results);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = Product::find($id);
        if ($product == null)
            return response()->json(["error" => ["message" => "product not found!"]], 404);

        unset($product["short_desc"]);

        return response()->json($product, 200);
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
            'count' => 'integer',
            'price' => 'integer|nullable',
            'dollar_price' => 'integer|nullable',
            'gold_18' => 'integer|nullable',
            'gold_24' => 'integer|nullable',
            'discount' => 'integer',
            'desc' => 'string',
            'category_id' => 'exists:categories,id',
            'product_status_id' => 'integer|exists:product_statuses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $product = Product::find($id);
        if ($product == null)
            return response()->json(["error" => "product not found!"], 404);

        if ($request->has("name"))
            $product->name = $request->name;
        if ($request->has("count"))
            $product->count = $request->count;
        if ($request->has("price"))
            $product->price = $request->price;
        if ($request->has("dollar_price"))
            $product->dollar_price = $request->dollar_price;
        if ($request->has("gold_18"))
            $product->gold_18 = $request->gold_18;
        if ($request->has("gold_24"))
            $product->gold_24 = $request->gold_24;
        if ($request->has("discount"))
            $product->discount = $request->discount;
        if ($request->has("desc"))
            $product->desc = $request->desc;
        if ($request->has("category_id"))
            $product->category_id = $request->category_id;
        if ($request->has("product_status_id"))
            $product->product_status_id = $request->product_status_id;

        $product->save();

        return response()->json(["success" => "product changed successfully!"], 200);
    }

    public function add_image_to_product(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'image' => 'image|required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        $file = $request->file('image');
        $extention = $file->getClientOriginalExtension();
        $filename = time() . '.' . $extention;
        $file->move(public_path('app/images/product/image/'), $filename);
        $image = Image::create([
            'image_path' => '/app/images/product/image/' . $filename,
            'image_type' => $file->getClientMimeType(),
        ]);

        ProductToImage::create([
            "product_id" => $id,
            "image_id" => $image->id,
        ]);
        return response()->json(["success" => "image added successfully!"], 200);
    }

    public function add_attribute_to_product(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'attribute_id' => 'exists:attributes,id|required',
            'value' => 'string',
            'product_id' => 'exists:products,id|required',
            'group_attribute_id' => 'exists:group_attributes,id|required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),

            ], 401);
        }

        ProductAttribute::query()->create([
            "attribute_id" => $request->attribute_id,
            "value" => $request->value,
            "product_id" => $request->product_id,
            "group_attribute_id" => $request->group_attribute_id,
        ]);

        return response()->json(["success" => "attribute added to product successfully"], 200);
    }

    public function get_attribute_of_product($id)
    {

        $product_attribute = ProductAttribute::query()->where("product_id", $id)->get();

        return response()->json($product_attribute, 200);
    }

    public function delete_attribute_from_product($id)
    {

        ProductAttribute::query()->findOrFail($id)->delete();
        return response()->json("deleted", 200);
    }

    public function delete_image_of_product($id)
    {

        $unit_id = Auth::user()->id;

        $product_to_image = ProductToImage::find($id);
        if ($product_to_image == null)
            return response()->json(["error" => "image not found!"], 404);

        $product = Product::find($product_to_image->product_id);
        if ($product->unit_id != $unit_id)
            return response()->json(["error" => "u do not have access to this image"], 403);

        $product_to_image->delete();

        return response()->json(["success" => "image deleted successfully!"], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product == null)
            return response()->json(["error" => "product not found!"], 404);

        foreach ($product["image_url"] as $image_url) {
            //            unlink($image_url["url"]);
        }

        $product->delete();
        return response()->json(["success" => "product deleted successfully!"], 200);
    }

    public function get_product_statuses()
    {
        return response()->json(ProductStatus::all(), 200);
    }

    public function set_discount_to_unit(Request $request)
    {

        $unit_id = Auth::user()["id"];

        UnitDiscount::query()->updateOrCreate(
            ["unit_id", $unit_id],
            ["discount", $request->dicount]);
        return response()->json("success", 200);
    }

}
