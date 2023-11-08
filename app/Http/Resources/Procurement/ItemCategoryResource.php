<?php

namespace App\Http\Resources\Procurement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemCategoryResource extends JsonResource
{

    /**
     * Static property to indicate if the resource should include the parents (all of them)
     * or the children, if it is false
     */
    public static $with_parents = false;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $result = [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->getPrefixCodeAttribute(),
        ];
        if (self::$with_parents) {
            if ($this->parent_id) {
                $parent = new ItemCategoryResource($this->parent);
                $parent->load('parent');
                $result['parent'] = $parent;
            }
        } else {
            $result['children'] = ItemCategoryResource::collection($this->children);
        }
        return $result;
    }
}
