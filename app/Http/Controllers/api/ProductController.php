<?php

namespace App\Http\Controllers\api;

use App\Attribute;
use App\AttributeOption;
use App\AttributePrice;
use App\Comment;
use App\GroupCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\GroupCategoryCollection;
use App\Http\Resources\ProductCollection;
use App\Image;
use App\Product;
use App\ProductAttribute;
use App\ProductToImage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Input\Input;
use Validator;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller{

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $products = Product::paginate(10);

        return response()->json($products, 200);
    }

    public function get_product_with_unit_id($id){

        $products = Product::where("unit_id", $id)->get();
        return response()->json($products, 200);
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

        $product = Product::find($id);
        if($product == null)
            return response()->json(["error" => ["message" => "product not found!"]], 404);

        $product->attributes;
        $attributes = $product["attributes"];
        foreach($attributes as $attribute){
            $attribute->attribute->group_attribute;
            $value = $attribute["value"];

            $price = AttributePrice::query()
                ->where("attribute_id" , $attribute["id"]);

            if($attribute["attribute"]["type"] == "2"){
                $attribute["options"] = AttributeOption::find($value);
                $price = $price->orWhere("attribute_option_id" , $value);
            } elseif($attribute["attribute"]["type"] == "1"){
                $attribute["options"] = AttributeOption::find($value);
                $price = $price->orWhere("attribute_option_id" , $value);
            }
            $price = $price->first();
            $attribute["price"] = $price;
        }
        unset($product["short_desc"]);

        $product["related"] = Product::all()->take(5);
        $product["comments"] = Comment::where('product_id',$product->id)->where('is_active',1)->where('reply_to',null)->get();

        $i=0;
        foreach ($product["comments"] as $cm){
            $product["comments"][$i]->replies=  Comment::where('reply_to',$cm->id)->get();
            $i++;
        }

        return response()->json($product, 200);
    }

    public function show_with_unit_id($id){

        $products = Product::query()->where("unit_id" , $id)->where("status" , 1)->paginate(12);

        /*foreach($products as $product){
            $product->attributes;
            $attributes = $product["attributes"];
            foreach($attributes as $attribute){
                $attribute->attribute->group_attribute;
                $value = $attribute["value"];

                $price = AttributePrice::query()
                    ->where("attribute_id" , $attribute["id"]);

                if($attribute["attribute"]["type"] == "2"){
                    $attribute["options"] = AttributeOption::find($value);
                    $price = $price->orWhere("attribute_option_id" , $value);
                } elseif($attribute["attribute"]["type"] == "1"){
                    $attribute["options"] = AttributeOption::find($value);
                    $price = $price->orWhere("attribute_option_id" , $value);
                }
                $price = $price->first();
                $attribute["price"] = $price;
            }
            unset($product["short_desc"]);
        }*/

        return response()->json($products, 200);
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'slug' => 'string',
            'seo_desc' => 'string',
            'count' => 'integer',
            'price' => 'integer',
            'discount' => 'integer',
            'category_id' => 'exists:categories,id',
            'desc' => 'string',
            'product_status_id' => 'string',

        ]);

        if($validator->fails()){
            return response()->json([
                "responseCode" => 401,
                "errorCode" => 'incomplete data',
                'message' => $validator->errors(),
            ], 401);
        }

        $product = Product::find($id);
        if($product == null)
            return response()->json(["error" => ["message" => "product not found!"]], 404);

        /* if($request->hasFile('images')){

             $productToImages = ProductToImage::where("product_id" , $id)->get();
             foreach($productToImages as $productToImage){
                 $productToImage->delete();
             }

             foreach($request->file('images') as $image){


                 $extention = $image->getClientOriginalExtension();
                 $filename = time() . '.' . $extention;
                 $image->move(public_path('app/images/unit/slide_image/'), $filename);

                 $images = Image::create([
                     'image_path' => '/app/images/unit/slide_image/' . $filename,
                     'image_type' => $image->getClientMimeType(),
                 ]);

                 ProductToImage::create([
                     "product_id" => $id,
                     "image_id" => $images->id
                 ]);
             }
         }*/

        if($request->has("name"))
            $product->name = $request->name;
        if($request->has("slug"))
            $product->name = $request->slug;
        if($request->has("seo_desc"))
            $product->name = $request->seo_desc;
        if($request->has("count"))
            $product->name = $request->count;
        if($request->has("price"))
            $product->name = $request->price;
        if($request->has("discount"))
            $product->name = $request->discount;
        if($request->has("desc"))
            $product->name = $request->desc;
        if($request->has("product_status_id"))
            $product->name = $request->product_status_id;
        if($request->has("category_id"))
            $product->category_id = $request->category_id;

        $product->save();

        return response()->json(["success" => ["message" => "product changed successfully!"]], 200);

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        $product = Product::find($id);
        if($product == null)
            return response()->json(["error" => "product not found!"], 404);

        foreach($product["image_url"] as $image_url){
            //            unlink($image_url["url"]);
        }

        $product->delete();
        return response()->json(["success" => "product deleted successfully!"], 200);
    }

    public function delete_image_of_product($id){

        $product_to_image = ProductToImage::find($id);
        if($product_to_image == null)
            return response()->json(["error" => "image not found!"], 404);

        $product_to_image->delete();

        return response()->json(["success" => "image deleted successfully!"], 200);
    }

    public function search_product(Request $request){

        $product = Product::query()->orWhere("name" , "like" , "%" . $request->what . "%")->where('status',1)->paginate();
        return response()->json($product , 200);
    }

    public function search_unit(Request $request){

        $products = Unit::query()->orWhere("name" , "like" , "%" . $request->what . "%")->paginate();

        foreach($products as $product){
            $product->category;
            $product->pluck;
        }

        return response()->json($products , 200);
    }

    public function getProductFilters(){

         if(!request()->exists('unit_id')){

        $Groupcategories = GroupCategory::whereHas('categories')->get();
        $Groupcategories = new GroupCategoryCollection($Groupcategories);
         }else{
             $unit_id = request()->unit_id;

               $Groupcategories = GroupCategory::whereHas('categories',function($q) use ($unit_id){

                            $q->whereHas('products',function($qq) use ($unit_id){
                                $qq->where('unit_id',$unit_id);
                            });
               })->get();

               $Groupcategories = new GroupCategoryCollection($Groupcategories);
         }

        if(!request()->exists('unit_id')){
            $finalArray = $this->getProductsAttributes();
        } else {
            $unit_id = request()->unit_id;
            $unit = Unit::find($unit_id);

            if(!$unit){
                return response()->json(['error' => 'واحدی با این شماره یافت نشد.'], 401);
            }

            if(!$unit->products()->exists()){
                $finalArray = $this->getProductsAttributes();
            }else{

                $productAttributes_attribute_ids = ProductAttribute::Numericvalue()->whereHas('product', function($q) use ($unit_id){
                        $q->where('unit_id', $unit_id);
                })->distinct('id')->pluck('attribute_id');


            if(!count($productAttributes_attribute_ids) > 0){
                $finalArray = $this->getProductsAttributes();
            }else{

                $attributes_names = Attribute::whereIn('id', $productAttributes_attribute_ids)->where([
                    ['status', 1],
                    ['type', 2],
                ])->groupBy('name')->get(['id', 'name'])->toArray();

                foreach($attributes_names as $attributes_name){
                    $attributeOptions['attr']['attribute_name'] = $attributes_name['name'];
                    $attributeOptions['attr']['attribute_options'] =AttributeOption::distinct()->whereHas('attribute', function($q) use ($attributes_name){
                        $q->where('name', $attributes_name['name']);
                    })->groupBy('attribute_option_value')->pluck('attribute_option_value')->toArray();

                    $finalArray[]['attribute'] = $attributeOptions['attr'];
                }
            }


            }

        }

        if(!request()->exists('unit_id')){
        $prices['min_price'] = Product::query()->min('price');
        $prices['max_price'] = Product::query()->max('price');
        }else{
             $unit_id = request()->unit_id;

             $prices['min_price'] = Product::query()->where('unit_id',$unit_id)->min('price');
             $prices['max_price'] = Product::query()->where('unit_id',$unit_id)->max('price');
        }
        return response()->json([
            'attributes' => $finalArray,
            'price_limit' => $prices,
            'group_categories' => $Groupcategories,
        ]);
    }

    private function getProductsAttributes(){

             $attributes_names = Attribute::where([['status', 1], ['type', 2]])->groupBy('name')->get(['id', 'name'])->toArray();

            foreach($attributes_names as $attributes_name){
                $attributeOptions['attr']['attribute_name'] = $attributes_name['name'];
                $attributeOptions['attr']['attribute_options'] =AttributeOption::distinct()->whereHas('attribute', function($q) use ($attributes_name){
                    $q->where('name', $attributes_name['name']);
                })->groupBy('attribute_option_value')->pluck('attribute_option_value')->toArray();
                $finalArray[]['attribute'] = $attributeOptions['attr'];
            }

            return $finalArray;
    }

    public function productGlobalFilterResult(){
        if(request()->exists('categories')){
            $categories = request()->categories;
        }
        if(request()->exists('attributes')){
            $attributes = request()->input('attributes');
        }
        if(request()->exists('price_limit')){
            $price_limit = request()->price_limit;
        }

        if(isset($categories) && isset($attributes) && isset($price_limit)){

            $products = Product::query()->whereIn('category_id', $categories)->whereBetween('price', $price_limit)->whereHas('attributes', function($query) use ($attributes){

                $query->whereHas('attribute', function($q) use ($attributes){
                    $q->where('type', 2)->whereHas('attributeOption', function($qq) use ($attributes){
                        $qq->WhereIn('attribute_option_value', $attributes['value']);
                    });
                });

            })->where('product_status_id', 2)->paginate(12);


        } elseif(isset($categories) && isset($attributes)) {

            $products = Product::query()->whereIn('category_id', $categories)->whereHas('attributes', function($query) use ($attributes){
                $query->whereHas('attribute', function($q) use ($attributes){
                    $q->where('type', 2)->whereHas('attributeOption', function($qq) use ($attributes){
                        $qq->WhereIn('attribute_option_value', $attributes['value']);
                    });
                });
            })->where('product_status_id', 2)->paginate(12);

        } elseif(isset($categories) && isset($price_limit)) {

            $products = Product::query()->whereIn('category_id', $categories)->whereBetween('price', $price_limit)->where('product_status_id', 2)->paginate(12);

        } elseif(isset($attributes) && isset($price_limit)) {

            $products = Product::query()->whereBetween('price', $price_limit)->whereHas('attributes', function($query) use ($attributes){
                $query->whereHas('attribute', function($q) use ($attributes){
                    $q->where('type', 2)->whereHas('attributeOption', function($qq) use ($attributes){
                        $qq->WhereIn('attribute_option_value', $attributes['value']);
                    });
                });
            })->where('product_status_id', 2)->paginate(12);

        } elseif(isset($categories)) {

            $products = Product::query()->whereIn('category_id', \request()->categories)->where('product_status_id', 2)->paginate(12);


        } elseif(isset($attributes)) {

            $productAttributes_product_ids = ProductAttribute::Numericvalue()->get()->filter(function($item) use ($attributes){
                return in_array($item->name, $attributes['value']);
            })->pluck('product_id');
            $products = Product::whereIn('id', $productAttributes_product_ids)->where('product_status_id', 2)->paginate(12);

        } elseif(isset($price_limit)) {

            $products = Product::query()->whereBetween('price', $price_limit)->where('product_status_id', 2)->paginate(12);

        } else {
            $products = Product::query()->where('product_status_id', 2)->paginate(12);
        }

        return new ProductCollection($products);

    }

    public function productUnitFilterResult(){
        if(request()->exists('categories')){
            $categories = request()->categories;
        }
        if(request()->exists('attributes')){
            $attributes = request()->input('attributes');
           // dd($attributes);
         /* print_r($attributes);
           die();*/
        }
        if(request()->exists('price_limit')){
            $price_limit = request()->price_limit;
        }

        if(request()->exists('unit_id')){
            $unit_id = request()->unit_id;
        } else {
            return response()->json(['error' => 'لطفا شماره واحد را ارسال کنید.']);
        }

        if(isset($categories) && isset($attributes) && isset($price_limit)){

            $products = Product::query()->where([['unit_id', $unit_id],['product_status_id', 2]])->whereIn('category_id', $categories)->whereBetween('price', $price_limit)->paginate(12)->filter(function($q) use ($attributes){
                if($q->real_options != null){
                    if($q->real_options == $attributes['value']){
                        return true;
                    }else{
                        foreach($attributes['value'] as $attr){
                            $arr[] = in_array($attr,$q->real_options);
                        }

                        if(in_array(false,$arr) ){
                            return false;
                        }else{
                            return true;
                        }
                    }

                }else{
                    return false;
                }
            });

        } elseif(isset($categories) && isset($attributes)) {

            /*->whereHas('attributes', function($query) use ($attributes){
                $query->whereHas('attribute', function($q) use ($attributes){
                    $q->where('type', 2)->whereHas('attributeOption', function($qq) use ($attributes){
                        $qq->WhereIn('attribute_option_value', $attributes['value']);
                    });
                });
            })*/


            $products = Product::query()->where([['unit_id', $unit_id],['product_status_id', 2]])->whereIn('category_id', $categories)->paginate(12)->filter(function($q) use ($attributes){
                if($q->real_options != null){
                    if($q->real_options == $attributes['value']){
                        return true;
                    }else{
                        foreach($attributes['value'] as $attr){
                            $arr[] = in_array($attr,$q->real_options);
                        }

                        if(in_array(false,$arr) ){
                            return false;
                        }else{
                            return true;
                        }
                    }

                }else{
                    return false;
                }
            });

        } elseif(isset($categories) && isset($price_limit)) {

            $products = Product::query()->whereIn('category_id', $categories)->whereBetween('price', $price_limit)->where('product_status_id', 2)->paginate(12);

        } elseif(isset($attributes) && isset($price_limit)) {

          /*  $products = Product::query()->where('unit_id', $unit_id)->whereBetween('price', $price_limit)->whereHas('attributes', function($query) use ($attributes){
                $query->whereHas('attribute', function($q) use ($attributes){
                    $q->where('type', 2)->whereHas('attributeOption', function($qq) use ($attributes){
                        $qq->WhereIn('attribute_option_value', $attributes['value']);
                    });
                });
            })->where('product_status_id', 2)->paginate(12);*/

             $products = Product::query()->where([['unit_id', $unit_id],['product_status_id', 2]])->whereBetween('price', $price_limit)->paginate(12)->filter(function($q) use ($attributes){
                if($q->real_options != null){
                    if($q->real_options == $attributes['value']){
                        return true;
                    }else{
                        foreach($attributes['value'] as $attr){
                            $arr[] = in_array($attr,$q->real_options);
                        }

                        if(in_array(false,$arr) ){
                            return false;
                        }else{
                            return true;
                        }
                    }

                }else{
                    return false;
                }
            });



        } elseif(isset($categories)) {

            $products = Product::query()->where('unit_id', $unit_id)->whereIn('category_id', \request()->categories)->where('product_status_id', 2)->paginate(12);


        } elseif(isset($attributes)) {

            /*$productAttributes_product_ids = ProductAttribute::Numericvalue()->get()->filter(function($item) use ($attributes){
                return in_array($item->name, $attributes['value']);
            })->pluck('product_id');
            $products = Product::where([
                ['product_status_id', 2],
                ['unit_id', $unit_id],
            ])->whereIn('id', $productAttributes_product_ids)->paginate(12);*/


              $products = Product::query()->where([['unit_id', $unit_id],['product_status_id', 2]])->paginate(12)->filter(function($q) use ($attributes){
                if($q->real_options != null){
                    if($q->real_options == $attributes['value']){
                        return true;
                    }else{
                        foreach($attributes['value'] as $attr){
                            $arr[] = in_array($attr,$q->real_options);
                        }

                        if(in_array(false,$arr) ){
                            return false;
                        }else{
                            return true;
                        }
                    }

                }else{
                    return false;
                }
            });


            /*  $products = Product::query()->where('unit_id', $unit_id)->whereHas('attributes', function($query) use ($attributes){
                $query->whereHas('attribute', function($q) use ($attributes){
                    $q->where('type', 2)->whereHas('attributeOption', function($qq) use ($attributes){
                        $qq->WhereIn('attribute_option_value', $attributes['value']);
                    });
                });
            })->where('product_status_id', 2)->paginate(12)->filter(function($q) use ($attributes){
                if($q->real_options != null){

                    if($q->real_options == $attributes['value']){
                        return true;
                    }else{
                        foreach($attributes['value'] as $attr){
                            $arr[] = in_array($attr,$q->real_options);
                        }

                        if(in_array(false,$arr) ){
                            return false;
                        }else{
                            return true;
                        }
                    }

                }else{
                    return false;
                }
            });
*/
        } elseif(isset($price_limit)) {

            $products = Product::query()->where([
                ['product_status_id', 2],
                ['unit_id', $unit_id],
            ])->whereBetween('price', $price_limit)->paginate(12);

        } else {
            $products = Product::query()->where([['product_status_id', 2], ['unit_id', $unit_id]])->paginate(12);
        }

        return new ProductCollection($products);
    }
}
