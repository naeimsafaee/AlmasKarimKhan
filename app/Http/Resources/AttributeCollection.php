<?php

namespace App\Http\Resources;

use App\Category;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\GroupAttribute;

class AttributeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Collection
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'type' => $item->type,
                'status' => $item->status,
                'group_attribute' => GroupAttribute::find($item->group_attribute_id)->name,
                'category' => Category::find(GroupAttribute::find($item->group_attribute_id)->category_id)->name,
                'options' => new AttributeOptionsCollection($item->attributeOption()->get())
            ];
        });

    }
}
