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
 * 
 * 
 * @property float $total_without_tax This is the total without tax before discount. It does not apply rounding.
 * @property float $cost_amount This is the cost (quantity * unit_price). It does not apply rounding.
 * @property float $total_without_tax_after_discount This is total price without tax after discount. It does not apply rounding.
 * @property float $total_with_tax This is the final total considering discount and tax. In this step we apply the rounding.
 * @property float $tax_amount This is the tax percentage applied to the total. It does not apply rounding.
 * @property float $price_per_wp This is the price per Watt Peak for this detail.
 *                               It is calculated dividing the final total by the total peak power of the budget.
 * @property float $cost_per_wp This is the cost per Watt Peak for this detail.
 * @property float $gain_amount This is the gain amount for this detail. It is calculated as the difference between the total and the cost.
 * @property float $gain_margin This is the gain margin for this detail. It is calculated as the difference between the total and the cost divided by the total.
 * 
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

    private ?float $totalWithoutTax = null;
    private ?float $costAmount = null;
    private ?float $totalWithoutTaxAfterDiscount = null;
    private ?float $totalWithTax = null;
    private ?float $taxAmount = null;
    private ?float $pricePerWp = null;
    private ?float $costPerWp = null;
    private ?float $gainAmount = null;
    private ?float $gainMargin = null;

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

    /**
     * Better use the attribute total_without_tax
     * @return float
     */
    public function getTotalWithoutTaxAttribute()
    {
        if (is_null($this->totalWithoutTax)) {
            $this->totalWithoutTax = $this->quantity * $this->sell_price;
        }
        return $this->totalWithoutTax;
    }

    /**
     * Better use the attribute cost_amount
     * @return float
     */
    public function getCostAmountAttribute()
    {
        if (is_null($this->costAmount)) {
            $this->costAmount = $this->quantity * $this->unit_price;
        }
        return $this->costAmount;
    }

    /**
     * Better use the attribute total_without_tax_after_discount
     * @return float
     */
    public function getTotalWithoutTaxAfterDiscountAttribute()
    {
        if (is_null($this->totalWithoutTaxAfterDiscount)) {
            $this->totalWithoutTaxAfterDiscount = $this->total_without_tax - $this->discount;
        }
        return $this->totalWithoutTaxAfterDiscount;
    }

    /**
     * Better use the attribute total_with_tax
     * @return float
     */
    public function getTotalWithTaxAttribute()
    {
        if (is_null($this->totalWithTax)) {
            $this->totalWithTax = round($this->total_without_tax_after_discount / (1 - floatval($this->tax_percentage)), 2);
        }
        return $this->totalWithTax;
    }

    /**
     * Better use the attribute tax_amount
     * @return float
     */
    public function getTaxAmountAttribute()
    {
        if (is_null($this->taxAmount)) {
            $this->taxAmount = round(floatval($this->total_with_tax - $this->total_without_tax_after_discount), 2);
        }
        return $this->taxAmount;
    }

    /**
     * Better use the attribute price_per_wp
     *
     * @return float
     */
    public function getPricePerWpAttribute()
    {
        if (is_null($this->pricePerWp)) {
            $this->pricePerWp = $this->total_with_tax / $this->budget->total_peak_power;
        }
        return $this->pricePerWp;
    }

    /**
     * Better use the attribute cost_per_wp
     *
     * @return float
     */
    public function getCostPerWpAttribute()
    {
        if (is_null($this->costPerWp)) {
            $this->costPerWp = $this->cost_amount / $this->budget->total_peak_power;
        }
        return $this->costPerWp;
    }

    /**
     * Better use the attribute gain_amount
     */
    public function getGainAmountAttribute()
    {
        if (is_null($this->gainAmount)) {
            $this->gainAmount = $this->total_without_tax_after_discount - $this->cost_amount;
        }
        return $this->gainAmount;
    }

    /**
     * Better use the attribute gain_margin
     */
    public function getGainMarginAttribute()
    {
        if (is_null($this->gainMargin)) {
            $this->gainMargin = $this->gain_amount / $this->total_without_tax_after_discount;
        }
        return $this->gainMargin;
    }

    /**
     * This method is used to reset the totals. This is useful when the user changes the values of the budget details
     */
    public function resetTotals()
    {
        $this->totalWithoutTax = null;
        $this->costAmount = null;
        $this->totalWithoutTaxAfterDiscount = null;
        $this->totalWithTax = null;
        $this->taxAmount = null;
        $this->pricePerWp = null;
        $this->costPerWp = null;
        $this->gainAmount = null;
        $this->gainMargin = null;
    }


}