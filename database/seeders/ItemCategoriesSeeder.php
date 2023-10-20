<?php

namespace Database\Seeders;

use App\Models\Procurement\ItemCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Deshabilitar restricciones de clave foránea
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        // Limpia la tabla antes de insertar nuevos datos
        ItemCategory::truncate();

        // Volver a habilitar restricciones de clave foránea
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $categories = [
            '01.PMA' => [
                'PROJECT MANAGEMENT, STUDIES & INSURANCES',
                '01' => 'PROJECT MANAGEMENT',
                '02' => 'LICENSES & INSURANCES & FEES',
                '03' => 'STUDIES',
            ],
            '02.CON' => [
                'CONSTRUCTION SITE & MACHINERY',
                '01' => 'CONSTRUCTION TOPICS',
                '02' => 'MACHINERY',
            ],
            '03.PVM' => [
                'PV MODULES',
                '01' => 'PV MODULES',
                '02' => 'TRANSPORT - PV MODULE',
            ],
            '04.INV' => [
                'INVERTERS',
                '01' => 'INVERTERS',
                '02' => 'XX',
            ],
            '05.ACC' => [
                'INVERTERS ACCESSORIES AND MONITORING SYSTEM',
                '01' => 'INVERTERS ACCESSORIES AND MONITORING SYSTEM',
                '02' => 'XX',
            ],
            '06.CAB' => [
                'ELECTRICAL CABLING AND CONECTORS',
                '01' => 'ELECTRICAL CABLING AND CONECTORS',
                '02' => 'XX',
            ],
            '07.STR' => [
                'STRUCTURE',
                '01' => 'STRUCTURE - IN PLANE',
                '02' => 'STRUCTURE - TILT',
                '03' => 'STRUCTURE - TRACKER',
                '04' => 'STRUCTURE - OTHERS',
            ],
            '08.MVE' => [
                'MV EQUIPMENT',
                '01' => 'MV EQUIPMENT',
                '02' => 'XX',
            ],
            '09.LVI' => [
                'LV INFRASTRUCTURE & ELECTRICAL INSTALLATION',
                '01' => 'LV INFRASTRUCTURE & ELECTRICAL INSTALLATION',
                '02' => 'GROUND PROTECTION',
            ],
            '10.CIV' => [
                'INFRASTRUCTURES & CIVIL WORKS',
                '01' => 'FENCING',
                '02' => 'ROADS',
                '03' => 'MONITORING AND CONTROL HOUSE',
                '04' => 'ROOF',
            ],
            '11.SUR' => [
                'SURVEILANCE AND SECURITY SYSTEM',
                '01' => 'SURVEILANCE AND SECURITY SYSTEM',
                '02' => 'XX',
            ],
            '12.THP' => [
                'THIRD PARTIES',
                '01' => 'XX',
            ],
        ];
        

        // Itera sobre el array y crea las instancias de ItemCategory
        foreach ($categories as $prefix => $subcategories) {
            $mainCategory = ItemCategory::create([
                'name' => ucwords(strtolower($subcategories[0])),
                'prefix' => $prefix,
            ]);
            
            // Las subcategorías comienzan desde el índice 1
            // Utiliza array_slice para omitir el primer elemento (nombre de la clase padre)
            foreach (array_slice($subcategories, 1, null, true) as $subCatPrefix => $subcategoryName) {
                ItemCategory::create([
                    'name' => ucwords(strtolower($subcategoryName)),
                    'prefix' => str_pad($subCatPrefix, 2, '0', STR_PAD_LEFT),
                    ItemCategory::PARENT_COLUMN_NAME => $mainCategory->id,
                ]);
            }
        }
    }
}
