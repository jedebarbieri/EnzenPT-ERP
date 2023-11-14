<?php

namespace App\Services\Projects;

use App\Models\Procurement\ItemCategory;
use App\Models\Projects\Budget;
use App\Models\Projects\BudgetDetail;
use App\Structures\Projects\BudgetCategoryDetailsManager;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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

    /**
     * Indicates the initial column to start printing the budget
     */
    const STARTING_COLUMN = 1;

    /**
     * Indicates the initial row to start printing the budget
     */
    const STARTING_ROW = 1;

    /**
     * Indicates the format for the currency values
     */
    const CURRENCY_FORMAT = '#,##0.00 €';

    /**
     * Indicates the format for the percentage values
     */
    const PERCENTAGE_FORMAT = '0.00 %';

    /**
     * Indicates the format for the price per Wp values
     */
    const PRICE_PER_WP_FORMAT = '#,####0.0000 "€/Wp"';

    /**
     * Indicates the initial indent for the multilevel categories rows.
     * The subcategories will have an indent of 1 more than the main categories.
     * And the budget details will have an indent of 2 more than the main categories.
     */
    const INIT_INDENT = 0;

    /**
     * Indicates the path to the file that will be used as a base template
     */
    const TEMPLATE_FILE = ABSOLUTE_APP_PATH . 'resources/report_templates/Budget_Report_Template.xlsx';

    /**
     * Indicates the path to the folder where the temporary files will be stored
     */
    const OUTPUT_TEMP_PATH = ABSOLUTE_APP_PATH . 'storage/app/temp/';

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

    /**
     * @var Worksheet $budgetSheet This is the reference to the worksheet that content the selling budget and the cost budget
     */
    public Worksheet $budgetSheet;

    public Worksheet $projectDetails;

    public $row = self::STARTING_ROW;

    public $column = self::STARTING_COLUMN;

    public function __construct(Budget $budget)
    {
        $this->budget = $budget;
        $this->row = 1;
        $this->mainBudgetDetailsManagers = new Collection();
    }

    /**
     * Generates the Excel report for the budget.
     * @return string The path to the generated Excel file
     */    
    public function generateReport()
    {
        // Crear una instancia de Spreadsheet cargando el archivo existente
        $this->spreadsheet = IOFactory::load(self::TEMPLATE_FILE);
        
        $this->projectDetails = $this->spreadsheet->getActiveSheet();
        $this->projectDetails->setTitle('Project Details');

        $this->printProjectDetails();

        $this->budgetSheet = $this->spreadsheet->createSheet();

        $this->budgetSheet->setTitle('Budget');

        
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
                $this->budgetSheet->getRowDimension($this->row)->setOutlineLevel(1);
                $this->budgetSheet->getRowDimension($this->row)->setVisible(false);
                $this->budgetSheet->getRowDimension($this->row)->setCollapsed(true);
                $this->row++;

                $subBudgetCategoryDetailsManager->budgetDetails->each(function (BudgetDetail $budgetDetail) {
                    $this->printSellingBudgetDetails($budgetDetail);
        
                    // Return to the previous row to set the outline level for the grouping
                    $this->row--;
                    $this->budgetSheet->getRowDimension($this->row)->setOutlineLevel(2);
                    $this->budgetSheet->getRowDimension($this->row)->setVisible(false);
                    $this->budgetSheet->getRowDimension($this->row)->setCollapsed(true);
                    $this->row++;
                });
            });
        });

        // Painting the last border of the last row
        $this->budgetSheet->getStyle(
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
                $this->budgetSheet->getRowDimension($this->row)->setOutlineLevel(1);
                $this->budgetSheet->getRowDimension($this->row)->setVisible(false);
                $this->budgetSheet->getRowDimension($this->row)->setCollapsed(true);
                $this->row++;

                $subBudgetCategoryDetailsManager->budgetDetails->each(function (BudgetDetail $budgetDetail) {
                    $this->printCostBudgetDetails($budgetDetail);
        
                    // Return to the previous row to set the outline level for the grouping
                    $this->row--;
                    $this->budgetSheet->getRowDimension($this->row)->setOutlineLevel(2);
                    $this->budgetSheet->getRowDimension($this->row)->setVisible(false);
                    $this->budgetSheet->getRowDimension($this->row)->setCollapsed(true);
                    $this->row++;
                });
            });
        });
        
        // Painting the last border of the last row
        $this->budgetSheet->getStyle(
            $this->cell(
                col: self::STARTING_COLUMN
            ) . ':' . $this->cell()
        )->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

        // Printing the grand totals for the costs budget
        $this->printCostBudgetGrandTotals();
        
        foreach (range(Coordinate::stringFromColumnIndex(self::STARTING_COLUMN), $this->budgetSheet->getHighestDataColumn()) as $col) {
            $this->budgetSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->projectDetails->setShowGridlines(false);
        $this->budgetSheet->setShowGridlines(false);
        
        $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');

        // Comprueba si el directorio existe, si no, lo crea
        if (!file_exists(self::OUTPUT_TEMP_PATH)) {
            mkdir(self::OUTPUT_TEMP_PATH, 0777, true);
        }

        $filePath = self::OUTPUT_TEMP_PATH . date('Ymd_His', time()) . ' - ' . $this->budget->name . '.xlsx';

        $writer->save($filePath);

        return $filePath;
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

    /**
     * Sets the general properties of the spreadsheet
     */
    public function settingGeneralProperties()
    {
        $properties = $this->spreadsheet->getProperties();

        $properties->setCreator('Enzen Portutal');
        $properties->setLastModifiedBy('System - Enzen Portutal');
        $properties->setTitle($this->budget->name);
        $properties->setSubject('Budget Report - ' . $this->budget->name);
        $properties->setDescription('');
        $properties->setKeywords('');
        $properties->setCategory('Budget Report');
    }

    /**
     * Prints the project details, the main information of the project
     */
    public function printProjectDetails()
    {
        $this->projectDetails->getCell('Project_Name_Cell')->setValue($this->budget->project_number . ' - ' . $this->budget->project_name);
        $this->projectDetails->getCell('Gross_Margin_Cell')->setValue($this->budget->gain_amount);
        $this->projectDetails->getCell('Date_Cell')->setValue(now()->format('d/m/Y H:i:s'));
        $this->projectDetails->getCell('Total_Peak_Power_Cell')->setValue($this->budget->total_peak_power);
    }

    /**
     * Prints the header for the final sale budget
     */
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
            $this->budgetSheet->setCellValue($this->cell(), $header);
            $this->column++;
        }

        $this->budgetSheet
            ->getStyle($this->cell(
                    col: self::STARTING_COLUMN,
                ) . ':' . $this->cell(
                    col: $lastColumn
                )
            )
            ->applyFromArray(self::STYLE_MAIN_HEADER);

        $this->budgetSheet
            ->getStyle($this->cell(
                    col: self::STARTING_COLUMN + 2,
                ) . ':' . $this->cell(
                    col: $lastColumn
                )
            )
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Establecer altura de fila
        $this->budgetSheet->getRowDimension($this->row)->setRowHeight(30);

        $this->row++;
    }

    /**
     * Prints the header for the costs budget
     */
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
            $this->budgetSheet->setCellValue($this->cell(), $header);
            $this->column++;
        }

        $this->budgetSheet
            ->getStyle($this->cell(
                    col: self::STARTING_COLUMN,
                    row: $this->row
                ) . ':' . $this->cell(
                    col: $lastColumn
                )
            )
            ->applyFromArray(self::STYLE_MAIN_HEADER);

        $this->budgetSheet
            ->getStyle($this->cell(
                    col: self::STARTING_COLUMN + 2,
                ) . ':' . $this->cell(
                    col: $lastColumn
                )
            )
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Establecer altura de fila
        $this->budgetSheet->getRowDimension($this->row)->setRowHeight(30);

        $this->row++;
    }

    /**
     * Prints the details for a selling category
     */
    public function printSellingCategoryDetails(BudgetCategoryDetailsManager $budgetCategoryDetailsManager, bool $isMain = false)
    {
        // Primero establecemos el valor de las celdas
        $startColumn = $this->column = self::STARTING_COLUMN;

        $this->budgetSheet->setCellValue(
            $codeCell = $this->cell(),
            $budgetCategoryDetailsManager->itemCategory->prefix_code
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $nameCell = $this->cell(),
            $budgetCategoryDetailsManager->itemCategory->name
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $totalWithoutTaxCell = $this->cell(),
            $budgetCategoryDetailsManager->total_without_tax_after_discount
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $taxPercentageCell = $this->cell(),
            "--"
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $taxAmountCell = $this->cell(),
            $budgetCategoryDetailsManager->tax_amount
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $totalWithTaxCell = $this->cell(),
            $budgetCategoryDetailsManager->total_with_tax
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $pricePerWpCell = $this->cell(),
            $budgetCategoryDetailsManager->price_per_wp
        );

        $this->budgetSheet->getStyle($totalWithoutTaxCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->budgetSheet->getStyle($taxPercentageCell)->getNumberFormat()->setFormatCode(self::PERCENTAGE_FORMAT);
        $this->budgetSheet->getStyle($taxPercentageCell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $this->budgetSheet->getStyle($taxAmountCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->budgetSheet->getStyle($totalWithTaxCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->budgetSheet->getStyle($pricePerWpCell)->getNumberFormat()->setFormatCode(self::PRICE_PER_WP_FORMAT);

        // Luego obtenemos el estilo del rango de celdas y establecemos la fuente en negrita
        $rangeStyle = $this->budgetSheet->getStyle(
            $this->cell(
                col: $startColumn
            ) . ':' . $this->cell()
        );

        $indent = self::INIT_INDENT + 1;

        if ($isMain) {
            $rangeStyle->applyFromArray(self::STYLE_MAIN_CATEGORY);
            $this->budgetSheet->getRowDimension($this->row)->setRowHeight(17);
        } else {
            $rangeStyle->applyFromArray(self::STYLE_SUB_CATEGORY);
            $indent = self::INIT_INDENT + 2;
        }

        $this->budgetSheet->getStyle($codeCell)->getAlignment()->setIndent($indent);
        $this->budgetSheet->getStyle($nameCell)->getAlignment()->setIndent($indent);

        $this->row++;
    }
    
    /**
     * Prints the details for a cost category
     */
    public function printCostCategoryDetails(BudgetCategoryDetailsManager $budgetCategoryDetailsManager, bool $isMain = false)
    {
        // Primero establecemos el valor de las celdas
        $startColumn = $this->column = self::STARTING_COLUMN;

        $this->budgetSheet->setCellValue(
            $codeCell = $this->cell(),
            $budgetCategoryDetailsManager->itemCategory->prefix_code
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $nameCell = $this->cell(),
            $budgetCategoryDetailsManager->itemCategory->name
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $totalCost = $this->cell(),
            $budgetCategoryDetailsManager->cost_amount
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $costPerWpCell = $this->cell(),
            $budgetCategoryDetailsManager->cost_per_wp
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $gainMarginCell = $this->cell(),
            $budgetCategoryDetailsManager->gain_margin
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $gainAmountCell = $this->cell(),
            $budgetCategoryDetailsManager->gain_amount
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $sellPriceCell = $this->cell(),
            $budgetCategoryDetailsManager->total_without_tax
        );

        $this->budgetSheet->getStyle($totalCost)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->budgetSheet->getStyle($costPerWpCell)->getNumberFormat()->setFormatCode(self::PRICE_PER_WP_FORMAT);
        $this->budgetSheet->getStyle($gainMarginCell)->getNumberFormat()->setFormatCode(self::PERCENTAGE_FORMAT);
        $this->budgetSheet->getStyle($gainAmountCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->budgetSheet->getStyle($sellPriceCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);

        // Luego obtenemos el estilo del rango de celdas y establecemos la fuente en negrita
        $rangeStyle = $this->budgetSheet->getStyle(
            $this->cell(
                col: $startColumn
            ) . ':' . $this->cell()
        );

        $indent = self::INIT_INDENT + 1;

        if ($isMain) {
            $rangeStyle->applyFromArray(self::STYLE_MAIN_CATEGORY);
            $this->budgetSheet->getRowDimension($this->row)->setRowHeight(17);
        } else {
            $rangeStyle->applyFromArray(self::STYLE_SUB_CATEGORY);
            $indent = self::INIT_INDENT + 2;
        }

        $this->budgetSheet->getStyle($codeCell)->getAlignment()->setIndent($indent);
        $this->budgetSheet->getStyle($nameCell)->getAlignment()->setIndent($indent);

        $this->row++;
    }

    /**
     * Prints the details for a budget detail 
     */
    public function printSellingBudgetDetails(BudgetDetail $budgetDetail)
    {
        $this->column = self::STARTING_COLUMN;

        $this->budgetSheet->setCellValue(
            $codeCell = $this->cell(),
            $budgetDetail->item->internal_cod
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $nameCell = $this->cell(),
            $budgetDetail->item->name
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $totalWithoutTaxCell = $this->cell(),
            $budgetDetail->total_without_tax_after_discount
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $taxPercentageCell = $this->cell(),
            $budgetDetail->tax_percentage
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $taxAmountCell = $this->cell(),
            $budgetDetail->tax_amount
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $totalWithTaxCell = $this->cell(),
            $budgetDetail->total_with_tax
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $pricePerWpCell = $this->cell(),
            $budgetDetail->price_per_wp
        );

        $this->budgetSheet->getStyle($totalWithoutTaxCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->budgetSheet->getStyle($taxPercentageCell)->getNumberFormat()->setFormatCode(self::PERCENTAGE_FORMAT);
        $this->budgetSheet->getStyle($taxAmountCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->budgetSheet->getStyle($totalWithTaxCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->budgetSheet->getStyle($pricePerWpCell)->getNumberFormat()->setFormatCode(self::PRICE_PER_WP_FORMAT);

        $rangeStyle = $this->budgetSheet->getStyle(
            $this->cell(
                col: self::STARTING_COLUMN
            ) . ':' . $this->cell()
        );
        
        $rangeStyle->applyFromArray(self::STYLE_BUDGET_DETAIL);

        $this->budgetSheet->getStyle($codeCell)->getAlignment()->setIndent(self::INIT_INDENT + 3);
        $this->budgetSheet->getStyle($nameCell)->getAlignment()->setIndent(self::INIT_INDENT + 3);

        $this->row++;
    }

    /**
     * Prints the details for a budget detail 
     */
    public function printCostBudgetDetails(BudgetDetail $budgetDetail)
    {
        $this->column = self::STARTING_COLUMN;

        $this->budgetSheet->setCellValue(
            $codeCell = $this->cell(),
            $budgetDetail->item->internal_cod
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $nameCell = $this->cell(),
            $budgetDetail->item->name
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $totalCost = $this->cell(),
            $budgetDetail->cost_amount
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $costPerWpCell = $this->cell(),
            $budgetDetail->cost_per_wp
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $gainMarginCell = $this->cell(),
            $budgetDetail->gain_margin
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $gainAmountCell = $this->cell(),
            $budgetDetail->gain_amount
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $sellPriceCell = $this->cell(),
            $budgetDetail->total_without_tax
        );

        $this->budgetSheet->getStyle($totalCost)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->budgetSheet->getStyle($costPerWpCell)->getNumberFormat()->setFormatCode(self::PRICE_PER_WP_FORMAT);
        $this->budgetSheet->getStyle($gainMarginCell)->getNumberFormat()->setFormatCode(self::PERCENTAGE_FORMAT);
        $this->budgetSheet->getStyle($gainAmountCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->budgetSheet->getStyle($sellPriceCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);

        $rangeStyle = $this->budgetSheet->getStyle(
            $this->cell(
                col: self::STARTING_COLUMN
            ) . ':' . $this->cell()
        );
        
        $rangeStyle->applyFromArray(self::STYLE_BUDGET_DETAIL);

        $this->budgetSheet->getStyle($codeCell)->getAlignment()->setIndent(self::INIT_INDENT + 3);
        $this->budgetSheet->getStyle($nameCell)->getAlignment()->setIndent(self::INIT_INDENT + 3);

        $this->row++;
    }

    /**
     * Prints the grand totals for the final sale budget
     */
    public function printSellingBudgetGrandTotals()
    {
        // Primero establecemos el valor de las celdas
        $startColumn = $this->column = self::STARTING_COLUMN + 2;
        $this->row += 1;

        $this->budgetSheet->setCellValue(
            $totalWithoutTaxCell = $this->cell(),
            $this->budget->total_without_tax_after_discount
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $taxPercentageCell = $this->cell(),
            "--"
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $taxAmountCell = $this->cell(),
            $this->budget->tax_amount
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $totalWithTaxCell = $this->cell(),
            $this->budget->total_with_tax
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $pricePerWpCell = $this->cell(),
            $this->budget->total_price_per_wp
        );     

        $this->budgetSheet->getStyle($totalWithoutTaxCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->budgetSheet->getStyle($taxPercentageCell)->getNumberFormat()->setFormatCode(self::PERCENTAGE_FORMAT);
        $this->budgetSheet->getStyle($taxPercentageCell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $this->budgetSheet->getStyle($taxAmountCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->budgetSheet->getStyle($totalWithTaxCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->budgetSheet->getStyle($pricePerWpCell)->getNumberFormat()->setFormatCode(self::PRICE_PER_WP_FORMAT);

        $this->budgetSheet
            ->getStyle($this->cell(
                    col: self::STARTING_COLUMN,
                    row: $this->row
                ) . ':' . $this->cell(
                    col: 7
                )
            )
            ->applyFromArray(self::STYLE_MAIN_FOOTER);
        
        // Establecer altura de fila
        $this->budgetSheet->getRowDimension($this->row)->setRowHeight(30);
    }

    /**
     * Prints the grand totals for the costs budget
     */
    public function printCostBudgetGrandTotals()
    {
        // Primero establecemos el valor de las celdas
        $startColumn = $this->column = self::STARTING_COLUMN + 2;
        $this->row += 1;

        $this->budgetSheet->setCellValue(
            $costAmountCell = $this->cell(),
            $this->budget->cost_amount
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $costPerWpCell = $this->cell(),
            $this->budget->cost_per_wp
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $gainMarginCell = $this->cell(),
            $this->budget->prorated_gain_margin
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $gainAmountCell = $this->cell(),
            $this->budget->gain_amount
        );
        $this->column++;

        $this->budgetSheet->setCellValue(
            $sellPriceCell = $this->cell(),
            $this->budget->total_without_tax_after_discount
        );

        $this->budgetSheet->getStyle($costAmountCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->budgetSheet->getStyle($costPerWpCell)->getNumberFormat()->setFormatCode(self::PRICE_PER_WP_FORMAT);
        $this->budgetSheet->getStyle($gainMarginCell)->getNumberFormat()->setFormatCode(self::PERCENTAGE_FORMAT);
        $this->budgetSheet->getStyle($gainAmountCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);
        $this->budgetSheet->getStyle($sellPriceCell)->getNumberFormat()->setFormatCode(self::CURRENCY_FORMAT);

        $this->budgetSheet
            ->getStyle($this->cell(
                    col: self::STARTING_COLUMN,
                    row: $this->row
                ) . ':' . $this->cell(
                    col: 7
                )
            )
            ->applyFromArray(self::STYLE_MAIN_FOOTER);
        
        // Establecer altura de fila
        $this->budgetSheet->getRowDimension($this->row)->setRowHeight(30);
    }
}
