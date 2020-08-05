<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection{
    //public static $wrap = false;

    public function toArray($request){
        return $this->collection->map(function($item){

            return [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'price' => number_format($item->product_price),
                'discount_price' => $item->discounted,
                'real_discount' => $item->real_discount,
                'discount' => $item->real_discount,
                'category' => $item->category()->exists() ? [
                    'id' => $item->category->id,
                    'name' => $item->category->name,
                    'group_category' => $item->category->group->name,
                ] : null,

                'unit' => $item->unit()->exists() ? [
                    'id' => $item->unit_id,
                    'name' => $item->unit->name,
                ] : null,
                'images' => $item->image()->get(),
                'created_at' => $item->created_at,
            ];
        });
    }
}
