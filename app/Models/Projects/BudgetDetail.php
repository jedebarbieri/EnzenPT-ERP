<?php
namespace App\Models\Projects;

use App\Models\Procurement\Item;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * This class is used to define the budget of any project
 * 
 * @property int $id
 * @property Item $item This is the relation to the item that this line represent
 * @property Budget $budget This is the reference to the budget that this line belogns
 * @property float $unit_price This is the price of this item. This will store the current item's price
 *                             The user can modify this value. This will affect the final gain.
 *                             **This value doesn't include IVA.**
 * @property float $quantity This is the quantity of this item. It could be decimal.
 * @property float $tax_percentage This is the percentage of the tax to be applied to this item. By default should be 23% (0.23) Its value is from 0 to 1.
 * @property float $discount This is the amount of money that will be deducted from the total for this line item.
 *                           This will apply on the $sellPrice to calculate the total.
 * @property float $sell_price This amount indicates the final selling price for this item.
 *                             The difference between this value and the $unit_price of this entity will define the gain value.
 *                             **This value doesn't include IVA.**
 */
class BudgetDetail extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $fillable = [
        "id",
        "item_id",
        "budget_id",
        "unit_price",
        "quantity",
        "tax_percentage",
        "discount",
        "sell_price"
    ];

    /**
     * Returns the relationship to the item that represents
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Returns the relationship to the budget hows belongs to
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class);
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

    /**
     * This is the getter and the setter for the discount attribute.
     * This will round the value to 2 decimals.
     * 
     * @return Attribute
     */
    protected function discount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => round(floatval($value), 2),
            set: fn($value) => round(floatval($value), 2),
        );
    }

    /**
     * This is the getter and the setter for the sell_price attribute.
     * This will round the value to 2 decimals.
     * 
     * @return Attribute
     */
    protected function sellPrice(): Attribute
    {
        return Attribute::make(
            get: fn($value) => round(floatval($value), 2),
            set: fn($value) => round(floatval($value), 2),
        );
    }

    /**
     * This is the getter and the setter for the quantity attribute.
     * This will round the value to 2 decimals and ensure that the value is not negative
     * 
     * @return Attribute
     */
    protected function quantity(): Attribute
    {
        return Attribute::make(
            get: fn($value) => round(floatval($value), 2),
            set: function($value) {
                $val = round(floatval($value), 2);
                if ($val < 0) {
                    $val = 0;
                }
                return $val;
            }
        );
    }

    /**
     * This is the getter and the setter for the tax_percentage attribute.
     * This will round the value to 2 decimals and ensure that the value is between 0 and 1
     * 
     * @return Attribute
     */
    protected function taxPercentage(): Attribute
    {
        return Attribute::make(
            get: fn($value) => round(floatval($value), 4),
            set: function($value) {
                // We ensure that the value is between 0 and 1
                $val = round(floatval($value), 4);
                if ($val < 0) {
                    $val = 0;
                } elseif ($val > 1) {
                    $val = 1;
                }
                return $val;
            },
        );
    }

}