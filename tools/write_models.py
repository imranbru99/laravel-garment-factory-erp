#!/usr/bin/env python3
from pathlib import Path

ROOT = Path("/run/media/dev/A/project/github/Trouser-Factory/app/Models")
ROOT.mkdir(parents=True, exist_ok=True)

HEADER = """<?php

namespace App\\Models;

use App\\Models\\Concerns\\Auditable;
use App\\Models\\Concerns\\BelongsToFactory;
use App\\Models\\Concerns\\LimitsBuyerPortal;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;
use Illuminate\\Database\\Eloquent\\Relations\\HasMany;
use ImranDev\\UniversalSlug\\Attributes\\Slug;
"""

MODELS = {
"Company": '''class Company extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function factories(): HasMany { return $this->hasMany(Factory::class); }
    public function buyers(): HasMany { return $this->hasMany(Buyer::class); }
    public function suppliers(): HasMany { return $this->hasMany(Supplier::class); }
    public function certifications(): HasMany { return $this->hasMany(Certification::class); }
    public function styles(): HasMany { return $this->hasMany(Style::class); }
}''',
"Factory": '''class Factory extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function departments(): HasMany { return $this->hasMany(Department::class); }
    public function sewingLines(): HasMany { return $this->hasMany(SewingLine::class); }
    public function employees(): HasMany { return $this->hasMany(Employee::class); }
    public function orders(): HasMany { return $this->hasMany(BuyerOrder::class); }
    public function issues(): HasMany { return $this->hasMany(FactoryIssue::class); }
}''',
"Department": '''class Department extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    public function sewingLines(): HasMany { return $this->hasMany(SewingLine::class); }
    public function employees(): HasMany { return $this->hasMany(Employee::class); }
}''',
"SewingLine": '''class SewingLine extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'target_efficiency' => 'decimal:2'];
    }

    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
    public function employees(): HasMany { return $this->hasMany(Employee::class); }
    public function machines(): HasMany { return $this->hasMany(Machine::class); }
    public function productionLogs(): HasMany { return $this->hasMany(ProductionLog::class); }
    public function linePlans(): HasMany { return $this->hasMany(LinePlan::class); }
}''',
"Buyer": '''class Buyer extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function styles(): HasMany { return $this->hasMany(Style::class); }
    public function orders(): HasMany { return $this->hasMany(BuyerOrder::class); }
}''',
"Shift": '''class Shift extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_night' => 'boolean'];
    }

    public function employees(): HasMany { return $this->hasMany(Employee::class); }
}''',
"Employee": '''class Employee extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'is_operator' => 'boolean',
            'is_active' => 'boolean',
            'piece_rate' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
    public function sewingLine(): BelongsTo { return $this->belongsTo(SewingLine::class); }
    public function shift(): BelongsTo { return $this->belongsTo(Shift::class); }
    public function leaveRequests(): HasMany { return $this->hasMany(LeaveRequest::class); }
    public function attendance(): HasMany { return $this->hasMany(WorkerAttendance::class); }
    public function payrollLines(): HasMany { return $this->hasMany(PayrollLine::class); }
}''',
"LeaveRequest": '''class LeaveRequest extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date', 'days' => 'decimal:1'];
    }

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
}''',
"Style": '''#[Slug(from: 'name', to: 'slug')]
class Style extends Model
{
    use Auditable, LimitsBuyerPortal, \\ImranDev\\UniversalSlug\\Traits\\HasUniversalSlug;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['smv' => 'decimal:2', 'ai_listing' => 'array'];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function buyer(): BelongsTo { return $this->belongsTo(Buyer::class); }
    public function techPacks(): HasMany { return $this->hasMany(TechPack::class); }
    public function samples(): HasMany { return $this->hasMany(Sample::class); }
    public function operations(): HasMany { return $this->hasMany(Operation::class); }
    public function washRecipes(): HasMany { return $this->hasMany(WashRecipe::class); }
    public function orders(): HasMany { return $this->hasMany(BuyerOrder::class); }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}''',
"TechPack": '''class TechPack extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['measurements' => 'array', 'approved_at' => 'datetime'];
    }

    public function style(): BelongsTo { return $this->belongsTo(Style::class); }
    public function bomItems(): HasMany { return $this->hasMany(BomItem::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
}''',
"BomItem": '''class BomItem extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['consumption' => 'decimal:4', 'unit_cost' => 'decimal:4'];
    }

    public function techPack(): BelongsTo { return $this->belongsTo(TechPack::class); }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
}''',
"Sample": '''class Sample extends Model
{
    use Auditable, LimitsBuyerPortal;

    protected string $buyerPortalVia = 'style';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['requested_on' => 'date', 'due_on' => 'date'];
    }

    public function style(): BelongsTo { return $this->belongsTo(Style::class); }
    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function comments(): HasMany { return $this->hasMany(SampleComment::class); }
}''',
"SampleComment": '''class SampleComment extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_buyer' => 'boolean'];
    }

    public function sample(): BelongsTo { return $this->belongsTo(Sample::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}''',
"BuyerOrder": '''class BuyerOrder extends Model
{
    use Auditable, BelongsToFactory, LimitsBuyerPortal;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['ex_factory_date' => 'date', 'fob_price' => 'decimal:2'];
    }

    public function buyer(): BelongsTo { return $this->belongsTo(Buyer::class); }
    public function style(): BelongsTo { return $this->belongsTo(Style::class); }
    public function matrixLines(): HasMany { return $this->hasMany(OrderMatrixLine::class); }
    public function milestones(): HasMany { return $this->hasMany(TnaMilestone::class); }
    public function costingSheets(): HasMany { return $this->hasMany(CostingSheet::class); }
    public function bundles(): HasMany { return $this->hasMany(Bundle::class); }
    public function purchaseOrders(): HasMany { return $this->hasMany(PurchaseOrder::class); }
    public function inspections(): HasMany { return $this->hasMany(Inspection::class); }
    public function shipments(): HasMany { return $this->hasMany(Shipment::class); }

    public function refreshQuantity(): void
    {
        $this->order_qty = (int) $this->matrixLines()->sum('quantity');
        $this->save();
    }
}''',
"OrderMatrixLine": '''class OrderMatrixLine extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
}''',
"TnaMilestone": '''class TnaMilestone extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['planned_date' => 'date', 'actual_date' => 'date'];
    }

    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
}''',
"CostingSheet": '''class CostingSheet extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'smv' => 'decimal:2',
            'cm_cost' => 'decimal:4',
            'fabric_cost' => 'decimal:4',
            'trim_cost' => 'decimal:4',
            'wash_cost' => 'decimal:4',
            'overhead' => 'decimal:4',
            'fob' => 'decimal:4',
            'margin_percent' => 'decimal:2',
        ];
    }

    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function style(): BelongsTo { return $this->belongsTo(Style::class); }
}''',
"Supplier": '''class Supplier extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function purchaseOrders(): HasMany { return $this->hasMany(PurchaseOrder::class); }
}''',
"PurchaseOrder": '''class PurchaseOrder extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['order_date' => 'date', 'expected_date' => 'date', 'total' => 'decimal:2'];
    }

    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function items(): HasMany { return $this->hasMany(PurchaseOrderItem::class); }
    public function receipts(): HasMany { return $this->hasMany(GoodsReceipt::class); }
}''',
"PurchaseOrderItem": '''class PurchaseOrderItem extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['quantity' => 'decimal:2', 'unit_price' => 'decimal:4', 'received_qty' => 'decimal:2'];
    }

    public function purchaseOrder(): BelongsTo { return $this->belongsTo(PurchaseOrder::class); }
}''',
"GoodsReceipt": '''class GoodsReceipt extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['received_at' => 'datetime', 'ocr_payload' => 'array'];
    }

    public function purchaseOrder(): BelongsTo { return $this->belongsTo(PurchaseOrder::class); }
    public function receiver(): BelongsTo { return $this->belongsTo(User::class, 'received_by'); }
    public function items(): HasMany { return $this->hasMany(GoodsReceiptItem::class); }
}''',
"GoodsReceiptItem": '''class GoodsReceiptItem extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['quantity' => 'decimal:2', 'accepted_qty' => 'decimal:2', 'rejected_qty' => 'decimal:2'];
    }

    public function goodsReceipt(): BelongsTo { return $this->belongsTo(GoodsReceipt::class); }
    public function purchaseOrderItem(): BelongsTo { return $this->belongsTo(PurchaseOrderItem::class); }
}''',
"Fabric": '''class Fabric extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['width_inch' => 'decimal:2'];
    }

    public function rolls(): HasMany { return $this->hasMany(FabricRoll::class); }
}''',
"FabricRoll": '''class FabricRoll extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['width_inch' => 'decimal:2', 'length_m' => 'decimal:2', 'remaining_m' => 'decimal:2'];
    }

    public function fabric(): BelongsTo { return $this->belongsTo(Fabric::class); }
    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
}''',
"Trim": '''class Trim extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['stock_qty' => 'decimal:2', 'reorder_level' => 'decimal:2', 'unit_cost' => 'decimal:4'];
    }
}''',
"StockMovement": '''class StockMovement extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['quantity' => 'decimal:2'];
    }

    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}''',
"StockReservation": '''class StockReservation extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['quantity' => 'decimal:2'];
    }

    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
}''',
"Marker": '''class Marker extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['sizes' => 'array', 'width_inch' => 'decimal:2', 'efficiency' => 'decimal:2'];
    }

    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function style(): BelongsTo { return $this->belongsTo(Style::class); }
    public function fabric(): BelongsTo { return $this->belongsTo(Fabric::class); }
    public function cutPlans(): HasMany { return $this->hasMany(CutPlan::class); }
}''',
"CutPlan": '''class CutPlan extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['plan_date' => 'date', 'fabric_roll_ids' => 'array'];
    }

    public function marker(): BelongsTo { return $this->belongsTo(Marker::class); }
    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function bundles(): HasMany { return $this->hasMany(Bundle::class); }
}''',
"Bundle": '''class Bundle extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    public function cutPlan(): BelongsTo { return $this->belongsTo(CutPlan::class); }
    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function components(): HasMany { return $this->hasMany(BundleComponent::class); }
    public function scans(): HasMany { return $this->hasMany(BundleScan::class); }
}''',
"BundleComponent": '''class BundleComponent extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    public function bundle(): BelongsTo { return $this->belongsTo(Bundle::class); }
}''',
"Operation": '''class Operation extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['smv' => 'decimal:3', 'piece_rate' => 'decimal:2'];
    }

    public function style(): BelongsTo { return $this->belongsTo(Style::class); }
}''',
"BundleScan": '''class BundleScan extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['scanned_at' => 'datetime'];
    }

    public function bundle(): BelongsTo { return $this->belongsTo(Bundle::class); }
    public function operation(): BelongsTo { return $this->belongsTo(Operation::class); }
    public function sewingLine(): BelongsTo { return $this->belongsTo(SewingLine::class); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
}''',
"ProductionLog": '''class ProductionLog extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['log_date' => 'date', 'smv' => 'decimal:2', 'efficiency' => 'decimal:2'];
    }

    public function sewingLine(): BelongsTo { return $this->belongsTo(SewingLine::class); }
    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
}''',
"LinePlan": '''class LinePlan extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['plan_date' => 'date'];
    }

    public function sewingLine(): BelongsTo { return $this->belongsTo(SewingLine::class); }
    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
}''',
"Inspection": '''class Inspection extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['inspected_at' => 'datetime'];
    }

    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function bundle(): BelongsTo { return $this->belongsTo(Bundle::class); }
    public function inspector(): BelongsTo { return $this->belongsTo(User::class, 'inspector_id'); }
    public function defects(): HasMany { return $this->hasMany(Defect::class); }
}''',
"Defect": '''class Defect extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    public function inspection(): BelongsTo { return $this->belongsTo(Inspection::class); }
    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
}''',
"WashRecipe": '''class WashRecipe extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['chemicals' => 'array', 'temperature_c' => 'decimal:1'];
    }

    public function style(): BelongsTo { return $this->belongsTo(Style::class); }
    public function lots(): HasMany { return $this->hasMany(WashLot::class); }
}''',
"WashLot": '''class WashLot extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'finished_at' => 'datetime'];
    }

    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function washRecipe(): BelongsTo { return $this->belongsTo(WashRecipe::class); }
}''',
"FinishingBatch": '''class FinishingBatch extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['finished_at' => 'datetime'];
    }

    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
}''',
"Shipment": '''class Shipment extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['etd' => 'date', 'eta' => 'date'];
    }

    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function cartons(): HasMany { return $this->hasMany(Carton::class); }
}''',
"Carton": '''class Carton extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['gross_weight' => 'decimal:2', 'net_weight' => 'decimal:2'];
    }

    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function shipment(): BelongsTo { return $this->belongsTo(Shipment::class); }
}''',
"Machine": '''class Machine extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['last_service_on' => 'date', 'next_service_on' => 'date'];
    }

    public function sewingLine(): BelongsTo { return $this->belongsTo(SewingLine::class); }
    public function breakdowns(): HasMany { return $this->hasMany(Breakdown::class); }
}''',
"Breakdown": '''class Breakdown extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['reported_at' => 'datetime', 'resolved_at' => 'datetime'];
    }

    public function machine(): BelongsTo { return $this->belongsTo(Machine::class); }
}''',
"FactoryIssue": '''class FactoryIssue extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'suggested_actions' => 'array',
            'detected_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function actions(): HasMany { return $this->hasMany(IssueAction::class); }

    public function openActions(): void
    {
        foreach ($this->suggested_actions ?? [] as $action) {
            $this->actions()->create([
                'title' => $action['title'] ?? 'Corrective action',
                'owner_role' => $action['owner_role'] ?? null,
                'due_on' => $action['due_on'] ?? now()->addDay()->toDateString(),
                'notes' => $action['detail'] ?? null,
                'status' => 'open',
            ]);
        }

        $this->update(['status' => 'in_progress']);
    }
}''',
"IssueAction": '''class IssueAction extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['due_on' => 'date', 'completed_at' => 'datetime'];
    }

    public function factoryIssue(): BelongsTo { return $this->belongsTo(FactoryIssue::class); }
}''',
"EscalationRule": '''class EscalationRule extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['auto_create_task' => 'boolean', 'is_active' => 'boolean'];
    }
}''',
"Post": '''#[Slug(from: 'title', to: 'slug')]
class Post extends Model
{
    use Auditable, \\ImranDev\\UniversalSlug\\Traits\\HasUniversalSlug;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'is_published' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}''',
"Inquiry": '''class Inquiry extends Model
{
    use Auditable;

    protected $guarded = ['id'];
}''',
"Certification": '''class Certification extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['valid_until' => 'date'];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
}''',
"FactoryHoliday": '''class FactoryHoliday extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['holiday_date' => 'date'];
    }

    public function factory(): BelongsTo { return $this->belongsTo(Factory::class); }
}''',
"PayrollPeriod": '''class PayrollPeriod extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date'];
    }

    public function lines(): HasMany { return $this->hasMany(PayrollLine::class); }
}''',
"PayrollLine": '''class PayrollLine extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'present_days' => 'decimal:1',
            'overtime_hours' => 'decimal:1',
            'piece_amount' => 'decimal:2',
            'attendance_amount' => 'decimal:2',
            'gross' => 'decimal:2',
        ];
    }

    public function payrollPeriod(): BelongsTo { return $this->belongsTo(PayrollPeriod::class); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
}''',
"WorkerAttendance": '''class WorkerAttendance extends Model
{
    use Auditable, BelongsToFactory;

    protected $table = 'worker_attendance';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'punched_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
}''',
"AuditLog": '''class AuditLog extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['old_values' => 'array', 'new_values' => 'array', 'created_at' => 'datetime'];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}''',
}

for name, body in MODELS.items():
    (ROOT / f"{name}.php").write_text(HEADER + "\n" + body + "\n")
    print(name)

print("models", len(MODELS))
