<?php

namespace App\Structures\Projects;

use App\Models\Procurement\ItemCategory;
use App\Models\Projects\Budget;
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
 * @property float $total_without_tax This is the total without tax for the budgetDetails of the category group
 * @property float $total_without_tax_after_discount This is the total without tax after discount for the budgetDetails of the category group
 * @property float $tax_amount This is the tax amount for the budgetDetails of the category group
 * @property float $total_with_tax This is the total with tax for the budgetDetails of the category group
 * @property float $tax_prorated_percentage This is the tax prorated percentage for the budgetDetails of the category group
 * @property float $price_per_wp This is the price per wp for the budgetDetails of the category group
 * 
 */
class BudgetCategoryDetailsManager {

    use GetterSetter;

    private Budget $budget;
    private ItemCategory $itemCategory;
    private Collection $budgetDetails;

    private ?float $totalWithoutTax = null;
    private ?float $totalWithoutTaxAfterDiscount = null;
    private ?float $taxAmount = null;
    private ?float $totalWithTax = null;
    private ?float $taxProratedPercentage = null;

    private ?float $pricePerWp = null;

    public function __construct(?Budget $budget, ?ItemCategory $itemCategory) {
        $this->budget = $budget;
        $this->itemCategory = $itemCategory;
        if ($budget && $itemCategory) {
            $this->loadBudgetDetails();
        }
    }

    /**
     * Filters the list of budgetDetails to get only the ones that belongs to the category group
     * @return Collection|BudgetDetail[]
     */
    public function loadBudgetDetails() {
        $this->budgetDetails = $this->budget->budgetDetails->filter(function ($budgetDetail) {
            return $budgetDetail->item->itemCategory->id == $this->itemCategory->id;
        });
        $this->performCalculations();
    }

    /**
     * Performs all the calculations for the budgetDetails of the category group
     */
    public function performCalculations() {
        $this->calculateTotalWithoutTax();
        $this->calculateTotalWithoutTaxAfterDiscount();
        $this->calculateTaxAmount();
        $this->calculateTotalWithTax();
        $this->calculateTaxProratedPercentage();
        $this->calculatePricePerWp();
    }

    /**
     * Resets all the calculations for the budgetDetails of the category group
     */
    public function resetCalculations() {
        $this->totalWithoutTax = null;
        $this->totalWithoutTaxAfterDiscount = null;
        $this->taxAmount = null;
        $this->totalWithTax = null;
        $this->taxProratedPercentage = null;
        $this->pricePerWp = null;
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
     * Calculates the tax prorated percentage for the budgetDetails of the category group
     */
    public function calculateTaxProratedPercentage() {
        $this->taxProratedPercentage = $this->budgetDetails->sum(function ($budgetDetail) {
            return $budgetDetail->tax_amount / $budgetDetail->total_with_tax;
        });
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

}
