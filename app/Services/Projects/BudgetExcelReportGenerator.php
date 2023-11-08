<?php

namespace App\Services\Projects;

use App\Models\Procurement\ItemCategory;
use App\Models\Projects\Budget;
use App\Models\Projects\BudgetDetail;
use App\Structures\Projects\BudgetCategoryDetailsManager;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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

    const STARTING_COLUMN = 1;

    const STARTING_ROW = 1;

    const CURRENCY_FORMAT = '#,##0.00 €';

    const PERCENTAGE_FORMAT = '0.00 %';

    const PRICE_PER_WP_FORMAT = '#,####0.0000 "€/Wp"';

    const INIT_INDENT = 0;

    const STYLE_MAIN_HEADER = [
        'font' => [
            'bold' => true,
            'color' => ['argb' => 'FFFFFFFF'],
            'size' => 14,
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FF808080'], // gray 35%
        ],
        'borders' => [
            'outline' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'], // black
            ],
        ],
    ];

    const STYLE_MAIN_FOOTER = [
        'font' => [
            'bold' => true,
            'size' => 14,
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'top' => [
                'borderStyle' => Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'], // black
            ],
        ],
    ];

    const STYLE_MAIN_CATEGORY = [
        'font' => [
            'bold' => true,
            'color' => ['argb' => '00000000'],
            'size' => 12,
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFD9D9D9'], // gray 15%
        ],
        'borders' => [
            'outline' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'], // black
            ],
        ],
    ];

    const STYLE_SUB_CATEGORY = [
        'font' => [
            'bold' => true,
            'color' => ['argb' => '00000000'],
            'size' => 11,
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFF2F2F2'], // gray 5%
        ],
        'borders' => [
            'outline' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'], // black
            ],
        ],
    ];

    const STYLE_BUDGET_DETAIL = [
        'font' => [
            'bold' => false,
            'color' => ['argb' => '00000000'],
            'size' => 11,
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'left' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'], // black
            ],
            'right' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'], // black
            ],
        ],
    ];


    public Budget $budget;

    public Collection $mainBudgetDetailsManagers;

    public Spreadsheet $spreadsheet;

    public Worksheet $worksheet;

    public $row = self::STARTING_ROW;

    public $column = self::STARTING_COLUMN;

    public function __construct(Budget $budget)
    {
        $this->budget = $budget;
        $this->row = 1;
        $this->mainBudgetDetailsManagers = new Collection();
    }

    /**
     * Returns the string reference to the current cell.
     * Optionally you can pass or the $row or the $col, otherwise will use the current values.
     * This won't change the current values of the row or column.
     * @return string
     */
    public function cell(?string $row = null, ?string $col = null)
    {
        return Coordinate::stringFromColumnIndex($col ?? $this->column) . ($row ?? $this->row);
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

        $this->row = self::STARTING_ROW;

        $this->printSellingBudgetHeader();

        // Iteration for the final sale budget. Final price for the client

        $this->mainBudgetDetailsManagers->each(function (BudgetCategoryDetailsManager $budgetCategoryDetailsManager) {
            $this->printSellingCategoryDetails($budgetCategoryDetailsManager, true);
            

            $budgetCategoryDetailsManager->sub_budget_category_details_managers->each(function (BudgetCategoryDetailsManager $subBudgetCategoryDetailsManager) {
                $this->printSellingCategoryDetails($subBudgetCategoryDetailsManager);
        
                // Return to the previous row to set the outline level for the grouping
                $this->row--;
                $this->worksheet->getRowDimension($this->row)->setOutlineLevel(1);
                $this->worksheet->getRowDimension($this->row)->setVisible(false);
                $this->worksheet->getRowDimension($this->row)->setCollapsed(true);
                $this->row++;

                $subBudgetCategoryDetailsManager->budgetDetails->each(function (BudgetDetail $budgetDetail) {
                    $this->printSellingBudgetDetails($budgetDetail);
        
                    // Return to the previous row to set the outline level for the grouping
                    $this->row--;
                    $this->worksheet->getRowDimension($this->row)->setOutlineLevel(2);
                    $this->worksheet->getRowDimension($this->row)->setVisible(false);
                    $this->worksheet->getRowDimension($this->row)->setCollapsed(true);
                    $this->row++;
                });
            });
        });

        // Painting the last border of the last row
        $this->worksheet->getStyle(
            $this->cell(
                col: self::STARTING_COLUMN
            ) . ':' . $this->cell()
        )->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

        // Printing the grand totals for the final sale budget
        $this->printSellingBudgetGrandTotals();

        // Iteration for the costs budget

        $this->row += 4;

        $this->printCostBudgetHeader();

        $this->mainBudgetDetailsManagers->each(function (BudgetCategoryDetailsManager $budgetCategoryDetailsManager) {
            $this->printCostCategoryDetails($budgetCategoryDetailsManager, true);
            $budgetCategoryDetailsManager->sub_budget_category_details_managers->each(function (BudgetCategoryDetailsManager $subBudgetCategoryDetailsManager) {
                $this->printCostCategoryDetails($subBudgetCategoryDetailsManager);
        
                // Return to the previous row to set the outline level for the grouping
                $this->row--;
                $this->worksheet->getRowDimension($this->row)->setOutlineLevel(1);
                $this->worksheet->getRowDimension($this->row)->setVisible(false);
                $this->worksheet->getRowDimension($this->row)->setCollapsed(true);
                $this->row++;

                $subBudgetCategoryDetailsManager->budgetDetails->each(function (BudgetDetail $budgetDetail) {
                    $this->printCostBudgetDetails($budgetDetail);
        
                    // Return to the previous row to set the outline level for the grouping
                    $this->row--;
                    $this->worksheet->getRowDimension($this->row)->setOutlineLevel(2);
                    $this->worksheet->getRowDimension($this->row)->setVisible(false);
                    $this->worksheet->getRowDimension($this->row)->setCollapsed(true);
                    $this->row++;
                });
            });
        });
        
        // Painting the last border of the last row
        $this->worksheet->getStyle(
            $this->cell(
                col: self::STARTING_COLUMN
            ) . ':' . $this->cell()
        )->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

        // Printing the grand totals for the costs budget
        $this->printCostBudgetGrandTotals();
        
        foreach (range(Coordinate::stringFromColumnIndex(self::STARTING_COLUMN), $this->worksheet->getHighestDataColumn()) as $col) {
            $this->worksheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->worksheet->setShowGridlines(false);

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

    public function printSellingBudgetHeader()
    {
        $this->column = self::STARTING_COLUMN;
        
        $headers = [
            'Cod',
            'Category',
            'Total wo tax',
            'Taxes %',
            'Taxes Amount',
            'Total',
            '€/Wp',
        ];

        $lastColumn = $this->column + count($headers) - 1;

        foreach ($headers as $header) {
            $this->worksheet->setCellValue($this->cell(), $header);
            $this->column++;
        }

        $this->worksheet
            ->getStyle($this->cell(
                    col: self::STARTING_COLUMN,
                ) . ':' . $this->cell(
                    col: $lastColumn
                )
            )
            ->applyFromArray(self::STYLE_MAIN_HEADER);

        $this->worksheet
            ->getStyle($this->cell(
                    col: self::STARTING_COLUMN + 2,
                ) . ':' . $this->cell(
                    col: $lastColumn
                )
            )
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Establecer altura de fila
        $this->worksheet->getRowDimension($this->row)->setRowHeight(30);

        $this->row++;
    }

    public function printCostBudgetHeader()
    {
        $this->column = self::STARTING_COLUMN;
        
        $headers = [
            'Cod',
            'Category',
            'Cost Amount',
            '€/Wp',
            'Gain %',
            'Gain',
            'Sell Price',
        ];

        $lastColumn = $this->column + count($headers) - 1;

        foreach ($headers as $header) {
            $this->worksheet->setCellValue($this->cell(), $header);
            $this->column++;
        }

        $this->worksheet
            ->getStyle($this->cell(
                    col: self::STARTING_COLUMN,
                    row: $this->row
                ) . ':' . $this->cell(
                    col: $lastColumn
                )
            )
            ->applyFromArray(self::STYLE_MAIN_HEADER);

        $this->worksheet
            ->getStyle($this->cell(
                    col: self::STARTING_COLUMN + 2,
                ) . ':' . $this->cell(
                    col: $lastColumn
                )
            )
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Establecer altura de fila
        $this->worksheet->getRowDimension($this->row)->setRowHeight(30);

        $this->row++;
    }

    public function printSellingCategoryDetails(BudgetCategoryDetailsManager $budgetCategoryDetailsManager, bool $isMain = false)
    {
        // Primero establecemos el valor de las celdas
        $startColumn = $this->column = self::STARTING_COLUMN;

        $this->worksheet->setCellValue(
            $codeCell = $this->cell(),
            $budgetCategoryDetailsManager->itemCategory->prefix_code
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $nameCell = $this->cell(),
            $budgetCategoryDetailsManager->itemCategory->name
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $totalWithoutTaxCell = $this->cell(),
            $budgetCategoryDetailsManager->total_without_tax_after_discount
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $taxPercentageCell = $this->cell(),
            $budgetCategoryDetailsManager->tax_prorated_percentage
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $taxAmountCell = $this->cell(),
            $budgetCategoryDetailsManager->tax_amount
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $totalWithTaxCell = $this->cell(),
            $budgetCategoryDetailsManager->total_with_tax
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $pricePerWpCell = $this->cell(),
            $budgetCategoryDetailsManager->price_per_wp
        );

        $this->worksheet->getStyle($totalWithoutTaxCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->worksheet->getStyle($taxPercentageCell)->getNumberFormat()->setFormatCode(self::PERCENTAGE_FORMAT);
        $this->worksheet->getStyle($taxAmountCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->worksheet->getStyle($totalWithTaxCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->worksheet->getStyle($pricePerWpCell)->getNumberFormat()->setFormatCode(self::PRICE_PER_WP_FORMAT);

        // Luego obtenemos el estilo del rango de celdas y establecemos la fuente en negrita
        $rangeStyle = $this->worksheet->getStyle(
            $this->cell(
                col: $startColumn
            ) . ':' . $this->cell()
        );

        $indent = self::INIT_INDENT + 1;

        if ($isMain) {
            $rangeStyle->applyFromArray(self::STYLE_MAIN_CATEGORY);
            $this->worksheet->getRowDimension($this->row)->setRowHeight(17);
        } else {
            $rangeStyle->applyFromArray(self::STYLE_SUB_CATEGORY);
            $indent = self::INIT_INDENT + 2;
        }

        $this->worksheet->getStyle($codeCell)->getAlignment()->setIndent($indent);
        $this->worksheet->getStyle($nameCell)->getAlignment()->setIndent($indent);

        $this->row++;
    }
    
    public function printCostCategoryDetails(BudgetCategoryDetailsManager $budgetCategoryDetailsManager, bool $isMain = false)
    {
        // Primero establecemos el valor de las celdas
        $startColumn = $this->column = self::STARTING_COLUMN;

        $this->worksheet->setCellValue(
            $codeCell = $this->cell(),
            $budgetCategoryDetailsManager->itemCategory->prefix_code
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $nameCell = $this->cell(),
            $budgetCategoryDetailsManager->itemCategory->name
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $totalCost = $this->cell(),
            $budgetCategoryDetailsManager->cost_amount
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $costPerWpCell = $this->cell(),
            $budgetCategoryDetailsManager->cost_per_wp
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $gainMarginCell = $this->cell(),
            $budgetCategoryDetailsManager->gain_margin
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $gainAmountCell = $this->cell(),
            $budgetCategoryDetailsManager->gain_amount
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $sellPriceCell = $this->cell(),
            $budgetCategoryDetailsManager->total_without_tax
        );

        $this->worksheet->getStyle($totalCost)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->worksheet->getStyle($costPerWpCell)->getNumberFormat()->setFormatCode(self::PRICE_PER_WP_FORMAT);
        $this->worksheet->getStyle($gainMarginCell)->getNumberFormat()->setFormatCode(self::PERCENTAGE_FORMAT);
        $this->worksheet->getStyle($gainAmountCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->worksheet->getStyle($sellPriceCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);

        // Luego obtenemos el estilo del rango de celdas y establecemos la fuente en negrita
        $rangeStyle = $this->worksheet->getStyle(
            $this->cell(
                col: $startColumn
            ) . ':' . $this->cell()
        );

        $indent = self::INIT_INDENT + 1;

        if ($isMain) {
            $rangeStyle->applyFromArray(self::STYLE_MAIN_CATEGORY);
            $this->worksheet->getRowDimension($this->row)->setRowHeight(17);
        } else {
            $rangeStyle->applyFromArray(self::STYLE_SUB_CATEGORY);
            $indent = self::INIT_INDENT + 2;
        }

        $this->worksheet->getStyle($codeCell)->getAlignment()->setIndent($indent);
        $this->worksheet->getStyle($nameCell)->getAlignment()->setIndent($indent);

        $this->row++;
    }

    public function printSellingBudgetDetails(BudgetDetail $budgetDetail)
    {
        $this->column = self::STARTING_COLUMN;

        $this->worksheet->setCellValue(
            $codeCell = $this->cell(),
            $budgetDetail->item->internal_cod
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $nameCell = $this->cell(),
            $budgetDetail->item->name
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $totalWithoutTaxCell = $this->cell(),
            $budgetDetail->total_without_tax_after_discount
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $taxPercentageCell = $this->cell(),
            $budgetDetail->tax_percentage
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $taxAmountCell = $this->cell(),
            $budgetDetail->tax_amount
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $totalWithTaxCell = $this->cell(),
            $budgetDetail->total_with_tax
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $pricePerWpCell = $this->cell(),
            $budgetDetail->price_per_wp
        );

        $this->worksheet->getStyle($totalWithoutTaxCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->worksheet->getStyle($taxPercentageCell)->getNumberFormat()->setFormatCode(self::PERCENTAGE_FORMAT);
        $this->worksheet->getStyle($taxAmountCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->worksheet->getStyle($totalWithTaxCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->worksheet->getStyle($pricePerWpCell)->getNumberFormat()->setFormatCode(self::PRICE_PER_WP_FORMAT);

        $rangeStyle = $this->worksheet->getStyle(
            $this->cell(
                col: self::STARTING_COLUMN
            ) . ':' . $this->cell()
        );
        
        $rangeStyle->applyFromArray(self::STYLE_BUDGET_DETAIL);

        $this->worksheet->getStyle($codeCell)->getAlignment()->setIndent(self::INIT_INDENT + 3);
        $this->worksheet->getStyle($nameCell)->getAlignment()->setIndent(self::INIT_INDENT + 3);

        $this->row++;
    }

    public function printCostBudgetDetails(BudgetDetail $budgetDetail)
    {
        $this->column = self::STARTING_COLUMN;

        $this->worksheet->setCellValue(
            $codeCell = $this->cell(),
            $budgetDetail->item->internal_cod
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $nameCell = $this->cell(),
            $budgetDetail->item->name
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $totalCost = $this->cell(),
            $budgetDetail->cost_amount
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $costPerWpCell = $this->cell(),
            $budgetDetail->cost_per_wp
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $gainMarginCell = $this->cell(),
            $budgetDetail->gain_margin
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $gainAmountCell = $this->cell(),
            $budgetDetail->gain_amount
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $sellPriceCell = $this->cell(),
            $budgetDetail->total_without_tax
        );

        $this->worksheet->getStyle($totalCost)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->worksheet->getStyle($costPerWpCell)->getNumberFormat()->setFormatCode(self::PRICE_PER_WP_FORMAT);
        $this->worksheet->getStyle($gainMarginCell)->getNumberFormat()->setFormatCode(self::PERCENTAGE_FORMAT);
        $this->worksheet->getStyle($gainAmountCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->worksheet->getStyle($sellPriceCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);

        $rangeStyle = $this->worksheet->getStyle(
            $this->cell(
                col: self::STARTING_COLUMN
            ) . ':' . $this->cell()
        );
        
        $rangeStyle->applyFromArray(self::STYLE_BUDGET_DETAIL);

        $this->worksheet->getStyle($codeCell)->getAlignment()->setIndent(self::INIT_INDENT + 3);
        $this->worksheet->getStyle($nameCell)->getAlignment()->setIndent(self::INIT_INDENT + 3);

        $this->row++;
    }

    public function printSellingBudgetGrandTotals()
    {
        // Primero establecemos el valor de las celdas
        $startColumn = $this->column = self::STARTING_COLUMN + 2;
        $this->row += 1;

        $this->worksheet->setCellValue(
            $totalWithoutTaxCell = $this->cell(),
            $this->budget->total_without_tax_after_discount
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $taxPercentageCell = $this->cell(),
            $this->budget->tax_prorated
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $taxAmountCell = $this->cell(),
            $this->budget->tax_amount
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $totalWithTaxCell = $this->cell(),
            $this->budget->total_with_tax
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $pricePerWpCell = $this->cell(),
            $this->budget->total_price_per_wp
        );     

        $this->worksheet->getStyle($totalWithoutTaxCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->worksheet->getStyle($taxPercentageCell)->getNumberFormat()->setFormatCode(self::PERCENTAGE_FORMAT);
        $this->worksheet->getStyle($taxAmountCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->worksheet->getStyle($totalWithTaxCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->worksheet->getStyle($pricePerWpCell)->getNumberFormat()->setFormatCode(self::PRICE_PER_WP_FORMAT);

        $this->worksheet
            ->getStyle($this->cell(
                    col: self::STARTING_COLUMN,
                    row: $this->row
                ) . ':' . $this->cell(
                    col: 7
                )
            )
            ->applyFromArray(self::STYLE_MAIN_FOOTER);
        
        // Establecer altura de fila
        $this->worksheet->getRowDimension($this->row)->setRowHeight(30);
    }

    public function printCostBudgetGrandTotals()
    {
        // Primero establecemos el valor de las celdas
        $startColumn = $this->column = self::STARTING_COLUMN + 2;
        $this->row += 1;

        $this->worksheet->setCellValue(
            $costAmountCell = $this->cell(),
            $this->budget->cost_amount
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $costPerWpCell = $this->cell(),
            $this->budget->cost_per_wp
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $gainMarginCell = $this->cell(),
            $this->budget->prorated_gain_margin
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $gainAmountCell = $this->cell(),
            $this->budget->gain_amount
        );
        $this->column++;

        $this->worksheet->setCellValue(
            $sellPriceCell = $this->cell(),
            $this->budget->total_without_tax_after_discount
        );

        $this->worksheet->getStyle($costAmountCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->worksheet->getStyle($costPerWpCell)->getNumberFormat()->setFormatCode(self::PRICE_PER_WP_FORMAT);
        $this->worksheet->getStyle($gainMarginCell)->getNumberFormat()->setFormatCode(self::PERCENTAGE_FORMAT);
        $this->worksheet->getStyle($gainAmountCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->worksheet->getStyle($sellPriceCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);

        $this->worksheet
            ->getStyle($this->cell(
                    col: self::STARTING_COLUMN,
                    row: $this->row
                ) . ':' . $this->cell(
                    col: 7
                )
            )
            ->applyFromArray(self::STYLE_MAIN_FOOTER);
        
        // Establecer altura de fila
        $this->worksheet->getRowDimension($this->row)->setRowHeight(30);
    }
}
