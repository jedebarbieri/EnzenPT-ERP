<?php

namespace Database\Seeders;

use App\Models\Procurement\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Deshabilitar restricciones de clave foránea
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        // Limpia la tabla antes de insertar nuevos datos
        Item::truncate();
        
        // Volver a habilitar restricciones de clave foránea
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $categories = [
            ['id' => 2, 'prefix' => '01.PMA-01'],
            ['id' => 3, 'prefix' => '01.PMA-02'],
            ['id' => 4, 'prefix' => '01.PMA-03'],
            ['id' => 6, 'prefix' => '02.CON-01'],
            ['id' => 7, 'prefix' => '02.CON-02'],
            ['id' => 9, 'prefix' => '03.PVM-01'],
            ['id' => 10, 'prefix' => '03.PVM-02'],
            ['id' => 12, 'prefix' => '04.INV-01'],
            ['id' => 13, 'prefix' => '04.INV-02'],
            ['id' => 15, 'prefix' => '05.ACC-01'],
            ['id' => 16, 'prefix' => '05.ACC-02'],
            ['id' => 18, 'prefix' => '06.CAB-01'],
            ['id' => 19, 'prefix' => '06.CAB-02'],
            ['id' => 21, 'prefix' => '07.STR-01'],
            ['id' => 22, 'prefix' => '07.STR-02'],
            ['id' => 23, 'prefix' => '07.STR-03'],
            ['id' => 24, 'prefix' => '07.STR-04'],
            ['id' => 26, 'prefix' => '08.MVE-01'],
            ['id' => 27, 'prefix' => '08.MVE-02'],
            ['id' => 29, 'prefix' => '09.LVI-01'],
            ['id' => 30, 'prefix' => '09.LVI-02'],
            ['id' => 32, 'prefix' => '10.CIV-01'],
            ['id' => 33, 'prefix' => '10.CIV-02'],
            ['id' => 34, 'prefix' => '10.CIV-03'],
            ['id' => 35, 'prefix' => '10.CIV-04'],
            ['id' => 37, 'prefix' => '11.SUR-01'],
            ['id' => 38, 'prefix' => '11.SUR-02'],
            ['id' => 40, 'prefix' => '12.THP-01'],
        ];

        $items = [
            ['internal_cod' => '01.PMA-01-01', 'name' => 'PROJECT MANAGER'],
            ['internal_cod' => '01.PMA-01-02', 'name' => 'SITE MANAGER'],
            ['internal_cod' => '01.PMA-01-03', 'name' => 'TECHNICAL MANAGER'],
            ['internal_cod' => '01.PMA-01-04', 'name' => 'WORKS TEAM LEADER'],
            ['internal_cod' => '01.PMA-01-05', 'name' => 'CIVIL WORKS TEAM LEADER'],
            ['internal_cod' => '01.PMA-01-06', 'name' => 'CIVIL TECHNICIAN'],
            ['internal_cod' => '01.PMA-01-07', 'name' => 'MECHANICAL WORKS TEAM LEADER'],
            ['internal_cod' => '01.PMA-01-08', 'name' => 'ELECTRICAL WORKS DIRECTOR'],
            ['internal_cod' => '01.PMA-01-09', 'name' => 'ELECTRICAL WORKS TEAM LEADER'],
            ['internal_cod' => '01.PMA-01-10', 'name' => 'MONITORING & SECURITY WORKS TECHNICIAN'],
            ['internal_cod' => '01.PMA-01-11', 'name' => 'WORKS PREPARATION RESPONSIBLE'],
            ['internal_cod' => '01.PMA-01-12', 'name' => 'DESIGNER'],
            ['internal_cod' => '01.PMA-01-13', 'name' => 'HEALTH AND SAFETY'],
            ['internal_cod' => '01.PMA-01-14', 'name' => 'SUPPLY MANAGER'],
            ['internal_cod' => '01.PMA-01-15', 'name' => 'LOGISTICS MANAGER'],
            ['internal_cod' => '01.PMA-01-16', 'name' => 'TRAVEL EXPENSES'],
            ['internal_cod' => '01.PMA-01-17', 'name' => 'HOUSE RENTAL '],
            ['internal_cod' => '01.PMA-01-18', 'name' => 'FOOD'],
            ['internal_cod' => '01.PMA-02-01', 'name' => 'CONTRUCTION LICENSES'],
            ['internal_cod' => '01.PMA-02-02', 'name' => 'CONSTRUCTION WORKS INSURANCE'],
            ['internal_cod' => '01.PMA-02-03', 'name' => 'BANK GUARANTEES DURING CONSTRUCTION'],
            ['internal_cod' => '01.PMA-02-04', 'name' => 'FINANCING COSTS FOR CONSTRUCTION'],
            ['internal_cod' => '01.PMA-02-05', 'name' => 'DEVELOPMENT LICENSES'],
            ['internal_cod' => '01.PMA-03-01', 'name' => 'TOPOGRAPHICAL STUDY'],
            ['internal_cod' => '01.PMA-03-02', 'name' => 'GEOTECHNICAL STUDY'],
            ['internal_cod' => '01.PMA-03-03', 'name' => 'STABILITY STUDY'],
            ['internal_cod' => '01.PMA-03-04', 'name' => 'ELECTRICAL ENGINEERING PROJECT'],
            ['internal_cod' => '01.PMA-03-05', 'name' => 'COMMISSIONING GENSUN'],
            ['internal_cod' => '02.CON-01-01', 'name' => 'MANPOWER'],
            ['internal_cod' => '02.CON-01-02', 'name' => 'MODULES MOUNTING'],
            ['internal_cod' => '02.CON-01-03', 'name' => 'STUCTURE MOUNTING'],
            ['internal_cod' => '02.CON-01-04', 'name' => 'ELECTRICAL INSTALLATION'],
            ['internal_cod' => '02.CON-01-05', 'name' => 'STAIRS AND LIFELINES'],
            ['internal_cod' => '02.CON-01-06', 'name' => 'CONSTRUCTION SITE ADEQUATED TO CONSTRUCTION WORKS'],
            ['internal_cod' => '02.CON-01-07', 'name' => 'INDIVIDUAL PROTECTION EQUIPMENT'],
            ['internal_cod' => '02.CON-01-08', 'name' => 'GROUP PROTECTION EQUIPMENT'],
            ['internal_cod' => '02.CON-01-09', 'name' => 'WC MONO UNITS'],
            ['internal_cod' => '02.CON-01-10', 'name' => 'CONTAINERS (OFFICES & WHAREHOUSES)'],
            ['internal_cod' => '02.CON-01-11', 'name' => 'SECURITY AND SURVEILANCE DURING CONSTRUCTIONS WORKS'],
            ['internal_cod' => '02.CON-01-12', 'name' => 'ENERGY SUPPLY GENERATOR & FUEL'],
            ['internal_cod' => '02.CON-01-13', 'name' => 'SKIP (CONSTRUCTION TRASH CONTAINER)'],
            ['internal_cod' => '02.CON-02-01', 'name' => 'MACHINERY RENTING'],
            ['internal_cod' => '02.CON-02-02', 'name' => 'BACKHOE'],
            ['internal_cod' => '02.CON-02-03', 'name' => 'TELESCOPIC HANDLER'],
            ['internal_cod' => '02.CON-02-04', 'name' => 'SCISSORS-SCAFFOLD'],
            ['internal_cod' => '02.CON-02-05', 'name' => 'CRANE'],
            ['internal_cod' => '02.CON-02-06', 'name' => 'ANCHORS HAMMER'],
            ['internal_cod' => '02.CON-02-07', 'name' => 'OTHER'],
            ['internal_cod' => '03.PVM-01-01', 'name' => 'LONGI SOLAR'],
            ['internal_cod' => '03.PVM-01-01-01', 'name' => 'LONGI - 550WP'],
            ['internal_cod' => '03.PVM-01-01-02', 'name' => 'LONGI - 555WP'],
            ['internal_cod' => '03.PVM-01-02', 'name' => 'JINKO SOLAR'],
            ['internal_cod' => '03.PVM-01-02-01', 'name' => 'JINKO - 550WP'],
            ['internal_cod' => '03.PVM-01-02-02', 'name' => 'JINKO - 555WP'],
            ['internal_cod' => '03.PVM-02-01', 'name' => 'XX'],
            ['internal_cod' => '04.INV-01-01', 'name' => 'SUNGROW'],
            ['internal_cod' => '04.INV-01-01-01', 'name' => 'SUNGROW SG33CX'],
            ['internal_cod' => '04.INV-01-01-02', 'name' => 'SUNGROW SG40CX'],
            ['internal_cod' => '04.INV-01-01-03', 'name' => 'SUNGROW SG50CX'],
            ['internal_cod' => '04.INV-01-01-04', 'name' => 'SUNGROW SG110CX'],
            ['internal_cod' => '04.INV-01-02', 'name' => 'HUAWEI'],
            ['internal_cod' => '04.INV-01-02-01', 'name' => 'HUAWEI SUN 2000 (100 KWac)'],
            ['internal_cod' => '04.INV-02-01', 'name' => 'XX'],
            ['internal_cod' => '05.ACC-01-01', 'name' => 'DC DISTRIBUTION SWITCH BOARD'],
            ['internal_cod' => '05.ACC-01-02', 'name' => 'DC DISTRIBUTION SWITCH BOARD'],
            ['internal_cod' => '05.ACC-01-03', 'name' => 'MONITORING SYSTEM (MW TRACKER PV PLANT W/ STRING MONITORING)'],
            ['internal_cod' => '05.ACC-01-04', 'name' => 'DATA LOGGER'],
            ['internal_cod' => '05.ACC-01-05', 'name' => 'METEO STATION'],
            ['internal_cod' => '05.ACC-01-06', 'name' => 'SUPERVISION AND COMMUNICATION'],
            ['internal_cod' => '05.ACC-01-07', 'name' => 'ENERGY METER'],
            ['internal_cod' => '05.ACC-01-08', 'name' => 'TIs - CURRENT TRANSFORMERS'],
            ['internal_cod' => '05.ACC-02-01', 'name' => 'XX'],
            ['internal_cod' => '06.CAB-01-01', 'name' => 'SOLAR CABLE 4 mm2 0,6/1kV'],
            ['internal_cod' => '06.CAB-01-02', 'name' => 'SOLAR CABLE 6 mm2 0,6/1kV'],
            ['internal_cod' => '06.CAB-01-03', 'name' => 'CABLE AL 16 mm2 0,6/1kV'],
            ['internal_cod' => '06.CAB-01-04', 'name' => 'CABLE AL 25 mm2 0,6/1kV'],
            ['internal_cod' => '06.CAB-01-05', 'name' => 'CABLE K 35 mm2 0,6/1kV'],
            ['internal_cod' => '06.CAB-01-06', 'name' => 'CABLE K 50 mm2 0,6/1kV'],
            ['internal_cod' => '06.CAB-01-07', 'name' => 'CABLE K 70 mm2 0,6/1kV'],
            ['internal_cod' => '06.CAB-01-08', 'name' => 'CABLE K 95 mm2 0,6/1kV'],
            ['internal_cod' => '06.CAB-01-09', 'name' => 'CABLE K 150 mm2 0,6/1kV'],
            ['internal_cod' => '06.CAB-01-10', 'name' => 'CABLE K 240 mm2 0,6/1kV'],
            ['internal_cod' => '06.CAB-01-11', 'name' => 'CABLE AL 70 mm2 0,6/1kV'],
            ['internal_cod' => '06.CAB-01-12', 'name' => 'CABLE AL 95 mm2 0,6/1kV'],
            ['internal_cod' => '06.CAB-01-13', 'name' => 'CABLE AL 150 mm2 0,6/1kV'],
            ['internal_cod' => '06.CAB-01-14', 'name' => 'Connectivity cable'],
            ['internal_cod' => '06.CAB-01-15', 'name' => 'Terminal copper '],
            ['internal_cod' => '06.CAB-01-16', 'name' => 'Terminal Bi-metallic'],
            ['internal_cod' => '06.CAB-01-17', 'name' => 'GROUND CABLE 6MM2'],
            ['internal_cod' => '06.CAB-01-18', 'name' => 'MC4'],
            ['internal_cod' => '06.CAB-01-19', 'name' => 'NEKED COPPER CABLE 25MM2'],
            ['internal_cod' => '06.CAB-02-01', 'name' => 'XX'],
            ['internal_cod' => '07.STR-01-01', 'name' => 'STRUCTURE - IN PLANE'],
            ['internal_cod' => '07.STR-02-01', 'name' => 'STRUCTURE - TILT'],
            ['internal_cod' => '07.STR-03-01', 'name' => 'STRUCTURE - TRACKER'],
            ['internal_cod' => '07.STR-04-01', 'name' => 'STRUCTURE - OTHERS'],
            ['internal_cod' => '08.MVE-01-01', 'name' => 'TRANSFORMATION CENTER WITH 1 10000 KVA TRANSFORMER, GROUP OF INPUT AND OUTPUT CELLS, LV SWITCHBOARD, GROUND GRID, IN PRE-FABRICATED CONCRETE KIOSK , ACCORDING TO ELECTRICAL COMPANY NORMATIVES.'],
            ['internal_cod' => '08.MVE-01-02', 'name' => 'TRANSFORMATION CENTER WITH 1 800 KVA TRANSFORMER, GROUP OF INPUT AND OUTPUT CELLS, LV SWITCHBOARD, GROUND GRID, IN PRE-FABRICATED CONCRETE KIOSK , ACCORDING TO ELECTRICAL COMPANY NORMATIVES.'],
            ['internal_cod' => '08.MVE-01-03', 'name' => 'CONNECTION CENTER 10 MVA WITH PRIMARY CELLS'],
            ['internal_cod' => '08.MVE-01-04', 'name' => 'CONNECTION CENTER 5 MVA WITH PRIMARY CELLS'],
            ['internal_cod' => '08.MVE-01-05', 'name' => 'CONNECTION CENTER 10 MVA WITH PRIMARY CELLS'],
            ['internal_cod' => '08.MVE-01-06', 'name' => 'MV COUNTER'],
            ['internal_cod' => '08.MVE-01-07', 'name' => 'COUNTER SWITCHBOARD'],
            ['internal_cod' => '08.MVE-01-08', 'name' => 'INTERNAL MV CIRCUIT DITCH INCLUDING OPENING AND ENCLOSURES, SUPPLY OF SAND AND HAD.'],
            ['internal_cod' => '08.MVE-01-09', 'name' => 'EXTERNAL MV CIRCUIT DITCH INCLUDING OPENING AND ENCLOSURES, SUPPLY OF SAND AND HAD.'],
            ['internal_cod' => '08.MVE-01-10', 'name' => 'INTERNAL MV MANHOLE'],
            ['internal_cod' => '08.MVE-01-11', 'name' => 'EXTERNAL MV MANHOLE'],
            ['internal_cod' => '08.MVE-01-12', 'name' => 'AERIAL MV LINE 20 KV'],
            ['internal_cod' => '08.MVE-01-13', 'name' => 'STEEL SUPPORT FOR MV LINE'],
            ['internal_cod' => '08.MVE-01-14', 'name' => 'PASS INTERNAL MV CABLE'],
            ['internal_cod' => '08.MVE-01-15', 'name' => 'PASS EXTERNAL MV CABLE'],
            ['internal_cod' => '08.MVE-01-16', 'name' => 'INTERNAL MV CONNECTION, ACCORDING TO ELECTRICAL COMPANY NORMATIVES, CLOSING THE MV RING, INCLUDING GROUND-AERIAL CONNECTION.'],
            ['internal_cod' => '08.MVE-02-01', 'name' => 'XX'],
            ['internal_cod' => '09.LVI-01-01', 'name' => 'CABLE TRAY'],
            ['internal_cod' => '09.LVI-01-02', 'name' => 'CABLE TRAY ACESSORIES'],
            ['internal_cod' => '09.LVI-01-03', 'name' => 'LV CIRCUIT DITCH INCLUDING OPENING AND ENCLOSURES, SUPPLY OF SAND AND HAD.'],
            ['internal_cod' => '09.LVI-01-04', 'name' => 'LV MANHOLE'],
            ['internal_cod' => '09.LVI-01-05', 'name' => 'PASS ELECTRIC CABLE 6 mm2, INCLUDING ALL THE NECESSARY WORKS AND ACESSORIES  FOR ITS CONNECTION.'],
            ['internal_cod' => '09.LVI-01-06', 'name' => 'PASS ELECTRIC CABLE 95 mm2, INCLUDING ALL THE NECESSARY WORKS AND ACESSORIES  FOR ITS CONNECTION.'],
            ['internal_cod' => '09.LVI-01-07', 'name' => 'PASS ELECTRIC CABLE 150 mm2, INCLUDING ALL THE NECESSARY WORKS AND ACESSORIES  FOR ITS CONNECTION.'],
            ['internal_cod' => '09.LVI-01-08', 'name' => 'PASS ELECTRIC CABLE 95 mm2, INCLUDING ALL THE NECESSARY WORKS AND ACESSORIES  FOR ITS CONNECTION.'],
            ['internal_cod' => '09.LVI-01-09', 'name' => 'PASS ELECTRIC CABLE 120 mm2, INCLUDING ALL THE NECESSARY WORKS AND ACESSORIES  FOR ITS CONNECTION.'],
            ['internal_cod' => '09.LVI-01-10', 'name' => 'PASS COMUNICATION CABLES'],
            ['internal_cod' => '09.LVI-01-11', 'name' => 'PASS CABLE FOR AUXILIARY SERVICES'],
            ['internal_cod' => '09.LVI-01-12', 'name' => 'PV INSTALLATION PRODUCTION METER'],
            ['internal_cod' => '09.LVI-01-13', 'name' => 'LV SWITCHBOARDS'],
            ['internal_cod' => '09.LVI-01-14', 'name' => 'AUXILIARY SERVICES SWITCHBOARDS'],
            ['internal_cod' => '09.LVI-01-15', 'name' => 'PROTECTIONS SWITCHBOARDS'],
            ['internal_cod' => '09.LVI-01-16', 'name' => 'ENERGY METER DGEG'],
            ['internal_cod' => '09.LVI-02-01', 'name' => '35 mm2 COPPER CABLES FOR GROUND CONNECTION RING'],
            ['internal_cod' => '09.LVI-02-02', 'name' => 'COPPER ELECTRICAL PICK FOR TC GROUND CONNECTION'],
            ['internal_cod' => '09.LVI-02-03', 'name' => 'HOMOPOLAR PROTECTION'],
            ['internal_cod' => '09.LVI-02-04', 'name' => 'GROUND GRID INSTALATION'],
            ['internal_cod' => '09.LVI-02-05', 'name' => 'CONNECTIVITY CABLE'],
            ['internal_cod' => '10.CIV-01-01', 'name' => 'GALVANIZED STEEL FENCING, WITH 2M HIGH'],
            ['internal_cod' => '10.CIV-01-02', 'name' => '5M WIDE GATE ACCESS TO THE PLANT'],
            ['internal_cod' => '10.CIV-01-03', 'name' => 'LAND PREPARATION'],
            ['internal_cod' => '10.CIV-01-04', 'name' => 'STRIPPING OR REMOVAL OF TOPSOIL WITH THE AVERAGE THICKNESS OF 20 cm, FOR STRUCTURES AND ROADS. IT COVERS THE OPERATIONS OF EXCAVATION, LOADING, TRANSPORTATION, UPLOADING AND SPREADING OR POSSIBLE PLACMENT IN TEMPORARY STORAGE FOR LATER USE, AND COMPENSATION FOR DEPOSIT.'],
            ['internal_cod' => '10.CIV-01-05', 'name' => 'DISASSEMBLE/EXCAVATION SITE OF ANY KIND, TO GIVE THE LAND TO GRAZING IN THE PLANNED DEVELOPMENT PROJECT, USING MECHANICAL MEANS. INCLUDING LOADING AND UPLOADING AND TRANSPORTATION OF PRODUCTS TO WAREHOUSE OR PLACE OF LOAN. AND IF NECESSARY AT THE END OF OUR TRANSPORTATION PRODUCTS LEFT OVER FROM DUMP.'],
            ['internal_cod' => '10.CIV-01-06', 'name' => 'REINSTATEMENT OF ENVIRONMENTAL CONDITIONS AT THE SITE, INCLUDING REAFFORESTATION'],
            ['internal_cod' => '10.CIV-02-01', 'name' => 'EXECUTION OF INTERNAL AND EXTERNAL ROADS '],
            ['internal_cod' => '10.CIV-02-02', 'name' => 'WORKS IN CONCRETE'],
            ['internal_cod' => '10.CIV-02-03', 'name' => 'CONCRETE FOUNDATIONS'],
            ['internal_cod' => '10.CIV-02-04', 'name' => 'ESCAVATIONS FOR CONCRETE FOUNDATIONS (M³)'],
            ['internal_cod' => '10.CIV-02-05', 'name' => 'TERRAIN PREPARATION FOR TRANSFORMATION CENTER'],
            ['internal_cod' => '10.CIV-03-01', 'name' => 'MONITORING AND CONTROL HOUSE, WITH SMALL WAREHOUSE FOR REPLACEMENT SPARE PARTS'],
            ['internal_cod' => '10.CIV-04-01', 'name' => 'LIFELINES AND STAIRS'],
            ['internal_cod' => '10.CIV-04-02', 'name' => 'CABLE TRAYS 100mm'],
            ['internal_cod' => '10.CIV-04-03', 'name' => 'TRAY COVER 100mm'],
            ['internal_cod' => '10.CIV-04-04', 'name' => 'CABLE TRAYS 200mm'],
            ['internal_cod' => '10.CIV-04-05', 'name' => 'TRAY COVER 200mm'],
            ['internal_cod' => '10.CIV-04-06', 'name' => 'CABLE TRAYS 300mm'],
            ['internal_cod' => '10.CIV-04-07', 'name' => 'TRAY COVER 300mm'],
            ['internal_cod' => '11.SUR-01-01', 'name' => 'ONE CHANNEL MULTIPLEXER WITH DIGITAL RECORDER OF 160 GB,MPEG4 FORMAT, IMAGE TRANSMISSION VIA TCP- IP, USB ENTRY FOR BACKUP.'],
            ['internal_cod' => '11.SUR-01-02', 'name' => 'HIGH PRECISION DOMUS COLOUR CAMERA WITH INFRA-RED PROJECTOR, OUTSIDE INSTALLATION WITH INDEPENDENT POWER SOURCE.'],
            ['internal_cod' => '11.SUR-01-03', 'name' => 'CCTV SYSTEM WITH HIGH PRECISION  COLOR CAMERA WITH INFRA-RED PROJECTOR, OUTSIDE INSTALLATION WITH INDEPENDENT POWER SOURCE.'],
            ['internal_cod' => '11.SUR-01-04', 'name' => 'GALVANIZED STEEL 4 METER POLE WITH FIXING STRUCTURE'],
            ['internal_cod' => '11.SUR-01-05', 'name' => 'TFT 17" COLOR MONITOR'],
            ['internal_cod' => '11.SUR-01-06', 'name' => 'CONTROL KEYBOARD AND MATRIX PARAMETRIZATION OF VIDEO'],
            ['internal_cod' => '11.SUR-01-07', 'name' => 'RACK OF 19" FOR EQUIPMENT'],
            ['internal_cod' => '11.SUR-01-08', 'name' => 'ELECTRICAL PANEL IN THE DIFFERENTIAL CONTROL CENTER '],
            ['internal_cod' => '11.SUR-01-09', 'name' => 'POWER SYSTEM UNINTERRUPTED, ON-LINE, PWP SERIES SINGLE PHASE INPUT OR THREE PHASE OF 10 kV, WITH 30 MINUTES AUTONOMY'],
            ['internal_cod' => '11.SUR-01-10', 'name' => 'INSIDE SIREN'],
            ['internal_cod' => '11.SUR-01-11', 'name' => 'OUTSIDE SIREN'],
            ['internal_cod' => '11.SUR-01-12', 'name' => 'PERIMETER PROTECTION SYSTEM BY INFRARED ANALYZER INCLUDING AREA MULTIPLEXERS AND SIGNAL REPEATERS'],
            ['internal_cod' => '11.SUR-01-13', 'name' => 'INSTALLATION & COMISSIONING'],
            ['internal_cod' => '11.SUR-02-01', 'name' => 'XX'],
            ['internal_cod' => '12.THP-01-01', 'name' => 'Integration EWEN Software'],

        ];

        // Recorre las categorías para crearlas e indexarlas por su prefix
        $categoryMap = collect($categories)->mapWithKeys(function ($category) {
            return [$category['prefix'] => [
                'id' => $category['id'],
                'prefix' => $category['prefix'],
            ]];
        });
        
        // Recorre los items para crearlos y vincularlos con su categoría
        foreach ($items as $item) {
            $internalCodParts = explode('-', $item['internal_cod']);
            $categoryPrefix = $internalCodParts[0].'-'.$internalCodParts[1];

            if (isset($categoryMap[$categoryPrefix])) {
                Item::create([
                    'internal_cod' => $item['internal_cod'],
                    'name' => ucwords(strtolower($item['name'])),
                    'item_categories_id' => $categoryMap[$categoryPrefix]['id'],
                ]);
            } else {
                // Puedes manejar casos donde el prefix no coincide con ninguna categoría
                // Aquí puedes agregar lógica adicional o simplemente omitir el item
                Item::create([
                    'internal_cod' => $item['internal_cod'],
                    'name' => ucwords(strtolower($item['name']))
                ]);
            }
        }
    }
}
