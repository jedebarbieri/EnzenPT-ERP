<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $internalCod 
 * @property float $unitPrice
 * @property ItemCategory $itemCategory
 */
class Item extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        "name",
        "internal_cod",
        "unit_price",
        "item_category_id"
    ];

    /**
     * Obtiene la categoría a la que pertenece este ítem.
     */
    public function itemCategory()
    {
        return $this->belongsTo(ItemCategory::class);
    }
}
