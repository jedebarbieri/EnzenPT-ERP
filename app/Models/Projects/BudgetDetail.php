<?php
namespace App\Models\Projects;

use App\Models\ModelCamelCase;
use App\Models\Procurement\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * This class is used to define the budget of any project
 * 
 * @property int $id
 * @property Item $item This is the relation to the item that this line represent
 * @property Budget $budget This is the reference to the budget that this line belogns
 * @property float $unitPrice This is the price of this item.
 * @property float $quantity This is the quantity of this item. It could be fractional.
 * @property float $taxPercentage This is the percentage of the tax to be applied to this item.
 * @property float $sellPrice This amount indicates the final selling price for this item.
 * @property float $discount This is the amount of money that will be deducted from the total for this line item.
 *                           This will apply on the $sellPrice to calculate the total.
 */
class BudgetDetail extends ModelCamelCase
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

}