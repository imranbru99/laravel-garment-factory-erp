<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('country')->default('Bangladesh');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('factories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->unsignedInteger('sewing_lines')->default(0);
            $table->unsignedInteger('daily_capacity_pcs')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['company_id', 'code']);
        });

        Schema::create('buyers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('country')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('payment_terms')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['company_id', 'code']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin')->index();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('factory_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('buyer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('locale', 5)->default('en');
            $table->string('phone')->nullable();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('type');
            $table->timestamps();
        });

        Schema::create('sewing_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code');
            $table->string('name');
            $table->unsignedInteger('operators_count')->default(20);
            $table->decimal('target_efficiency', 5, 2)->default(75);
            $table->unsignedInteger('hourly_target')->default(40);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['factory_id', 'code']);
        });

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('break_minutes')->default(60);
            $table->boolean('is_night')->default(false);
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sewing_line_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_code');
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('skill_grade')->nullable();
            $table->decimal('piece_rate', 10, 2)->default(0);
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->date('joining_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('nid')->nullable();
            $table->boolean('is_operator')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['factory_id', 'employee_code']);
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days', 5, 1);
            $table->string('status')->default('pending');
            $table->text('reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('styles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained()->cascadeOnDelete();
            $table->string('style_no');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('season')->nullable();
            $table->string('category');
            $table->string('fit')->nullable();
            $table->string('fabric_type')->nullable();
            $table->string('wash')->nullable();
            $table->unsignedSmallInteger('gsm')->nullable();
            $table->string('composition')->nullable();
            $table->decimal('smv', 8, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('status')->default('active');
            $table->json('ai_listing')->nullable();
            $table->timestamps();
            $table->unique(['buyer_id', 'style_no']);
        });

        Schema::create('tech_packs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete();
            $table->string('version');
            $table->string('status')->default('draft');
            $table->json('measurements')->nullable();
            $table->text('construction_notes')->nullable();
            $table->text('artwork_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('bom_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tech_pack_id')->constrained()->cascadeOnDelete();
            $table->string('item_type');
            $table->string('name');
            $table->string('spec')->nullable();
            $table->decimal('consumption', 12, 4)->default(0);
            $table->string('uom')->default('pcs');
            $table->decimal('unit_cost', 12, 4)->default(0);
            $table->foreignId('supplier_id')->nullable();
            $table->timestamps();
        });

        Schema::create('buyer_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete();
            $table->string('po_number');
            $table->string('season')->nullable();
            $table->unsignedInteger('order_qty')->default(0);
            $table->decimal('fob_price', 12, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->date('ex_factory_date')->nullable();
            $table->string('priority')->default('normal');
            $table->string('status')->default('draft');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['buyer_id', 'po_number']);
        });

        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('stage');
            $table->string('status')->default('pending');
            $table->date('requested_on')->nullable();
            $table->date('due_on')->nullable();
            $table->text('comments_summary')->nullable();
            $table->string('version')->default('1');
            $table->timestamps();
        });

        Schema::create('sample_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sample_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('author_name');
            $table->text('body');
            $table->boolean('is_buyer')->default(false);
            $table->timestamps();
        });

        Schema::create('order_matrix_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_order_id')->constrained()->cascadeOnDelete();
            $table->string('color');
            $table->string('size');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('cut_qty')->default(0);
            $table->unsignedInteger('sewn_qty')->default(0);
            $table->unsignedInteger('packed_qty')->default(0);
            $table->timestamps();
            $table->unique(['buyer_order_id', 'color', 'size']);
        });

        Schema::create('tna_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_order_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('department')->nullable();
            $table->date('planned_date')->nullable();
            $table->date('actual_date')->nullable();
            $table->string('status')->default('pending');
            $table->string('owner')->nullable();
            $table->timestamps();
        });

        Schema::create('costing_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete();
            $table->decimal('smv', 8, 2)->default(0);
            $table->decimal('cm_cost', 12, 4)->default(0);
            $table->decimal('fabric_cost', 12, 4)->default(0);
            $table->decimal('trim_cost', 12, 4)->default(0);
            $table->decimal('wash_cost', 12, 4)->default(0);
            $table->decimal('overhead', 12, 4)->default(0);
            $table->decimal('fob', 12, 4)->default(0);
            $table->decimal('margin_percent', 6, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('both');
            $table->string('contact')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->unsignedInteger('lead_time_days')->default(14);
            $table->unsignedTinyInteger('rating')->default(3);
            $table->timestamps();
        });

        Schema::table('bom_items', function (Blueprint $table) {
            $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('po_number');
            $table->string('type');
            $table->string('status')->default('draft');
            $table->date('order_date');
            $table->date('expected_date')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->decimal('total', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->string('color')->nullable();
            $table->string('spec')->nullable();
            $table->decimal('quantity', 12, 2);
            $table->string('uom')->default('m');
            $table->decimal('unit_price', 12, 4)->default(0);
            $table->decimal('received_qty', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->string('grn_number');
            $table->timestamp('received_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('qc_status')->default('pending');
            $table->text('notes')->nullable();
            $table->json('ocr_payload')->nullable();
            $table->timestamps();
        });

        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->decimal('quantity', 12, 2);
            $table->string('shade')->nullable();
            $table->string('lot')->nullable();
            $table->decimal('accepted_qty', 12, 2)->default(0);
            $table->decimal('rejected_qty', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('fabrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('composition')->nullable();
            $table->unsignedSmallInteger('gsm')->nullable();
            $table->decimal('width_inch', 6, 2)->nullable();
            $table->string('color')->nullable();
            $table->string('uom')->default('m');
            $table->timestamps();
        });

        Schema::create('fabric_rolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fabric_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('roll_no');
            $table->string('lot')->nullable();
            $table->string('shade')->nullable();
            $table->decimal('width_inch', 6, 2)->nullable();
            $table->unsignedSmallInteger('gsm')->nullable();
            $table->decimal('length_m', 10, 2);
            $table->decimal('remaining_m', 10, 2);
            $table->string('location')->nullable();
            $table->string('status')->default('available');
            $table->timestamps();
            $table->index(['buyer_order_id', 'shade']);
        });

        Schema::create('trims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('category');
            $table->string('spec')->nullable();
            $table->string('color')->nullable();
            $table->string('uom')->default('pcs');
            $table->decimal('stock_qty', 14, 2)->default(0);
            $table->decimal('reorder_level', 14, 2)->default(0);
            $table->decimal('unit_cost', 12, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->string('movable_type');
            $table->unsignedBigInteger('movable_id');
            $table->foreignId('buyer_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->decimal('quantity', 14, 2);
            $table->string('reference')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index(['movable_type', 'movable_id']);
        });

        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_order_id')->constrained()->cascadeOnDelete();
            $table->string('reservable_type');
            $table->unsignedBigInteger('reservable_id');
            $table->decimal('quantity', 14, 2);
            $table->string('status')->default('reserved');
            $table->timestamps();
        });

        Schema::create('markers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fabric_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->decimal('width_inch', 6, 2)->nullable();
            $table->json('sizes')->nullable();
            $table->decimal('efficiency', 5, 2)->default(0);
            $table->unsignedInteger('planned_plies')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        Schema::create('cut_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('marker_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_order_id')->constrained()->cascadeOnDelete();
            $table->date('plan_date');
            $table->string('lay_no');
            $table->unsignedInteger('plies')->default(0);
            $table->string('shade')->nullable();
            $table->string('lot')->nullable();
            $table->json('fabric_roll_ids')->nullable();
            $table->unsignedInteger('cut_qty')->default(0);
            $table->string('status')->default('planned');
            $table->timestamps();
        });

        Schema::create('bundles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cut_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_order_id')->constrained()->cascadeOnDelete();
            $table->string('bundle_no');
            $table->string('qr_code')->unique();
            $table->string('color');
            $table->string('size');
            $table->unsignedInteger('quantity');
            $table->string('shade')->nullable();
            $table->string('lot')->nullable();
            $table->string('current_section')->default('cutting');
            $table->string('status')->default('open');
            $table->timestamps();
        });

        Schema::create('bundle_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained()->cascadeOnDelete();
            $table->string('component');
            $table->unsignedInteger('quantity');
            $table->string('shade')->nullable();
            $table->timestamps();
        });

        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete();
            $table->string('section');
            $table->string('name');
            $table->unsignedSmallInteger('sequence')->default(1);
            $table->decimal('smv', 8, 3)->default(0);
            $table->string('machine_type')->nullable();
            $table->decimal('piece_rate', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('bundle_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('operation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sewing_line_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->string('direction');
            $table->string('section');
            $table->timestamp('scanned_at');
            $table->unsignedInteger('quantity');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('production_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sewing_line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->date('log_date');
            $table->unsignedTinyInteger('hour_slot')->nullable();
            $table->string('section');
            $table->unsignedInteger('target_qty')->default(0);
            $table->unsignedInteger('actual_qty')->default(0);
            $table->decimal('smv', 8, 2)->default(0);
            $table->decimal('efficiency', 6, 2)->default(0);
            $table->unsignedInteger('defect_qty')->default(0);
            $table->timestamps();
        });

        Schema::create('line_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sewing_line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_order_id')->constrained()->cascadeOnDelete();
            $table->date('plan_date');
            $table->unsignedInteger('planned_qty');
            $table->unsignedInteger('allocated_operators')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bundle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->unsignedInteger('inspected_qty');
            $table->unsignedInteger('passed_qty')->default(0);
            $table->unsignedInteger('rejected_qty')->default(0);
            $table->string('aql_level')->nullable();
            $table->string('result')->default('hold');
            $table->timestamp('inspected_at')->nullable();
            $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('defects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_order_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('category');
            $table->string('name');
            $table->unsignedInteger('quantity')->default(1);
            $table->string('severity')->default('major');
            $table->string('photo_path')->nullable();
            $table->string('ai_category')->nullable();
            $table->text('root_cause')->nullable();
            $table->timestamps();
            $table->index(['code', 'created_at']);
        });

        Schema::create('wash_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('process')->nullable();
            $table->json('chemicals')->nullable();
            $table->decimal('temperature_c', 5, 1)->nullable();
            $table->unsignedInteger('duration_min')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('wash_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wash_recipe_id')->nullable()->constrained()->nullOnDelete();
            $table->string('lot_no');
            $table->unsignedInteger('quantity');
            $table->string('status')->default('queued');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });

        Schema::create('finishing_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_order_id')->constrained()->cascadeOnDelete();
            $table->string('batch_no');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('pressed_qty')->default(0);
            $table->unsignedInteger('trimmed_qty')->default(0);
            $table->string('status')->default('queued');
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_order_id')->constrained()->cascadeOnDelete();
            $table->string('shipment_no');
            $table->string('mode')->default('sea');
            $table->string('container_no')->nullable();
            $table->string('vessel')->nullable();
            $table->date('etd')->nullable();
            $table->date('eta')->nullable();
            $table->string('status')->default('planned');
            $table->string('invoice_no')->nullable();
            $table->string('destination')->nullable();
            $table->timestamps();
        });

        Schema::create('cartons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('carton_no');
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->unsignedInteger('quantity');
            $table->decimal('gross_weight', 8, 2)->nullable();
            $table->decimal('net_weight', 8, 2)->nullable();
            $table->string('barcode')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
        });

        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sewing_line_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('brand')->nullable();
            $table->string('status')->default('running');
            $table->date('last_service_on')->nullable();
            $table->date('next_service_on')->nullable();
            $table->timestamps();
            $table->unique(['factory_id', 'code']);
        });

        Schema::create('breakdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->timestamp('reported_at');
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedInteger('minutes_down')->default(0);
            $table->text('reason')->nullable();
            $table->text('action_taken')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
        });

        Schema::create('factory_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->string('department')->nullable();
            $table->string('type');
            $table->string('severity')->default('medium');
            $table->string('title');
            $table->text('detail')->nullable();
            $table->string('fingerprint')->index();
            $table->string('status')->default('open');
            $table->json('suggested_actions')->nullable();
            $table->text('ai_recommendation')->nullable();
            $table->string('source')->default('detector');
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->timestamp('detected_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('issue_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_issue_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('owner_role')->nullable();
            $table->string('status')->default('open');
            $table->date('due_on')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('escalation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('issue_type');
            $table->string('severity')->nullable();
            $table->unsignedInteger('escalate_after_hours')->default(8);
            $table->string('notify_role');
            $table->boolean('auto_create_task')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('locale', 5)->default('en');
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('inquiry');
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('style_interest')->nullable();
            $table->text('message');
            $table->string('status')->default('new');
            $table->string('locale', 5)->default('en');
            $table->timestamps();
        });

        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('issuer')->nullable();
            $table->string('certificate_no')->nullable();
            $table->date('valid_until')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();
        });

        Schema::create('factory_holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('holiday_date');
            $table->string('name');
            $table->string('region')->default('BD');
            $table->timestamps();
        });

        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        Schema::create('payroll_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('present_days', 5, 1)->default(0);
            $table->decimal('overtime_hours', 6, 1)->default(0);
            $table->unsignedInteger('pieces')->default(0);
            $table->decimal('piece_amount', 12, 2)->default(0);
            $table->decimal('attendance_amount', 12, 2)->default(0);
            $table->decimal('gross', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('worker_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->timestamp('punched_at');
            $table->string('source')->default('manual');
            $table->string('direction');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('device_name')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->string('event');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('worker_attendance');
        Schema::dropIfExists('payroll_lines');
        Schema::dropIfExists('payroll_periods');
        Schema::dropIfExists('factory_holidays');
        Schema::dropIfExists('certifications');
        Schema::dropIfExists('inquiries');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('escalation_rules');
        Schema::dropIfExists('issue_actions');
        Schema::dropIfExists('factory_issues');
        Schema::dropIfExists('breakdowns');
        Schema::dropIfExists('machines');
        Schema::dropIfExists('cartons');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('finishing_batches');
        Schema::dropIfExists('wash_lots');
        Schema::dropIfExists('wash_recipes');
        Schema::dropIfExists('defects');
        Schema::dropIfExists('inspections');
        Schema::dropIfExists('line_plans');
        Schema::dropIfExists('production_logs');
        Schema::dropIfExists('bundle_scans');
        Schema::dropIfExists('operations');
        Schema::dropIfExists('bundle_components');
        Schema::dropIfExists('bundles');
        Schema::dropIfExists('cut_plans');
        Schema::dropIfExists('markers');
        Schema::dropIfExists('stock_reservations');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('trims');
        Schema::dropIfExists('fabric_rolls');
        Schema::dropIfExists('fabrics');
        Schema::dropIfExists('goods_receipt_items');
        Schema::dropIfExists('goods_receipts');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::table('bom_items', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
        });
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('costing_sheets');
        Schema::dropIfExists('tna_milestones');
        Schema::dropIfExists('order_matrix_lines');
        Schema::dropIfExists('sample_comments');
        Schema::dropIfExists('samples');
        Schema::dropIfExists('buyer_orders');
        Schema::dropIfExists('bom_items');
        Schema::dropIfExists('tech_packs');
        Schema::dropIfExists('styles');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('sewing_lines');
        Schema::dropIfExists('departments');
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('buyer_id');
            $table->dropConstrainedForeignId('factory_id');
            $table->dropConstrainedForeignId('company_id');
            $table->dropColumn(['role', 'locale', 'phone']);
        });
        Schema::dropIfExists('buyers');
        Schema::dropIfExists('factories');
        Schema::dropIfExists('companies');
    }
};
