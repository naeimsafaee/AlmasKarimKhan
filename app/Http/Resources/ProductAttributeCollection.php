<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\AttributeOption;

class ProductAttributeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Collection
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            if ($item->attribute()->first()->type == 1) {
                return [

                    'attribute_id' => $item->attribute_id,
                    'product_id' => $item->product_id,
                    'attribute' => $item->attribute()->first()->name,
                    'value' => $item->value,

                ];
            }else{
                return [

                    'attribute_id' => $item->attribute_id,
                    'product_id' => $item->product_id,
                    'attribute' => $item->attribute()->first()->name,
                    'value' => AttributeOption::find($item->value)->attribute_option_value,

                ];
            }
        });
    }
}
