<?php

namespace App\Services\Projects;

use App\Models\Procurement\ItemCategory;
use App\Models\Projects\Budget;
use App\Models\Projects\BudgetDetail;
use App\Structures\Projects\BudgetCategoryDetailsManager;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * This class is used to generate the Excel report for a budget
 * @property Budget $budget This is the budget to be used to generate the report
 * @property Collection|BudgetCategoryDetailsManager[] $mainBudgetDetailsManagers This is the list of the main budget details managers
 * @property Spreadsheet $spreadsheet This is the spreadsheet object
 * @property Worksheet $worksheet This is the worksheet object
 * @property int $row This is the current row of the worksheet
 * @property string $column This is the current column of the worksheet
 */
class BudgetExcelReportGenerator
{

    public Budget $budget;

    public Collection $mainBudgetDetailsManagers;

    public Spreadsheet $spreadsheet;

    public Worksheet $worksheet;

    public $row = 1;

    public $column = 'A';

    public function __construct(Budget $budget)
    {
        $this->budget = $budget;
        $this->row = 1;
        $this->mainBudgetDetailsManagers = new Collection();
    }

    public function generateReport()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->worksheet = $this->spreadsheet->getActiveSheet();
        
        // Building the main structure of the budget

        $this->budget->main_item_categories->each(function (ItemCategory $itemCategory) {
            $newBudgetCategoryDetailsManager = new BudgetCategoryDetailsManager($this->budget, $itemCategory);
            $this->mainBudgetDetailsManagers->push($newBudgetCategoryDetailsManager);
        });

        // Imprimimos la cabecera de la tabla de precios finales
        $this->worksheet->setCellValue($this->column++ . $this->row, 'Cod');
        $this->worksheet->setCellValue($this->column++ . $this->row, 'Category');
        $this->worksheet->setCellValue($this->column++ . $this->row, 'Total wo tax');
        $this->worksheet->setCellValue($this->column++ . $this->row, 'Taxes %');
        $this->worksheet->setCellValue($this->column++ . $this->row, 'Taxes Amount');
        $this->worksheet->setCellValue($this->column++ . $this->row, 'Total');
        $this->worksheet->setCellValue($this->column++ . $this->row, '€/Wp');

        $this->row++;
        $this->column = 'A';

        // Iteration for the final sale budget. Final price for the client

        $this->mainBudgetDetailsManagers->each(function (BudgetCategoryDetailsManager $budgetCategoryDetailsManager) {
            $this->printSellingCategoryDetails($budgetCategoryDetailsManager, true);
            $budgetCategoryDetailsManager->sub_budget_category_details_managers->each(function (BudgetCategoryDetailsManager $subBudgetCategoryDetailsManager) {
                $this->printSellingCategoryDetails($subBudgetCategoryDetailsManager);
                $subBudgetCategoryDetailsManager->budgetDetails->each(function (BudgetDetail $budgetDetail) {
                    $this->printSellingBudgetDetails($budgetDetail);
                });
            });
        });

        // Printing the grand totals for the final sale budget
        $this->printSellingBudgetGrandTotals();

        // Iteration for the costs budget

        $this->row += 4;
        $this->column = 'A';

        // Imprimimos la cabecera de la tabla de precios finales
        $this->worksheet->setCellValue($this->column++ . $this->row, 'Cod');
        $this->worksheet->setCellValue($this->column++ . $this->row, 'Category');
        $this->worksheet->setCellValue($this->column++ . $this->row, 'Cost Amount');
        $this->worksheet->setCellValue($this->column++ . $this->row, '€/Wp');
        $this->worksheet->setCellValue($this->column++ . $this->row, 'Gain %');
        $this->worksheet->setCellValue($this->column++ . $this->row, 'Gain');
        $this->worksheet->setCellValue($this->column++ . $this->row, 'Sell Price');

        $this->row++;
        $this->column = 'A';

        $this->mainBudgetDetailsManagers->each(function (BudgetCategoryDetailsManager $budgetCategoryDetailsManager) {
            $this->printCostCategoryDetails($budgetCategoryDetailsManager, true);
            $budgetCategoryDetailsManager->sub_budget_category_details_managers->each(function (BudgetCategoryDetailsManager $subBudgetCategoryDetailsManager) {
                $this->printCostCategoryDetails($subBudgetCategoryDetailsManager);
                $subBudgetCategoryDetailsManager->budgetDetails->each(function (BudgetDetail $budgetDetail) {
                    $this->printCostBudgetDetails($budgetDetail);
                });
            });
        });

        // Printing the grand totals for the costs budget
        $this->printCostBudgetGrandTotals();
        
        foreach (range('A', $this->worksheet->getHighestDataColumn()) as $col) {
            $this->worksheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($this->spreadsheet);

        $folderPath = '/path/to/your/';

        // Comprueba si el directorio existe, si no, lo crea
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $filePath = $folderPath . 'report.xlsx';

        $writer->save($filePath);

        return $filePath;
    }


    public function printSellingCategoryDetails(BudgetCategoryDetailsManager $budgetCategoryDetailsManager, bool $isMain = false)
    {
        // Primero establecemos el valor de las celdas
        $startColumn = $this->column = 'A';

        $this->worksheet->setCellValue(
            $codeCell = $this->column++ . $this->row,
            $budgetCategoryDetailsManager->itemCategory->prefix_code
        );
        $this->worksheet->setCellValue(
            $nameCell = $this->column++ . $this->row,
            $budgetCategoryDetailsManager->itemCategory->name
        );
        $this->worksheet->setCellValue(
            $totalWithoutTaxCell = $this->column++ . $this->row,
            $budgetCategoryDetailsManager->total_without_tax
        );
        $this->worksheet->setCellValue(
            $taxPercentageCell = $this->column++ . $this->row,
            $budgetCategoryDetailsManager->tax_prorated_percentage
        );
        $this->worksheet->setCellValue(
            $taxAmountCell = $this->column++ . $this->row,
            $budgetCategoryDetailsManager->tax_amount
        );
        $this->worksheet->setCellValue(
            $totalWithTaxCell = $this->column++ . $this->row,
            $budgetCategoryDetailsManager->total_with_tax
        );
        $this->worksheet->setCellValue(
            $pricePerWpCell = $this->column++ . $this->row,
            $budgetCategoryDetailsManager->price_per_wp
        );

        if (!$isMain) {
            $this->worksheet->getStyle($codeCell)->getAlignment()->setIndent(1);
            $this->worksheet->getStyle($nameCell)->getAlignment()->setIndent(1);
        }

        $this->worksheet->getStyle($totalWithoutTaxCell)->getNumberFormat()->setFormatCode('#,##0.00 €');
        $this->worksheet->getStyle($taxPercentageCell)->getNumberFormat()->setFormatCode('0.00%');
        $this->worksheet->getStyle($taxAmountCell)->getNumberFormat()->setFormatCode('#,##0.00 €');
        $this->worksheet->getStyle($totalWithTaxCell)->getNumberFormat()->setFormatCode('#,##0.00 €');
        $this->worksheet->getStyle($pricePerWpCell)->getNumberFormat()->setFormatCode('#,##0.00 "€/Wp"');

        // Luego obtenemos el estilo del rango de celdas y establecemos la fuente en negrita
        if ($isMain) {
            $this->worksheet->getStyle($startColumn . $this->row . ':' . $this->column . $this->row)->getFont()->setBold(true);
        }

        $this->row++;
    }
    
    public function printCostCategoryDetails(BudgetCategoryDetailsManager $budgetCategoryDetailsManager, bool $isMain = false)
    {
        // Primero establecemos el valor de las celdas
        $startColumn = $this->column = 'A';

        $this->worksheet->setCellValue(
            $codeCell = $this->column++ . $this->row,
            $budgetCategoryDetailsManager->itemCategory->prefix_code
        );
        $this->worksheet->setCellValue(
            $nameCell = $this->column++ . $this->row,
            $budgetCategoryDetailsManager->itemCategory->name
        );
        $this->worksheet->setCellValue(
            $costWithoutTaxCell = $this->column++ . $this->row,
            $budgetCategoryDetailsManager->cost_without_tax
        );
        $this->worksheet->setCellValue(
            $costPerWpCell = $this->column++ . $this->row,
            $budgetCategoryDetailsManager->cost_per_wp
        );
        $this->worksheet->setCellValue(
            $gainMarginCell = $this->column++ . $this->row,
            $budgetCategoryDetailsManager->gain_margin
        );
        $this->worksheet->setCellValue(
            $gainAmountCell = $this->column++ . $this->row,
            $budgetCategoryDetailsManager->gain_amount
        );
        $this->worksheet->setCellValue(
            $sellPriceCell = $this->column++ . $this->row,
            $budgetCategoryDetailsManager->total_without_tax
        );

        if (!$isMain) {
            $this->worksheet->getStyle($codeCell)->getAlignment()->setIndent(1);
            $this->worksheet->getStyle($nameCell)->getAlignment()->setIndent(1);
        }

        $this->worksheet->getStyle($costWithoutTaxCell)->getNumberFormat()->setFormatCode('#,##0.00 €');
        $this->worksheet->getStyle($costPerWpCell)->getNumberFormat()->setFormatCode('#,##0.00 "€/Wp"');
        $this->worksheet->getStyle($gainMarginCell)->getNumberFormat()->setFormatCode('0.00%');
        $this->worksheet->getStyle($gainAmountCell)->getNumberFormat()->setFormatCode('#,##0.00 €');
        $this->worksheet->getStyle($sellPriceCell)->getNumberFormat()->setFormatCode('#,##0.00 €');

        // Luego obtenemos el estilo del rango de celdas y establecemos la fuente en negrita
        if ($isMain) {
            $this->worksheet->getStyle($startColumn . $this->row . ':' . $this->column . $this->row)->getFont()->setBold(true);
        }

        $this->row++;
    }

    public function printSellingBudgetDetails(BudgetDetail $budgetDetail)
    {
        $this->column = 'A';

        $this->worksheet->setCellValue(
            $codeCell = $this->column++ . $this->row,
            $budgetDetail->item->internal_cod
        );
        $this->worksheet->setCellValue(
            $nameCell = $this->column++ . $this->row,
            $budgetDetail->item->name
        );
        $this->worksheet->setCellValue(
            $totalWithoutTaxCell = $this->column++ . $this->row,
            $budgetDetail->total_without_tax
        );
        $this->worksheet->setCellValue(
            $taxPercentageCell = $this->column++ . $this->row,
            $budgetDetail->tax_percentage
        );
        $this->worksheet->setCellValue(
            $taxAmountCell = $this->column++ . $this->row,
            $budgetDetail->tax_amount
        );
        $this->worksheet->setCellValue(
            $totalWithTaxCell = $this->column++ . $this->row,
            $budgetDetail->total_with_tax
        );
        $this->worksheet->setCellValue(
            $pricePerWpCell = $this->column++ . $this->row,
            $budgetDetail->price_per_wp
        );

        $this->worksheet->getStyle($codeCell)->getAlignment()->setIndent(2);
        $this->worksheet->getStyle($nameCell)->getAlignment()->setIndent(2);

        $this->worksheet->getStyle($totalWithoutTaxCell)->getNumberFormat()->setFormatCode('#,##0.00 €');
        $this->worksheet->getStyle($taxPercentageCell)->getNumberFormat()->setFormatCode('0.00%');
        $this->worksheet->getStyle($taxAmountCell)->getNumberFormat()->setFormatCode('#,##0.00 €');
        $this->worksheet->getStyle($totalWithTaxCell)->getNumberFormat()->setFormatCode('#,##0.00 €');
        $this->worksheet->getStyle($pricePerWpCell)->getNumberFormat()->setFormatCode('#,##0.00 "€/Wp"');

        $this->row++;
    }

    public function printCostBudgetDetails(BudgetDetail $budgetDetail)
    {
        $this->column = 'A';

        $this->worksheet->setCellValue(
            $codeCell = $this->column++ . $this->row,
            $budgetDetail->item->internal_cod
        );
        $this->worksheet->setCellValue(
            $nameCell = $this->column++ . $this->row,
            $budgetDetail->item->name
        );
        $this->worksheet->setCellValue(
            $costWithoutTaxCell = $this->column++ . $this->row,
            $budgetDetail->cost_without_tax
        );
        $this->worksheet->setCellValue(
            $costPerWpCell = $this->column++ . $this->row,
            $budgetDetail->cost_per_wp
        );
        $this->worksheet->setCellValue(
            $gainMarginCell = $this->column++ . $this->row,
            $budgetDetail->gain_margin
        );
        $this->worksheet->setCellValue(
            $gainAmountCell = $this->column++ . $this->row,
            $budgetDetail->gain_amount
        );
        $this->worksheet->setCellValue(
            $sellPriceCell = $this->column++ . $this->row,
            $budgetDetail->total_without_tax
        );

        $this->worksheet->getStyle($codeCell)->getAlignment()->setIndent(2);
        $this->worksheet->getStyle($nameCell)->getAlignment()->setIndent(2);

        $this->worksheet->getStyle($costWithoutTaxCell)->getNumberFormat()->setFormatCode('#,##0.00 €');
        $this->worksheet->getStyle($costPerWpCell)->getNumberFormat()->setFormatCode('#,##0.00 "€/Wp"');
        $this->worksheet->getStyle($gainMarginCell)->getNumberFormat()->setFormatCode('0.00%');
        $this->worksheet->getStyle($gainAmountCell)->getNumberFormat()->setFormatCode('#,##0.00 €');
        $this->worksheet->getStyle($sellPriceCell)->getNumberFormat()->setFormatCode('#,##0.00 €');

        $this->row++;
    }

    public function printSellingBudgetGrandTotals()
    {
        // Primero establecemos el valor de las celdas
        $startColumn = $this->column = 'C';
        $this->row += 2;

        $this->worksheet->setCellValue(
            $totalWithoutTaxCell = $this->column++ . $this->row,
            $this->budget->total_without_tax
        );
        $this->worksheet->setCellValue(
            $taxPercentageCell = $this->column++ . $this->row,
            $this->budget->tax_prorated
        );
        $this->worksheet->setCellValue(
            $taxAmountCell = $this->column++ . $this->row,
            $this->budget->tax_amount
        );
        $this->worksheet->setCellValue(
            $totalWithTaxCell = $this->column++ . $this->row,
            $this->budget->total_with_tax
        );
        $this->worksheet->setCellValue(
            $pricePerWpCell = $this->column++ . $this->row,
            $this->budget->price_per_wp
        );

        $this->worksheet->getStyle($totalWithoutTaxCell)->getNumberFormat()->setFormatCode('#,##0.00 €');
        $this->worksheet->getStyle($taxPercentageCell)->getNumberFormat()->setFormatCode('0.00%');
        $this->worksheet->getStyle($taxAmountCell)->getNumberFormat()->setFormatCode('#,##0.00 €');
        $this->worksheet->getStyle($totalWithTaxCell)->getNumberFormat()->setFormatCode('#,##0.00 €');
        $this->worksheet->getStyle($pricePerWpCell)->getNumberFormat()->setFormatCode('#,##0.00 "€/Wp"');

        $this->worksheet->getStyle($startColumn . $this->row . ':' . $this->column . $this->row)->getFont()->setBold(true);
    }

    public function printCostBudgetGrandTotals()
    {
        // Primero establecemos el valor de las celdas
        $startColumn = $this->column = 'C';
        $this->row += 2;

        $this->worksheet->setCellValue(
            $costAmountCell = $this->column++ . $this->row,
            $this->budget->cost_amount
        );
        $this->worksheet->setCellValue(
            $costPerWpCell = $this->column++ . $this->row,
            $this->budget->cost_per_wp
        );
        $this->worksheet->setCellValue(
            $gainMarginCell = $this->column++ . $this->row,
            $this->budget->prorated_gain_margin
        );
        $this->worksheet->setCellValue(
            $gainAmountCell = $this->column++ . $this->row,
            $this->budget->gain_amount
        );
        $this->worksheet->setCellValue(
            $sellPriceCell = $this->column++ . $this->row,
            $this->budget->total_without_tax
        );

        $this->worksheet->getStyle($costAmountCell)->getNumberFormat()->setFormatCode('#,##0.00 €');
        $this->worksheet->getStyle($costPerWpCell)->getNumberFormat()->setFormatCode('#,##0.00 "€/Wp"');
        $this->worksheet->getStyle($gainMarginCell)->getNumberFormat()->setFormatCode('0.00%');
        $this->worksheet->getStyle($gainAmountCell)->getNumberFormat()->setFormatCode('#,##0.00 €');
        $this->worksheet->getStyle($sellPriceCell)->getNumberFormat()->setFormatCode('#,##0.00 €');

        $this->worksheet->getStyle($startColumn . $this->row . ':' . $this->column . $this->row)->getFont()->setBold(true);
    }
}
