<?php

namespace App\Structures\Projects;

use App\Models\Procurement\ItemCategory;
use App\Models\Projects\Budget;
use App\Models\Projects\BudgetDetail;
use App\Structures\GetterSetter;
use Illuminate\Database\Eloquent\Collection;

/**
 * This class is in charged to group some budgetDetails from a specific budget and perform 
 * some specific calculations withing the category group.
 * 
 * @property Budget $budget This is the budget to be used to generate the report
 * @property ItemCategory $item_category This is the category group
 * @property Collection|BudgetDetail[] $budget_details This is the list of the budgetDetails related to this budget.
 *                                                    Each one has a relationship with the item
 * @property Collection|BudgetCategoryDetailsManager[] $sub_budget_category_details_managers This is the list of the sub category managers
 * 
 * 
 * @property float $total_without_tax This is the total without tax for the budgetDetails of the category group
 * @property float $cost_without_tax This is the cost without tax for the budgetDetails of the category group
 * @property float $total_without_tax_after_discount This is the total without tax after discount for the budgetDetails of the category group
 * @property float $tax_amount This is the tax amount for the budgetDetails of the category group
 * @property float $cost_tax_amount This is the cost tax amount for the budgetDetails of the category group
 * @property float $total_with_tax This is the total with tax for the budgetDetails of the category group
 * @property float $cost_with_tax This is the cost with tax for the budgetDetails of the category group
 * @property float $tax_prorated_percentage This is the tax prorated percentage for the budgetDetails of the category group
 * @property float $price_per_wp This is the price per wp for the budgetDetails of the category group
 * @property float $cost_per_wp This is the cost price per wp for the budgetDetails of the category group
 * @property float $gain_margin This is the gain margin in percentage for the budgetDetails of the category group
 * @property float $gain_amount This is the gain amount for the budgetDetails of the category group
 * 
 */
class BudgetCategoryDetailsManager {

    use GetterSetter;

    private Budget $budget;
    private ItemCategory $itemCategory;
    private Collection $budgetDetails;

    private ?Collection $subBudgetCategoryDetailsManagers = null;

    private ?float $totalWithoutTax = null;
    private ?float $costWithoutTax = null;
    private ?float $totalWithoutTaxAfterDiscount = null;
    private ?float $taxAmount = null;
    private ?float $costTaxAmount = null;
    private ?float $totalWithTax = null;
    private ?float $costWithTax = null;
    private ?float $taxProratedPercentage = null;
    private ?float $pricePerWp = null;
    private ?float $costPerWp = null;
    private ?float $gainMargin = null;
    private ?float $gainAmount = null;

    public function __construct(?Budget $budget, ?ItemCategory $itemCategory) {
        $this->budget = $budget;
        $this->itemCategory = $itemCategory;
        $this->subBudgetCategoryDetailsManagers = new Collection();

        if ($budget && $itemCategory) {
            $this->loadBudgetDetails();
            $this->loadSubBudgetCategoryDetailsManagers();
        }
    }

    /**
     * Loads the sub budget category details managers
     */
    public function loadSubBudgetCategoryDetailsManagers() {
        $this->itemCategory->children->each(function (ItemCategory $subItemCategory) {
            $subBudgetCategoryDetailsManager = new BudgetCategoryDetailsManager($this->budget, $subItemCategory);
            // In case these category doesn't have any item in this budget
            if ($subBudgetCategoryDetailsManager->budget_details->count() == 0) {
                return;
            }
            $this->subBudgetCategoryDetailsManagers->push($subBudgetCategoryDetailsManager);
        });
    }

    /**
     * Filters the list of budgetDetails to get only the ones that belongs to the category group.
     * If this category group has children, then the budgetDetails of the children will be included too.
     * 
     * @return Collection|BudgetDetail[]
     */
    public function loadBudgetDetails() {

        $this->budgetDetails = $this->budget->budgetDetails->filter(function ($budgetDetail) {
            $idItemCat = $budgetDetail->item->itemCategory->id;

            // If the category has children, then we need to check if the budget detail belongs to some of its children
            if ($this->itemCategory->children()->count() > 0) {
                return $this
                    ->itemCategory
                    ->children
                    ->filter(
                        function ($itemCategory) use ($idItemCat) {
                            return $itemCategory->id == $idItemCat;
                        }
                    )->count() > 0;
            }
            // Else, we just need to check if the budget detail belongs to the category
            return $idItemCat == $this->itemCategory->id;
        });

        if ($this->budgetDetails->count() > 0) {
            $this->performCalculations();
        }
    }

    /**
     * Performs all the calculations for the budgetDetails of the category group
     */
    public function performCalculations() {
        $this->resetCalculations();
        $this->calculateTotalWithoutTax();
        $this->calculateCostWithoutTax();
        $this->calculateTotalWithoutTaxAfterDiscount();
        $this->calculateTaxAmount();
        $this->calculateCostTaxAmount();
        $this->calculateTotalWithTax();
        $this->calculateCostWithTax();
        $this->calculateTaxProratedPercentage();
        $this->calculatePricePerWp();
        $this->calculateCostPerWp();
        $this->calculateGainAmount();
        $this->calculateGainMargin();
    }

    /**
     * Resets all the calculations for the budgetDetails of the category group
     */
    public function resetCalculations() {
        $this->totalWithoutTax = null;
        $this->costWithoutTax = null;
        $this->totalWithoutTaxAfterDiscount = null;
        $this->taxAmount = null;
        $this->costTaxAmount = null;
        $this->totalWithTax = null;
        $this->costWithTax = null;
        $this->taxProratedPercentage = null;
        $this->pricePerWp = null;
        $this->costPerWp = null;
        $this->gainMargin = null;
        $this->gainAmount = null;
    }

    /**
     * Calculates the total without tax for the budgetDetails of the category group
     */
    public function calculateTotalWithoutTax() {
        $this->totalWithoutTax = $this->budgetDetails->sum(function ($budgetDetail) {
            return $budgetDetail->total_without_tax;
        });
    }

    /**
     * Better use the attribute total_without_tax
     */
    public function getTotalWithoutTaxAttribute() {
        if (is_null($this->totalWithoutTax)) {
            $this->calculateTotalWithoutTax();
        }
        return $this->totalWithoutTax;
    }

    /**
     * Calculates the cost without tax for the budgetDetails of the category group
     */
    public function calculateCostWithoutTax() {
        $this->costWithoutTax = $this->budgetDetails->sum(function ($budgetDetail) {
            return $budgetDetail->cost_without_tax;
        });
    }

    /**
     * Better use the attribute cost_without_tax
     */
    public function getCostWithoutTaxAttribute() {
        if (is_null($this->costWithoutTax)) {
            $this->calculateCostWithoutTax();
        }
        return $this->costWithoutTax;
    }

    /**
     * Calculates the total without tax after discount for the budgetDetails of the category group
     */
    public function calculateTotalWithoutTaxAfterDiscount() {
        $this->totalWithoutTaxAfterDiscount = $this->budgetDetails->sum(function ($budgetDetail) {
            return $budgetDetail->total_without_tax_after_discount;
        });
    }

    /**
     * Better use the attribute total_without_tax_after_discount
     */
    public function getTotalWithoutTaxAfterDiscountAttribute() {
        if (is_null($this->totalWithoutTaxAfterDiscount)) {
            $this->calculateTotalWithoutTaxAfterDiscount();
        }
        return $this->totalWithoutTaxAfterDiscount;
    }

    /**
     * Calculates the tax amount for the budgetDetails of the category group
     */
    public function calculateTaxAmount() {
        $this->taxAmount = $this->budgetDetails->sum(function ($budgetDetail) {
            return $budgetDetail->tax_amount;
        });
    }

    /**
     * Better use the attribute tax_amount
     */
    public function getTotalTaxAmountAttribute() {
        if (is_null($this->taxAmount)) {
            $this->calculateTaxAmount();
        }
        return $this->taxAmount;
    }

    /**
     * Calculates the cost tax amount for the budgetDetails of the category group
     */
    public function calculateCostTaxAmount() {
        $this->costTaxAmount = $this->budgetDetails->sum(function (BudgetDetail $budgetDetail) {
            return $budgetDetail->cost_tax_amount;
        });
    }

    /**
     * Better use the attribute cost_tax_amount
     */
    public function getCostTaxAmountAttribute() {
        if (is_null($this->costTaxAmount)) {
            $this->calculateCostTaxAmount();
        }
        return $this->costTaxAmount;
    }

    /**
     * Calculates the total with tax for the budgetDetails of the category group
     */
    public function calculateTotalWithTax() {
        $this->totalWithTax = $this->budgetDetails->sum(function ($budgetDetail) {
            return $budgetDetail->total_with_tax;
        });
    }

    /**
     * Better use the attribute total_with_tax
     */
    public function getTotalWithTaxAttribute() {
        if (is_null($this->totalWithTax)) {
            $this->calculateTotalWithTax();
        }
        return $this->totalWithTax;
    }

    /**
     * Calculates the cost with tax for the budgetDetails of the category group
     */
    public function calculateCostWithTax() {
        $this->costWithTax = $this->budgetDetails->sum(function (BudgetDetail $budgetDetail) {
            return $budgetDetail->cost_with_tax;
        });
    }

    /**
     * Better use the attribute cost_with_tax
     */
    public function getCostWithTaxAttribute() {
        if (is_null($this->costWithTax)) {
            $this->calculateCostWithTax();
        }
        return $this->costWithTax;
    }

    /**
     * Calculates the tax prorated percentage for the budgetDetails of the category group
     */
    public function calculateTaxProratedPercentage() {
        $this->taxProratedPercentage = round($this->taxAmount / $this->totalWithTax, 4);
    }

    /**
     * Better use the attribute tax_prorated_percentage
     */
    public function getTaxProratedPercentageAttribute() {
        if (is_null($this->taxProratedPercentage)) {
            $this->calculateTaxProratedPercentage();
        }
        return $this->taxProratedPercentage;
    }

    /**
     * Calculates the price per wp for the budgetDetails of the category group
     */
    public function calculatePricePerWp() {
        $this->pricePerWp = $this->budgetDetails->sum(function ($budgetDetail) {
            return $budgetDetail->price_per_wp;
        });
    }

    /**
     * Better use the attribute price_per_wp
     */
    public function getPricePerWpAttribute() {
        if (is_null($this->pricePerWp)) {
            $this->calculatePricePerWp();
        }
        return $this->pricePerWp;
    }

    /**
     * Calculates the cost per wp for the budgetDetails of the category group
     */
    public function calculateCostPerWp() {
        $this->costPerWp = $this->budgetDetails->sum(function (BudgetDetail $budgetDetail) {
            return $budgetDetail->cost_per_wp;
        });
    }

    /**
     * Better use the attribute cost_price_per_wp
     */
    public function getCostPerWpAttribute() {
        if (is_null($this->costPerWp)) {
            $this->calculateCostPerWp();
        }
        return $this->costPerWp;
    }

    /**
     * Calculates the gain margin in percentage for the budgetDetails of the category group
     */
    public function calculateGainAmount() {
        $this->gainAmount = $this->totalWithoutTaxAfterDiscount - $this->costWithoutTax;
    }

    /**
     * Better use the attribute gain_amount
     */
    public function getGainAmountAttribute() {
        if (is_null($this->gainAmount)) {
            $this->calculateGainAmount();
        }
        return $this->gainAmount;
    }

    /**
     * Calculates the gain margin in percentage for the budgetDetails of the category group
     */
    public function calculateGainMargin() {
        $this->gainMargin = round($this->gainAmount / $this->totalWithoutTaxAfterDiscount, 4);
    }

    /**
     * Better use the attribute gain_margin
     */
    public function getGainMarginAttribute() {
        if (is_null($this->gainMargin)) {
            $this->calculateGainMargin();
        }
        return $this->gainMargin;
    }

}
