<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    /**
     * This is the getter and the setter for the unit_price attribute.
     * This will round the value to 2 decimals.
     *
     * @return Attribute
     */
    protected function unitPrice(): Attribute
    {
        return Attribute::make(
            get: fn($value) => round(floatval($value), 2),
            set: fn($value) => round(floatval($value), 2),
        );
    }
}
