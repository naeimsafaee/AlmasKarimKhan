<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
        'name',
        'slug',
        'seo_desc',
        'count',
        'price',
        'wage',
        'dollar_price',
        'gold_18',
        'gold_24',
        'discount',
        'desc',
        'product_status_id',
        "unit_id",
        "category_id",
    ];

    protected $hidden = ["created_at", "updated_at", "product_status_id", "unit_id"];

    protected $appends = ["short_desc", "status", "discounted", "image_url", "main_price", "category_name", "unit", "real_options", "real_discount"];

    public function getShortDescAttribute()
    {
        return mb_substr($this->desc, 0, 200);
    }

    public function getUnitAttribute()
    {
        return Unit::find($this->unit_id);
    }

    public function getCategoryNameAttribute()
    {
        if ($this->category_id !== 0) {

            $category = Category::query()->find($this->category_id);
            if ($category == null)
                return "ندارد";
            return $category->name;
        } else
            return 'ندارد';
    }

    public function getStatusAttribute()
    {
        if (ProductStatus::find($this->product_status_id) == null)
            return "";
        return ProductStatus::find($this->product_status_id)->name;
    }

    public function getDiscountedAttribute()
    {
        if ($this->online_price !== 0)
            return number_format($this->online_price - ($this->online_price * $this->getRealDiscountAttribute() / 100));

        $has = ProductAttribute::where('product_id', $this->id)->count();
        // if ($has === 0) {
            // return number_format($this->price - ($this->price * $this->getRealDiscountAttribute() / 100));
        // } else {
            $pr = $this->getProductPriceAttribute();
            return number_format($pr - ($pr * $this->getRealDiscountAttribute() / 100));
        // }
    }

    public function getRealDiscountAttribute()
    {
        if ($this->unit()->exists()) {
            if ($this->unit->discount()->exists()) {
                if ($this->unit->discount->discount != 0) {
                    $discount = $this->unit->discount->discount;
                }else{
                    $discount = $this->discount;
                }
            } else {
                $discount = $this->discount;
            }
        } else {
            $discount = $this->discount;
        }
        return $discount;
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function getProductPriceAttribute()
    {
        if ($this->online_price !== 0) {

            $has = ProductAttribute::where('product_id', $this->id)->count();
            if ($has === 0) {

                return number_format($this->price);
            } else {

                $attrs = ProductAttribute::where('product_id', $this->id)->get();

                $tot = 0;
                foreach ($attrs as $attr) {
                    $atrribute = Attribute::find($attr->attribute_id);
                    $value = $attr->value;
                    $value = preg_replace("/[^0-9]/", "", $value);
                    if (is_numeric($value)) {
                        if ($atrribute->type === 1) {
                            $price = @AttributePrice::where('attribute_id', $attr->attribute_id)->first()->price;
                            $tot += $price * $value;
                        }
                    }
                }
                return $tot;
            }
        } else {
            if ($this->dollar_price !== null && $this->dollar_price !== 0) {
                $dollar = Control::where('title', 'dollar')->first();
                if (!$dollar->updated_at->isToday()) {

                    $uri = 'http://nerkh-api.ir/api/da136eb6620b2afa7b56e20686606b44/currency/';

                    $client = new Client(['base_uri' => 'http://nerkh-api.ir']);

                    $cookieJar = CookieJar::fromArray([

                    ], 'http://nerkh-api.ir');
                    try {
                        $response = $client->request('GET', $uri);

                        $body = $response->getBody();
                        $result = json_decode($body);

                    } catch (BadResponseException $e) {

                        $result = json_decode($e->getResponse()->getBody()->getContents(), true);
                    }

                    $upd = Control::where('title', 'dollar')->first()->id;
                    $upd = Control::find($upd);
                    $upd->opt_1 = ($result->data->prices->USD->current) / 10;
                    $upd->save();
                }
                return $this->dollar_price * Control::where('title', 'dollar')->first()->opt_1;
            } else if ($this->gold_18 !== null && $this->gold_18 !== 0) {
                $gold_18 = Control::where('title', 'gold_18')->first();
                if (!$gold_18->updated_at->isToday()) {

                    $uri = 'http://nerkh-api.ir/api/da136eb6620b2afa7b56e20686606b44/gold/';

                    $client = new Client(['base_uri' => 'http://nerkh-api.ir']);

                    $cookieJar = CookieJar::fromArray([

                    ], 'http://nerkh-api.ir');
                    try {
                        $response = $client->request('GET', $uri);

                        $body = $response->getBody();
                        $result = json_decode($body);

                    } catch (BadResponseException $e) {

                        $result = json_decode($e->getResponse()->getBody()->getContents(), true);
                    }

                    $upd = Control::where('title', 'gold_18')->first()->id;
                    $upd = Control::find($upd);
                    $upd->opt_1 = ($result->data->prices->geram18->current) / 10;
                    $upd->save();

                    $upd = Control::where('title', 'gold_24')->first()->id;
                    $upd = Control::find($upd);
                    $upd->opt_1 = ($result->data->prices->geram24->current) / 10;
                    $upd->save();
                }

                return $this->gold_18 * Control::where('title', 'gold_18')->first()->opt_1;

            } else if ($this->gold_24 !== null && $this->gold_24 !== 0) {

                $gold_24 = Control::where('title', 'gold_24')->first();
                if (!$gold_24->updated_at->isToday()) {

                    $uri = 'http://nerkh-api.ir/api/da136eb6620b2afa7b56e20686606b44/gold/';

                    $client = new Client(['base_uri' => 'http://nerkh-api.ir']);

                    $cookieJar = CookieJar::fromArray([

                    ], 'http://nerkh-api.ir');
                    try {
                        $response = $client->request('GET', $uri);

                        $body = $response->getBody();
                        $result = json_decode($body);

                    } catch (BadResponseException $e) {

                        $result = json_decode($e->getResponse()->getBody()->getContents(), true);
                    }

                    $upd = Control::where('title', 'gold_18')->first()->id;
                    $upd = Control::find($upd);
                    $upd->opt_1 = ($result->data->prices->geram18->current) / 10;
                    $upd->save();

                    $upd = Control::where('title', 'gold_24')->first()->id;
                    $upd = Control::find($upd);
                    $upd->opt_1 = ($result->data->prices->geram24->current) / 10;
                    $upd->save();
                }

                return $this->gold_24 * Control::where('title', 'gold_24')->first()->opt_1;
            }

            return $this->price;

        }
    }




    public function getMainPriceAttribute()
    {
        return number_format($this->getProductPriceAttribute());
    }

    public function image()
    {
        return $this->hasmany(ProductToImage::class, 'product_id', 'id');
    }

    public function getImageUrlAttribute()
    {
        $image = $this->image;
        unset($this->image);
        return $image;
    }

    public function order()
    {
        return $this->hasMany(UserOrders::class, 'product_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getRealOptionsAttribute()
    {

        if ($this->attributes()->exists()) {

            $attributeOptions = $this->attributes()->whereHas('attribute', function ($q) {
                $q->where('type', 2);
            })->get();

            foreach ($attributeOptions as $attr) {
                $names[] = $attr->name;
            }

            return $names;
        } else {
            return null;
        }
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id', 'id');
    }

}
