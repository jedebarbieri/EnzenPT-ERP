<?php

namespace App\Services\Projects;

use App\Models\Procurement\ItemCategory;
use App\Models\Projects\Budget;
use App\Structures\Projects\BudgetCategoryDetailsManager;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * This class is used to generate the Excel report for a budget
 * @property Budget $budget This is the budget to be used to generate the report
 */
class BudgetExcelReportGenerator
{

    public Budget $budget;

    public Collection $budgetDetailsManagers;

    public Spreadsheet $spreadsheet;

    public Worksheet $worksheet;
    
    public $row;

    public function __construct(Budget $budget)
    {
        $this->budget = $budget;
        $this->row = 1;
        $this->budgetDetailsManagers = new Collection();
    }

    public function generateReport()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->worksheet = $this->spreadsheet->getActiveSheet();

        // Imprimimos la cabecera de la tabla
        $this->worksheet->setCellValue('A' . $this->row, 'Category');
        $this->worksheet->setCellValue('B' . $this->row, 'Taxes Amount');
        $this->worksheet->setCellValue('C' . $this->row, 'Total wo tax');
        $this->worksheet->setCellValue('D' . $this->row, 'Total');
        $this->worksheet->setCellValue('D' . $this->row, '€/Wp');

        $this->row++;

        $this->budget->item_categories->each(function (ItemCategory $itemCategory) {
            $newBudgetCategoryDetailsManager = new BudgetCategoryDetailsManager($this->budget, $itemCategory);
            $this->budgetDetailsManagers->push($newBudgetCategoryDetailsManager);
        });

        $this->budgetDetailsManagers->each(function (BudgetCategoryDetailsManager $budgetCategoryDetailsManager) {
            $this->printCategoryDetails($budgetCategoryDetailsManager);
        });


        $writer = new Xlsx($this->spreadsheet);

        $filePath = '/path/to/your/report.xlsx';
        $writer->save($filePath);

        return $filePath;
    }


    public function printCategoryDetails(BudgetCategoryDetailsManager $budgetCategoryDetailsManager)
    {
        $this->worksheet->setCellValue('A' . $this->row, $budgetCategoryDetailsManager->itemCategory->name);
        $this->worksheet->setCellValue('B' . $this->row, $budgetCategoryDetailsManager->tax_amount);
        $this->worksheet->setCellValue('C' . $this->row, $budgetCategoryDetailsManager->total_without_tax);
        $this->worksheet->setCellValue('D' . $this->row, $budgetCategoryDetailsManager->total_with_tax);
        $this->worksheet->setCellValue('D' . $this->row, $budgetCategoryDetailsManager->price_per_wp);
    }
}