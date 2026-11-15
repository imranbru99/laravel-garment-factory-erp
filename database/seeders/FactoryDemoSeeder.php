<?php

namespace Database\Seeders;

use App\Models\BomItem;
use App\Models\Breakdown;
use App\Models\Bundle;
use App\Models\BundleComponent;
use App\Models\BundleScan;
use App\Models\Buyer;
use App\Models\BuyerOrder;
use App\Models\Carton;
use App\Models\Certification;
use App\Models\Company;
use App\Models\CostingSheet;
use App\Models\CutPlan;
use App\Models\Defect;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EscalationRule;
use App\Models\Fabric;
use App\Models\FabricRoll;
use App\Models\Factory;
use App\Models\FactoryHoliday;
use App\Models\FinishingBatch;
use App\Models\GoodsReceipt;
use App\Models\Inspection;
use App\Models\LinePlan;
use App\Models\Machine;
use App\Models\Marker;
use App\Models\Operation;
use App\Models\OrderMatrixLine;
use App\Models\PayrollPeriod;
use App\Models\Post;
use App\Models\ProductionLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Sample;
use App\Models\SampleComment;
use App\Models\SewingLine;
use App\Models\Shift;
use App\Models\Shipment;
use App\Models\Style;
use App\Models\Supplier;
use App\Models\TechPack;
use App\Models\TnaMilestone;
use App\Models\Trim;
use App\Models\User;
use App\Models\WashLot;
use App\Models\WashRecipe;
use App\Services\Attendance\AttendanceBridge;
use App\Services\Catalog\StyleListingService;
use App\Services\Issues\IssueEngine;
use App\Services\Payroll\PayrollCalculator;
use App\Services\Production\EfficiencyCalculator;
use Illuminate\Database\Seeder;
use ImranDevBd\AttendanceHub\Facades\AttendanceHub;

class FactoryDemoSeeder extends Seeder
{
    public function run(): void
    {
        config(['factory.audit' => false]);

        $company = Company::query()->create([
            'name' => 'Padma Trouser Mills Ltd',
            'code' => 'PTM',
            'country' => 'Bangladesh',
            'address' => 'Konabari, Gazipur 1740',
            'phone' => '+8801700000000',
            'email' => 'desk@padmatrouser.test',
        ]);

        $gazipur = Factory::query()->create([
            'company_id' => $company->id,
            'name' => 'Gazipur Unit 1',
            'code' => 'GZP',
            'city' => 'Gazipur',
            'address' => 'Konabari industrial area',
            'sewing_lines' => 8,
            'daily_capacity_pcs' => 4800,
        ]);

        $ashulia = Factory::query()->create([
            'company_id' => $company->id,
            'name' => 'Ashulia Unit 2',
            'code' => 'ASH',
            'city' => 'Ashulia',
            'address' => 'Tongi-Ashulia road',
            'sewing_lines' => 4,
            'daily_capacity_pcs' => 2200,
        ]);

        $buyers = collect([
            ['Harrow & Co', 'HARROW', 'United Kingdom'],
            ['Nordform', 'NORDFORM', 'Germany'],
            ['Metro Basics', 'METRO', 'United States'],
        ])->map(fn ($row) => Buyer::query()->create([
            'company_id' => $company->id,
            'name' => $row[0],
            'code' => $row[1],
            'country' => $row[2],
            'currency' => 'USD',
            'payment_terms' => 'TT 30 days after BL',
            'contact_name' => $row[0].' merchandiser',
            'email' => strtolower($row[1]).'@buyer.test',
        ]));

        $password = 'password';
        $users = [
            ['Nabila Rahman', 'admin@trouser.test', 'admin', null, null],
            ['Farhan Kabir', 'merchandiser@trouser.test', 'merchandiser', $gazipur->id, null],
            ['Shirin Akter', 'production@trouser.test', 'production_manager', $gazipur->id, null],
            ['Jamal Uddin', 'cutting@trouser.test', 'cutting_master', $gazipur->id, null],
            ['Rokeya Sultana', 'qc@trouser.test', 'qc', $gazipur->id, null],
            ['Imran Hossain', 'store@trouser.test', 'store', $gazipur->id, null],
            ['Lamia Chowdhury', 'hr@trouser.test', 'hr', $gazipur->id, null],
            ['Helen Ward', 'buyer@trouser.test', 'buyer', null, $buyers[0]->id],
        ];

        foreach ($users as [$name, $email, $role, $factoryId, $buyerId]) {
            User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => $role,
                'company_id' => $company->id,
                'factory_id' => $factoryId,
                'buyer_id' => $buyerId,
                'locale' => $role === 'buyer' ? 'en' : 'en',
            ]);
        }

        $sewingDept = Department::query()->create(['factory_id' => $gazipur->id, 'name' => 'Sewing', 'code' => 'SEW', 'type' => 'sewing']);
        Department::query()->create(['factory_id' => $gazipur->id, 'name' => 'Cutting', 'code' => 'CUT', 'type' => 'cutting']);
        Department::query()->create(['factory_id' => $ashulia->id, 'name' => 'Sewing', 'code' => 'SEW', 'type' => 'sewing']);

        $shift = Shift::query()->create([
            'factory_id' => $gazipur->id,
            'name' => 'General',
            'start_time' => '08:00',
            'end_time' => '18:00',
            'break_minutes' => 60,
        ]);

        $lines = collect(['L1', 'L2', 'L3', 'L4'])->map(fn ($code, $index) => SewingLine::query()->create([
            'factory_id' => $gazipur->id,
            'department_id' => $sewingDept->id,
            'code' => $code,
            'name' => 'Line '.$code,
            'operators_count' => 24,
            'target_efficiency' => 75,
            'hourly_target' => 42 + $index,
        ]));

        $operator = Employee::query()->create([
            'factory_id' => $gazipur->id,
            'department_id' => $sewingDept->id,
            'sewing_line_id' => $lines[0]->id,
            'shift_id' => $shift->id,
            'employee_code' => '101',
            'name' => 'Karim Mia',
            'designation' => 'Front-fly operator',
            'skill_grade' => 'A',
            'piece_rate' => 2.5,
            'hourly_rate' => 80,
            'joining_date' => '2019-03-01',
            'is_operator' => true,
        ]);

        Employee::query()->create([
            'factory_id' => $gazipur->id,
            'department_id' => $sewingDept->id,
            'sewing_line_id' => $lines[1]->id,
            'shift_id' => $shift->id,
            'employee_code' => '118',
            'name' => 'Salma Begum',
            'designation' => 'Assembly operator',
            'skill_grade' => 'B',
            'piece_rate' => 2.2,
            'hourly_rate' => 75,
            'joining_date' => '2021-07-12',
            'is_operator' => true,
        ]);

        $styles = [
            ['PT-2401', 'Classic Pleated Formal', 'formal', 'regular', 'Poly-viscose', null, 210, '65% polyester 35% viscose', 18.4, $buyers[0]->id],
            ['PT-2408', 'Stretch Chino', 'chino', 'slim', 'Cotton stretch', 'enzyme', 240, '97% cotton 3% elastane', 22.1, $buyers[1]->id],
            ['PT-2512', 'Cargo Utility', 'cargo', 'relaxed', 'Cotton twill', null, 280, '100% cotton', 26.5, $buyers[2]->id],
            ['PT-2519', 'Selvedge Five-Pocket', 'jeans', 'straight', 'Cotton twill', 'rinse', 320, '100% cotton', 24.8, $buyers[0]->id],
        ];

        $styleModels = collect($styles)->map(function ($row) use ($company) {
            return Style::query()->create([
                'company_id' => $company->id,
                'buyer_id' => $row[9],
                'style_no' => $row[0],
                'name' => $row[1],
                'season' => 'SS26',
                'category' => $row[2],
                'fit' => $row[3],
                'fabric_type' => $row[4],
                'wash' => $row[5],
                'gsm' => $row[6],
                'composition' => $row[7],
                'smv' => $row[8],
                'description' => $row[1].' for a repeat program. Measurements live on the approved tech pack.',
                'status' => 'active',
            ]);
        });

        app(StyleListingService::class)->generate($styleModels[0]);

        $pack = TechPack::query()->create([
            'style_id' => $styleModels[0]->id,
            'version' => 'V3',
            'status' => 'approved',
            'measurements' => ['waist' => '82', 'hip' => '102', 'thigh' => '60', 'inseam' => '81', 'outseam' => '104', 'hem' => '38'],
            'construction_notes' => 'Fused waistband, French fly, two back darts, hook and bar.',
            'approved_at' => now()->subDays(20),
        ]);

        $supplier = Supplier::query()->create([
            'company_id' => $company->id,
            'name' => 'Meghna Weaving',
            'type' => 'fabric',
            'contact' => 'Stores',
            'lead_time_days' => 21,
            'phone' => '+8801711000000',
            'address' => 'Narayanganj',
        ]);

        BomItem::query()->create([
            'tech_pack_id' => $pack->id,
            'item_type' => 'fabric',
            'name' => 'Poly-viscose navy',
            'spec' => '58 inch, 210 GSM',
            'consumption' => 1.35,
            'uom' => 'm',
            'unit_cost' => 2.4,
            'supplier_id' => $supplier->id,
        ]);

        foreach ([['preparatory', 'Pocket bag', 1, 0.8], ['front', 'Fly attach', 2, 1.4], ['back', 'Seat seam', 3, 1.1], ['assembly', 'Side seam join', 4, 1.6]] as $op) {
            Operation::query()->create([
                'style_id' => $styleModels[0]->id,
                'section' => $op[0],
                'name' => $op[1],
                'sequence' => $op[2],
                'smv' => $op[3],
                'machine_type' => 'SNLS',
                'piece_rate' => 1.5,
            ]);
        }

        $order = BuyerOrder::query()->create([
            'factory_id' => $gazipur->id,
            'buyer_id' => $buyers[0]->id,
            'style_id' => $styleModels[0]->id,
            'po_number' => 'HAR-8841',
            'season' => 'SS26',
            'fob_price' => 8.75,
            'currency' => 'USD',
            'ex_factory_date' => now()->addDays(28),
            'priority' => 'high',
            'status' => 'in_production',
        ]);

        $chino = BuyerOrder::query()->create([
            'factory_id' => $gazipur->id,
            'buyer_id' => $buyers[1]->id,
            'style_id' => $styleModels[1]->id,
            'po_number' => 'NF-2208',
            'season' => 'SS26',
            'fob_price' => 9.4,
            'ex_factory_date' => now()->addDays(18),
            'priority' => 'urgent',
            'status' => 'in_production',
        ]);

        foreach ([['Navy', '32', 600], ['Navy', '34', 800], ['Charcoal', '32', 400], ['Charcoal', '34', 500]] as $cell) {
            OrderMatrixLine::query()->create([
                'buyer_order_id' => $order->id,
                'color' => $cell[0],
                'size' => $cell[1],
                'quantity' => $cell[2],
            ]);
        }
        foreach ([['Khaki', '32', 700], ['Khaki', '34', 900]] as $cell) {
            OrderMatrixLine::query()->create([
                'buyer_order_id' => $chino->id,
                'color' => $cell[0],
                'size' => $cell[1],
                'quantity' => $cell[2],
            ]);
        }
        $order->refreshQuantity();
        $chino->refreshQuantity();

        foreach (['Order confirm', 'Fabric in-house', 'Trim in-house', 'PP sample', 'Cutting', 'Sewing', 'Wash', 'Finishing', 'Ex-factory'] as $index => $name) {
            TnaMilestone::query()->create([
                'buyer_order_id' => $order->id,
                'name' => $name,
                'department' => $name,
                'planned_date' => now()->subDays(10)->addDays($index * 4),
                'actual_date' => $index < 3 ? now()->subDays(8)->addDays($index * 4) : null,
                'status' => $index < 3 ? 'done' : ($index === 3 ? 'delayed' : 'pending'),
                'owner' => 'Merchandising',
            ]);
        }

        CostingSheet::query()->create([
            'buyer_order_id' => $order->id,
            'style_id' => $styleModels[0]->id,
            'smv' => 18.4,
            'cm_cost' => 1.85,
            'fabric_cost' => 3.24,
            'trim_cost' => 0.62,
            'wash_cost' => 0,
            'overhead' => 0.4,
            'fob' => 8.75,
            'margin_percent' => 12,
            'notes' => 'CM based on 75% efficiency and a 10-hour shift.',
        ]);

        $sample = Sample::query()->create([
            'style_id' => $styleModels[0]->id,
            'buyer_order_id' => $order->id,
            'stage' => 'pp',
            'status' => 'approved',
            'requested_on' => now()->subDays(30),
            'due_on' => now()->subDays(18),
            'version' => '2',
            'comments_summary' => 'Waist ease accepted. Keep the charcoal shade against the approved band.',
        ]);
        SampleComment::query()->create([
            'sample_id' => $sample->id,
            'author_name' => 'Helen Ward',
            'body' => 'PP approved if charcoal stays on shade band B only. Do not mix with the navy lot.',
            'is_buyer' => true,
        ]);

        $fabric = Fabric::query()->create([
            'factory_id' => $gazipur->id,
            'name' => 'Poly-viscose navy',
            'composition' => '65/35',
            'gsm' => 210,
            'width_inch' => 58,
            'color' => 'Navy',
        ]);

        foreach ([['R-14', 'A', 'LOT-9', 120], ['R-15', 'B', 'LOT-9', 80], ['R-16', 'A', 'LOT-11', 140]] as $roll) {
            FabricRoll::query()->create([
                'factory_id' => $gazipur->id,
                'fabric_id' => $fabric->id,
                'buyer_order_id' => $order->id,
                'roll_no' => $roll[0],
                'shade' => $roll[1],
                'lot' => $roll[2],
                'width_inch' => 58,
                'gsm' => 210,
                'length_m' => $roll[3],
                'remaining_m' => $roll[3] - 20,
                'location' => 'Rack C',
                'status' => 'reserved',
            ]);
        }

        Trim::query()->create([
            'factory_id' => $gazipur->id,
            'name' => 'Horn button 20L',
            'category' => 'button',
            'spec' => '20L 4-hole',
            'color' => 'Brown',
            'stock_qty' => 800,
            'reorder_level' => 2000,
            'unit_cost' => 0.02,
        ]);

        $latePo = PurchaseOrder::query()->create([
            'factory_id' => $gazipur->id,
            'supplier_id' => $supplier->id,
            'buyer_order_id' => $chino->id,
            'po_number' => 'FAB-441',
            'type' => 'fabric',
            'status' => 'sent',
            'order_date' => now()->subDays(20),
            'expected_date' => now()->subDay(),
            'total' => 6400,
        ]);
        PurchaseOrderItem::query()->create([
            'purchase_order_id' => $latePo->id,
            'description' => 'Khaki cotton stretch',
            'color' => 'Khaki',
            'quantity' => 2400,
            'uom' => 'm',
            'unit_price' => 2.1,
        ]);

        GoodsReceipt::query()->create([
            'purchase_order_id' => $latePo->id,
            'factory_id' => $gazipur->id,
            'grn_number' => 'GRN-1008',
            'received_at' => now()->subDays(2),
            'qc_status' => 'conditional',
            'notes' => 'Partial roll delivery. Shade band not yet matched.',
        ]);

        $marker = Marker::query()->create([
            'factory_id' => $gazipur->id,
            'buyer_order_id' => $order->id,
            'style_id' => $styleModels[0]->id,
            'fabric_id' => $fabric->id,
            'name' => 'Navy 32/34',
            'width_inch' => 58,
            'sizes' => ['32' => 2, '34' => 2],
            'efficiency' => 84.5,
            'planned_plies' => 40,
            'status' => 'approved',
        ]);

        $cut = CutPlan::query()->create([
            'factory_id' => $gazipur->id,
            'marker_id' => $marker->id,
            'buyer_order_id' => $order->id,
            'plan_date' => today(),
            'lay_no' => 'LAY-17',
            'plies' => 40,
            'shade' => 'A',
            'lot' => 'LOT-9',
            'cut_qty' => 160,
            'status' => 'bundled',
        ]);

        $bundleA = Bundle::query()->create([
            'factory_id' => $gazipur->id,
            'cut_plan_id' => $cut->id,
            'buyer_order_id' => $order->id,
            'bundle_no' => 'B-1701',
            'qr_code' => 'BDL-1701',
            'color' => 'Navy',
            'size' => '32',
            'quantity' => 20,
            'shade' => 'A',
            'lot' => 'LOT-9',
            'current_section' => 'front',
            'status' => 'in_process',
        ]);
        Bundle::query()->create([
            'factory_id' => $gazipur->id,
            'cut_plan_id' => $cut->id,
            'buyer_order_id' => $order->id,
            'bundle_no' => 'B-1702',
            'qr_code' => 'BDL-1702',
            'color' => 'Navy',
            'size' => '32',
            'quantity' => 20,
            'shade' => 'B',
            'lot' => 'LOT-9',
            'current_section' => 'cutting',
            'status' => 'hold',
        ]);

        foreach (['front', 'back', 'waistband', 'pocket'] as $part) {
            BundleComponent::query()->create([
                'bundle_id' => $bundleA->id,
                'component' => $part,
                'quantity' => 20,
                'shade' => 'A',
            ]);
        }

        LinePlan::query()->create([
            'factory_id' => $gazipur->id,
            'sewing_line_id' => $lines[0]->id,
            'buyer_order_id' => $order->id,
            'plan_date' => today(),
            'planned_qty' => 420,
            'allocated_operators' => 24,
            'notes' => 'Navy formal only. Do not feed the chino.',
        ]);

        $stray = Bundle::query()->create([
            'factory_id' => $gazipur->id,
            'cut_plan_id' => $cut->id,
            'buyer_order_id' => $chino->id,
            'bundle_no' => 'B-2201',
            'qr_code' => 'BDL-2201',
            'color' => 'Khaki',
            'size' => '32',
            'quantity' => 15,
            'shade' => 'A',
            'lot' => 'LOT-K',
            'current_section' => 'front',
            'status' => 'in_process',
        ]);

        BundleScan::query()->create([
            'bundle_id' => $stray->id,
            'sewing_line_id' => $lines[0]->id,
            'employee_id' => $operator->id,
            'direction' => 'in',
            'section' => 'front',
            'scanned_at' => now(),
            'quantity' => 15,
            'note' => 'Scanned onto the formal line by mistake.',
        ]);

        $efficiency = app(EfficiencyCalculator::class);
        ProductionLog::query()->create([
            'factory_id' => $gazipur->id,
            'sewing_line_id' => $lines[0]->id,
            'buyer_order_id' => $order->id,
            'employee_id' => $operator->id,
            'log_date' => today(),
            'section' => 'front',
            'target_qty' => 120,
            'actual_qty' => 110,
            'smv' => 18.4,
            'efficiency' => $efficiency->percent(110, 18.4, 22, 90),
            'defect_qty' => 2,
        ]);
        ProductionLog::query()->create([
            'factory_id' => $gazipur->id,
            'sewing_line_id' => $lines[0]->id,
            'buyer_order_id' => $order->id,
            'log_date' => today(),
            'section' => 'back',
            'target_qty' => 120,
            'actual_qty' => 42,
            'smv' => 18.4,
            'efficiency' => $efficiency->percent(42, 18.4, 22, 90),
            'defect_qty' => 0,
        ]);

        $inspection = Inspection::query()->create([
            'factory_id' => $gazipur->id,
            'buyer_order_id' => $order->id,
            'bundle_id' => $bundleA->id,
            'type' => 'endline',
            'inspected_qty' => 40,
            'passed_qty' => 32,
            'rejected_qty' => 8,
            'aql_level' => '2.5',
            'result' => 'fail',
            'inspected_at' => now()->subHours(3),
            'notes' => 'Skip stitch repeating on the side seam.',
        ]);

        foreach (range(1, 4) as $ignored) {
            Defect::query()->create([
                'inspection_id' => $inspection->id,
                'buyer_order_id' => $order->id,
                'code' => 'CON-14',
                'category' => 'construction',
                'name' => 'Skip stitch',
                'quantity' => 2,
                'severity' => 'major',
            ]);
        }

        WashRecipe::query()->create([
            'style_id' => $styleModels[1]->id,
            'name' => 'Khaki enzyme',
            'process' => 'enzyme',
            'chemicals' => ['enzyme' => '1.2%', 'softener' => '2%'],
            'temperature_c' => 45,
            'duration_min' => 35,
        ]);
        $recipe = WashRecipe::query()->where('name', 'Khaki enzyme')->first();
        WashLot::query()->create([
            'factory_id' => $gazipur->id,
            'buyer_order_id' => $chino->id,
            'wash_recipe_id' => $recipe->id,
            'lot_no' => 'W-19',
            'quantity' => 200,
            'status' => 'queued',
        ]);

        FinishingBatch::query()->create([
            'factory_id' => $gazipur->id,
            'buyer_order_id' => $order->id,
            'batch_no' => 'FIN-3',
            'quantity' => 80,
            'pressed_qty' => 40,
            'trimmed_qty' => 20,
            'status' => 'pressing',
        ]);

        $shipment = Shipment::query()->create([
            'factory_id' => $gazipur->id,
            'buyer_order_id' => $order->id,
            'shipment_no' => 'SHP-310',
            'mode' => 'sea',
            'container_no' => 'MSCU1234567',
            'vessel' => 'MV Padma',
            'etd' => now()->addDays(30),
            'eta' => now()->addDays(48),
            'status' => 'planned',
            'invoice_no' => 'INV-310',
            'destination' => 'Felixstowe',
        ]);

        Carton::query()->create([
            'factory_id' => $gazipur->id,
            'buyer_order_id' => $order->id,
            'shipment_id' => $shipment->id,
            'carton_no' => 'C-001',
            'color' => 'Navy',
            'size' => '32',
            'quantity' => 20,
            'gross_weight' => 8.4,
            'net_weight' => 7.6,
            'barcode' => 'CTN310001',
            'status' => 'packed',
        ]);

        $machine = Machine::query()->create([
            'factory_id' => $gazipur->id,
            'sewing_line_id' => $lines[0]->id,
            'code' => 'SN-104',
            'name' => 'Single needle 104',
            'type' => 'SNLS',
            'brand' => 'Juki',
            'status' => 'breakdown',
            'last_service_on' => now()->subMonths(2),
            'next_service_on' => now()->subDays(3),
        ]);
        Breakdown::query()->create([
            'machine_id' => $machine->id,
            'factory_id' => $gazipur->id,
            'reported_at' => now()->subHours(2),
            'minutes_down' => 90,
            'reason' => 'Hook timing slipped. Line 1 front is waiting on this head.',
            'status' => 'open',
        ]);

        foreach ([
            ['shade_mismatch', 'high', 'cutting_master', 2],
            ['bundle_mixup', 'critical', 'production_manager', 1],
            ['line_imbalance', 'high', 'production_manager', 4],
            ['low_efficiency', 'medium', 'production_manager', 8],
            ['late_material', 'high', 'merchandiser', 4],
            ['recurring_defect', 'high', 'qc', 8],
            ['machine_downtime', 'high', 'production_manager', 2],
        ] as $rule) {
            EscalationRule::query()->create([
                'issue_type' => $rule[0],
                'severity' => $rule[1],
                'notify_role' => $rule[2],
                'escalate_after_hours' => $rule[3],
                'auto_create_task' => true,
            ]);
        }

        Certification::query()->create([
            'company_id' => $company->id,
            'name' => 'WRAP',
            'issuer' => 'WRAP',
            'certificate_no' => 'WRAP-88211',
            'valid_until' => now()->addYear(),
            'summary' => 'Gold certificate covering both Gazipur and Ashulia units.',
        ]);
        Certification::query()->create([
            'company_id' => $company->id,
            'name' => 'OEKO-TEX Standard 100',
            'issuer' => 'Hohenstein',
            'certificate_no' => 'OT-4490',
            'valid_until' => now()->addMonths(8),
            'summary' => 'Fabric and sewing thread for the formal and chino programs.',
        ]);

        foreach ([
            ['2026-02-21', 'International Mother Language Day'],
            ['2026-03-26', 'Independence Day'],
            ['2026-12-16', 'Victory Day'],
        ] as $holiday) {
            FactoryHoliday::query()->create([
                'factory_id' => null,
                'holiday_date' => $holiday[0],
                'name' => $holiday[1],
                'region' => 'BD',
            ]);
        }

        Post::query()->create([
            'title' => 'SS26 size set is on the table',
            'locale' => 'en',
            'excerpt' => 'Harrow formal and Nordform chino are through fit. PP comments are on the style.',
            'body' => "The Gazipur size set for PT-2401 closed with the waist ease the buyer asked for.\n\nChino PT-2408 is waiting on the late khaki lot before cutting can start. The issue desk will keep that PO visible until the rolls are in rack C.",
            'published_at' => now()->subDay(),
            'is_published' => true,
        ]);
        Post::query()->create([
            'title' => 'গাজীপুর লাইনে নতুন শিফট',
            'locale' => 'bn',
            'excerpt' => 'সাধারণ শিফট সকাল আটটা থেকে সন্ধ্যা ছয়টা।',
            'body' => "আশুলিয়া ইউনিট এখন চার লাইনে কার্গো চালাচ্ছে।\n\nউপস্থিতি বায়োমেট্রিক, জিপিএস বা কিউআর দিয়েও নেওয়া যাবে। পিস রেট আর হাজিরার হিসাব একই পেরোল শিটে বসে।",
            'published_at' => now()->subDays(2),
            'is_published' => true,
        ]);

        AttendanceHub::fake();
        app(AttendanceBridge::class)->safePull('zkteco', ['ip' => '10.0.0.50', 'port' => 4370]);

        $period = PayrollPeriod::query()->create([
            'factory_id' => $gazipur->id,
            'name' => now()->format('F Y'),
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
            'status' => 'draft',
        ]);
        app(PayrollCalculator::class)->build($period);
        $period->update(['status' => 'calculated']);

        app(IssueEngine::class)->scan();
    }
}
