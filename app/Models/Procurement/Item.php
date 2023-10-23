<?php

namespace App\Models\Procurement;

use App\Models\ModelCamelCase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $internalCod 
 * @property float $unitPrice
 * @property ItemCategory $category
 */
class Item extends ModelCamelCase
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        "name",
        "internal_cod",
        "unit_price",
        "item_categories_id"
    ];

    /**
     * Obtiene la categoría a la que pertenece este ítem.
     */
    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'item_categories_id');
    }
}