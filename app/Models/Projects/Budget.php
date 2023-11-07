<?php

namespace App\Models\Projects;

use App\Models\Procurement\ItemCategory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * This class is used to define the budget of any project
 * 
 * @property int $id Is the system identificator
 * @property Collection|BudgetDetail[] $budgetDetails This is the list of the budgetDetails related to this budget. Each one has a relationship with the item
 * @property int $status This is the status of the Budget (to be defined)
 * @property string $name An optional name for this budget
 * @property float $gain_margin The default percentage of gain for whole budget. This value can be overwrite within the line detail level. It is from 0 to 1
 * @property string $project_name The name of the project which this budget belongs. * This will be moved to the project entity.
 * @property string $project_number The number of the project which this budget belong. * This will be moved to the project entity.
 * @property string $project_location The location of the project which this budget belongs. * This will be moved to the project entity.
 * @property float $total_peak_power This is the total of the maximum power that this project can provide.
 *                                 This data is used to calculate the cost of each item or category per Watt Peak ( 0.00 € / Wp)
 * @property Collection|ItemCategory[] $item_categories This is the list of the item categories that belongs to this budget
 * @property Collection|ItemCategory[] $main_item_categories This is the list of the main item categories that belongs to this budget
 */
class Budget extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_DRAFT = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    const STATUS = [
        self::STATUS_DRAFT => "Draft",
        self::STATUS_APPROVED => "Approved",
        self::STATUS_REJECTED => "Rejected"
    ];

    protected $fillable = [
        "status",
        "name",
        "gain_margin",
        "project_name",
        "project_number",
        "project_location",
        "total_peak_power"
    ];

    /**
     * Stores the result of the tax amount calculation of all the budget details
     */
    private ?float $taxAmount = null;

    /**
     * Stores the result of discount sum calculation of all the budget details
     */
    private ?float $totalDiscount = null;

    /**
     * Stores the result of the totals without tax calculation of all the budget details
     */
    private ?float $totalWithoutTax = null;

    /**
     * Stores the result of the final total calculation of all the budget details
     */
    private ?float $totalWithTax = null;

    /**
     * Stores the list of the item categories that are present in this budget
     */
    private ?Collection $itemCategories = null;

    /**
     * Stores the list of the main item categories that are present in this budget
     */
    private ?Collection $mainItemCategories = null;

    /**
     * Relationship with the lines of this detail represented by BudgetDetails
     * @return HasMany
     */
    public function budgetDetails() 
    {
        return $this->hasMany(BudgetDetail::class);
    }

    /**
     * This is the getter and the setter for the tax_percentage attribute.
     * This will round the value to 2 decimals and ensure that the value is between 0 and 1
     * 
     * @return Attribute
     */
    protected function gainMargin(): Attribute
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
     * This is the getter and the setter for the total_peak_power attribute.
     * This will round the value to 2 decimals and ensure that the value is not negative
     * 
     * @return Attribute
     */
    protected function totalPeakPower(): Attribute
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
     * Better use the attribute total_without_tax
     *
     * @return float
     */
    public function getTotalWithoutTaxAttribute()
    {
        if (is_null($this->totalWithoutTax)) {
            $this->totalWithoutTax = $this->budgetDetails->sum("total_without_tax_after_discount");
        }
        return $this->totalWithoutTax;
    }

    /**
     * Better use the attribute total_discount
     *
     * @return float
     */
    public function getTotalDiscountAttribute()
    {
        if (is_null($this->totalDiscount)) {
            $this->totalDiscount = $this->budgetDetails->sum("discount");
        }
        return $this->totalDiscount;
    }

    /**
     * Better use the attribute total_with_tax
     * @return float
     */
    public function getTotalWithTaxAttribute()
    {
        if (is_null($this->totalWithTax)) {
            $this->totalWithTax = $this->budgetDetails->sum("total_with_tax");
        }
        return $this->totalWithTax;
    }

    /**
     * Better use the attribute total_tax_amount
     * @return float
     */
    public function getTotalTaxAmountAttribute()
    {
        if (is_null($this->taxAmount)) {
            $this->taxAmount = $this->budgetDetails->sum("tax_amount");
        }
        return $this->taxAmount;
    }

    /**
     * **Better use the attribute item_categories**
     * This method returns the total of the budget details that belongs to a specific ItemCategory
     * It performs a join between budget_details and items to get the item_category_id
     * @return Collection|ItemCategory[]
     */
    public function getItemCategoriesAttribute() {
        if (is_null($this->itemCategories)) {
            $this->itemCategories = ItemCategory::join("items", "items.item_category_id", "=", "item_categories.id")
                ->join("budget_details", "budget_details.item_id", "=", "items.id")
                ->where("budget_details.budget_id", "=", $this->id)
                ->select("item_categories.*")
                ->distinct()
                ->orderBy("item_categories.prefix")
                ->get();
        }
        return $this->itemCategories;
    }

    /**
     * **Better use the attribute item_categories**
     * This method returns the total of the budget details that belongs to a specific ItemCategory
     * It performs a join between budget_details and items to get the item_category_id
     * @return Collection|ItemCategory[]
     */
    public function getMainItemCategoriesAttribute() {
        if (is_null($this->mainItemCategories)) {
            $this->mainItemCategories = ItemCategory::join("item_categories as ic2", "ic2.id", "=", "item_categories.parent_id")
                ->join("items", "items.item_category_id", "=", "item_categories.id")
                ->join("budget_details", "budget_details.item_id", "=", "items.id")
                ->where("budget_details.budget_id", "=", $this->id)
                ->select("ic2.*")
                ->distinct()
                ->with("children")
                ->orderBy("ic2.prefix")
                ->get();
        }
        return $this->mainItemCategories;
    }
}
