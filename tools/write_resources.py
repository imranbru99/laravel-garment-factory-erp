#!/usr/bin/env python3
from pathlib import Path

OUT = Path("/run/media/dev/A/project/github/Trouser-Factory/app/Filament/Resources")
OUT.mkdir(parents=True, exist_ok=True)

def f(name, typ="text", **kw):
    data = {"name": name, "type": typ}
    data.update(kw)
    return data

def rel(name, relation, title, **kw):
    return f(name, "relation", relation=relation, title=title, **kw)

def php(value, indent=0):
    pad = "    " * indent
    if isinstance(value, bool):
        return "true" if value else "false"
    if isinstance(value, int) and not isinstance(value, bool):
        return str(value)
    if isinstance(value, str):
        return "'" + value.replace("\\", "\\\\").replace("'", "\\'") + "'"
    if isinstance(value, list):
        if not value:
            return "[]"
        inner = ",\n".join(pad + "    " + php(v, indent + 1) for v in value)
        return "[\n" + inner + ",\n" + pad + "]"
    if isinstance(value, dict):
        if not value:
            return "[]"
        parts = [f"{pad}    {php(k)} => {php(v, indent + 1)}" for k, v in value.items()]
        return "[\n" + ",\n".join(parts) + ",\n" + pad + "]"
    raise TypeError(type(value))

RESOURCES = [
    dict(model="Company", group="Organization", icon="heroicon-o-building-office-2", module="organization", roles=["admin"], sort=1, fields=[
        f("name", required=True, search=True), f("code", required=True, search=True), f("country", default="Bangladesh"),
        f("phone"), f("email"), f("is_active", "toggle", default=True, filter=True), f("address", "textarea"),
    ]),
    dict(model="Factory", group="Organization", icon="heroicon-o-home-modern", module="organization", roles=["admin"], sort=2, fields=[
        rel("company_id", "company", "name", required=True, filter=True), f("name", required=True, search=True), f("code", required=True),
        f("city"), f("sewing_lines", "number"), f("daily_capacity_pcs", "number"), f("is_active", "toggle", default=True), f("address", "textarea"),
    ]),
    dict(model="Department", group="Organization", icon="heroicon-o-rectangle-group", module="organization", roles=["admin", "production_manager", "hr"], sort=3, fields=[
        rel("factory_id", "factory", "name", required=True, filter=True), f("name", required=True, search=True), f("code", required=True),
        f("type", "select", options={"merchandising": "Merchandising", "store": "Store", "cutting": "Cutting", "sewing": "Sewing", "washing": "Washing", "finishing": "Finishing", "packing": "Packing", "qc": "QC", "maintenance": "Maintenance", "hr": "HR"}, required=True, filter=True),
    ]),
    dict(model="SewingLine", group="Organization", icon="heroicon-o-queue-list", module="planning", roles=["production_manager", "cutting_master", "hr"], sort=4, fields=[
        rel("factory_id", "factory", "name", required=True, filter=True), rel("department_id", "department", "name"),
        f("code", required=True, search=True), f("name", required=True, search=True), f("operators_count", "number", default=20),
        f("target_efficiency", "decimal", default=75), f("hourly_target", "number", default=40), f("is_active", "toggle", default=True),
    ]),
    dict(model="Buyer", group="Merchandising", icon="heroicon-o-user-group", module="orders", roles=["merchandiser", "admin"], sort=1, fields=[
        rel("company_id", "company", "name", required=True, filter=True), f("name", required=True, search=True), f("code", required=True, search=True),
        f("country"), f("contact_name"), f("email"), f("phone"), f("currency", default="USD"), f("payment_terms"), f("is_active", "toggle", default=True),
    ]),
    dict(model="Style", group="Merchandising", icon="heroicon-o-swatch", module="styles", roles=["merchandiser", "buyer", "production_manager"], sort=2, fields=[
        rel("company_id", "company", "name", required=True), rel("buyer_id", "buyer", "name", required=True, filter=True),
        f("style_no", required=True, search=True, label="Style no"), f("name", required=True, search=True),
        f("season"), f("category", "select", options="style_categories", required=True, filter=True, badge=True),
        f("fit", "select", options="fits", filter=True), f("fabric_type", search=True, label="Fabric"), f("wash", filter=True),
        f("gsm", "number"), f("composition"), f("smv", "decimal", required=True), f("status", "select", options={"active": "Active", "development": "Development", "dropped": "Dropped"}, default="active", filter=True, badge=True),
        f("description", "textarea"), f("image_path", "file", label="Image"),
    ]),
    dict(model="TechPack", group="Merchandising", icon="heroicon-o-document-text", module="styles", roles=["merchandiser", "buyer", "production_manager"], sort=3, fields=[
        rel("style_id", "style", "name", required=True, filter=True), f("version", required=True),
        f("status", "select", options="approval", default="draft", filter=True, badge=True),
        f("measurements", "keyvalue", label="Measurements (point / cm)"), f("construction_notes", "textarea"), f("artwork_notes", "textarea"),
        f("approved_at", "datetime"),
    ]),
    dict(model="BomItem", group="Merchandising", icon="heroicon-o-list-bullet", module="styles", roles=["merchandiser", "store"], sort=4, fields=[
        rel("tech_pack_id", "techPack", "version", required=True, filter=True), f("item_type", "select", options={"fabric": "Fabric", "trim": "Trim"}, required=True, filter=True),
        f("name", required=True, search=True), f("spec"), f("consumption", "decimal"), f("uom", default="pcs"), f("unit_cost", "decimal"), rel("supplier_id", "supplier", "name"),
    ]),
    dict(model="Sample", group="Merchandising", icon="heroicon-o-beaker", module="styles", roles=["merchandiser", "buyer", "qc"], sort=5, fields=[
        rel("style_id", "style", "name", required=True, filter=True), rel("buyer_order_id", "buyerOrder", "po_number"),
        f("stage", "select", options="sample_stages", required=True, filter=True, badge=True),
        f("status", "select", options="approval", default="pending", filter=True, badge=True),
        f("version", default="1"), f("requested_on", "date"), f("due_on", "date"), f("comments_summary", "textarea"),
    ]),
    dict(model="SampleComment", group="Merchandising", icon="heroicon-o-chat-bubble-left-right", module="styles", roles=["merchandiser", "buyer", "qc"], sort=6, fields=[
        rel("sample_id", "sample", "stage", required=True, filter=True), f("author_name", required=True, search=True),
        f("is_buyer", "toggle", label="Buyer comment"), f("body", "textarea", required=True),
    ]),
    dict(model="BuyerOrder", group="Merchandising", icon="heroicon-o-clipboard-document-list", module="orders", roles=["merchandiser", "buyer", "production_manager", "cutting_master"], sort=7, fields=[
        rel("factory_id", "factory", "name", required=True, filter=True), rel("buyer_id", "buyer", "name", required=True, filter=True),
        rel("style_id", "style", "name", required=True, filter=True), f("po_number", required=True, search=True, label="PO number"),
        f("season"), f("order_qty", "number"), f("fob_price", "decimal", label="FOB"), f("currency", default="USD"),
        f("ex_factory_date", "date", label="Ex-factory"), f("priority", "select", options="priorities", default="normal", filter=True, badge=True),
        f("status", "select", options="order_statuses", default="draft", filter=True, badge=True), f("remarks", "textarea"),
    ]),
    dict(model="OrderMatrixLine", group="Merchandising", icon="heroicon-o-table-cells", module="orders", roles=["merchandiser", "buyer", "production_manager", "cutting_master"], sort=8, fields=[
        rel("buyer_order_id", "buyerOrder", "po_number", required=True, filter=True), f("color", required=True, search=True, filter=True),
        f("size", "select", options="sizes", required=True, filter=True), f("quantity", "number", required=True),
        f("cut_qty", "number"), f("sewn_qty", "number"), f("packed_qty", "number"),
    ]),
    dict(model="TnaMilestone", group="Merchandising", icon="heroicon-o-calendar-days", module="orders", roles=["merchandiser", "production_manager"], sort=9, fields=[
        rel("buyer_order_id", "buyerOrder", "po_number", required=True, filter=True), f("name", required=True, search=True),
        f("department"), f("planned_date", "date"), f("actual_date", "date"), f("status", "select", options={"pending": "Pending", "done": "Done", "delayed": "Delayed"}, default="pending", filter=True, badge=True), f("owner"),
    ]),
    dict(model="CostingSheet", group="Merchandising", icon="heroicon-o-calculator", module="orders", roles=["merchandiser"], sort=10, fields=[
        rel("buyer_order_id", "buyerOrder", "po_number", required=True, filter=True), rel("style_id", "style", "name", required=True),
        f("smv", "decimal"), f("cm_cost", "decimal", label="CM"), f("fabric_cost", "decimal"), f("trim_cost", "decimal"),
        f("wash_cost", "decimal"), f("overhead", "decimal"), f("fob", "decimal", label="FOB"), f("margin_percent", "decimal"), f("notes", "textarea"),
    ]),
    dict(model="Supplier", group="Materials", icon="heroicon-o-truck", module="procurement", roles=["store", "merchandiser"], sort=1, fields=[
        rel("company_id", "company", "name", required=True), f("name", required=True, search=True),
        f("type", "select", options={"fabric": "Fabric", "trim": "Trim", "both": "Both"}, default="both", filter=True),
        f("contact"), f("phone"), f("email"), f("lead_time_days", "number", default=14), f("rating", "number"), f("address", "textarea"),
    ]),
    dict(model="PurchaseOrder", group="Materials", icon="heroicon-o-document-currency-dollar", module="procurement", roles=["store", "merchandiser"], sort=2, fields=[
        rel("factory_id", "factory", "name", required=True, filter=True), rel("supplier_id", "supplier", "name", required=True, filter=True),
        rel("buyer_order_id", "buyerOrder", "po_number"), f("po_number", required=True, search=True),
        f("type", "select", options={"fabric": "Fabric", "trim": "Trim"}, required=True, filter=True),
        f("status", "select", options="po_statuses", default="draft", filter=True, badge=True),
        f("order_date", "date", required=True), f("expected_date", "date"), f("currency", default="USD"), f("total", "decimal"),
    ]),
    dict(model="PurchaseOrderItem", group="Materials", icon="heroicon-o-squares-2x2", module="procurement", roles=["store", "merchandiser"], sort=3, fields=[
        rel("purchase_order_id", "purchaseOrder", "po_number", required=True, filter=True), f("description", required=True, search=True),
        f("color"), f("spec"), f("quantity", "decimal", required=True), f("uom", default="m"), f("unit_price", "decimal"), f("received_qty", "decimal"),
    ]),
    dict(model="GoodsReceipt", group="Materials", icon="heroicon-o-archive-box-arrow-down", module="procurement", roles=["store", "qc"], sort=4, fields=[
        rel("purchase_order_id", "purchaseOrder", "po_number", required=True, filter=True), rel("factory_id", "factory", "name", required=True),
        f("grn_number", required=True, search=True, label="GRN"), f("received_at", "datetime"),
        f("qc_status", "select", options="qc_results", default="pending", filter=True, badge=True), f("notes", "textarea"),
    ]),
    dict(model="GoodsReceiptItem", group="Materials", icon="heroicon-o-inbox-stack", module="procurement", roles=["store", "qc"], sort=5, fields=[
        rel("goods_receipt_id", "goodsReceipt", "grn_number", required=True, filter=True), f("description", required=True, search=True),
        f("quantity", "decimal", required=True), f("shade"), f("lot"), f("accepted_qty", "decimal"), f("rejected_qty", "decimal"),
    ]),
    dict(model="Fabric", group="Materials", icon="heroicon-o-square-3-stack-3d", module="inventory", roles=["store", "cutting_master"], sort=6, fields=[
        rel("factory_id", "factory", "name", required=True, filter=True), f("name", required=True, search=True), f("composition"),
        f("gsm", "number"), f("width_inch", "decimal", label="Width (inch)"), f("color", search=True), f("uom", default="m"),
    ]),
    dict(model="FabricRoll", group="Materials", icon="heroicon-o-circle-stack", module="inventory", roles=["store", "cutting_master"], sort=7, fields=[
        rel("factory_id", "factory", "name", required=True), rel("fabric_id", "fabric", "name", required=True, filter=True),
        rel("buyer_order_id", "buyerOrder", "po_number", filter=True), f("roll_no", required=True, search=True), f("lot", search=True),
        f("shade", search=True, filter=True), f("width_inch", "decimal"), f("gsm", "number"), f("length_m", "decimal", required=True),
        f("remaining_m", "decimal", required=True), f("location"), f("status", "select", options={"available": "Available", "reserved": "Reserved", "issued": "Issued", "exhausted": "Exhausted", "rejected": "Rejected"}, default="available", filter=True, badge=True),
    ]),
    dict(model="Trim", group="Materials", icon="heroicon-o-tag", module="inventory", roles=["store"], sort=8, fields=[
        rel("factory_id", "factory", "name", required=True), f("name", required=True, search=True),
        f("category", "select", options="trim_categories", required=True, filter=True, badge=True), f("spec"), f("color"),
        f("uom", default="pcs"), f("stock_qty", "decimal"), f("reorder_level", "decimal"), f("unit_cost", "decimal"),
    ]),
    dict(model="StockMovement", group="Materials", icon="heroicon-o-arrows-right-left", module="inventory", roles=["store"], sort=9, fields=[
        rel("factory_id", "factory", "name", required=True), f("movable_type", required=True), f("movable_id", "number", required=True),
        rel("buyer_order_id", "buyerOrder", "po_number"), f("type", "select", options={"in": "In", "out": "Out", "reserve": "Reserve", "release": "Release", "adjust": "Adjust"}, required=True, filter=True, badge=True),
        f("quantity", "decimal", required=True), f("reference"), f("note", "textarea"),
    ]),
    dict(model="StockReservation", group="Materials", icon="heroicon-o-lock-closed", module="inventory", roles=["store", "merchandiser"], sort=10, fields=[
        rel("factory_id", "factory", "name", required=True), rel("buyer_order_id", "buyerOrder", "po_number", required=True, filter=True),
        f("reservable_type", required=True), f("reservable_id", "number", required=True), f("quantity", "decimal", required=True),
        f("status", "select", options={"reserved": "Reserved", "issued": "Issued", "released": "Released"}, default="reserved", filter=True),
    ]),
    dict(model="Marker", group="Cutting", icon="heroicon-o-map", module="cutting", roles=["cutting_master", "production_manager"], sort=1, fields=[
        rel("factory_id", "factory", "name", required=True), rel("buyer_order_id", "buyerOrder", "po_number", required=True, filter=True),
        rel("style_id", "style", "name", required=True), rel("fabric_id", "fabric", "name"), f("name", required=True, search=True),
        f("width_inch", "decimal"), f("efficiency", "decimal", label="Marker efficiency %"), f("planned_plies", "number"),
        f("status", "select", options={"draft": "Draft", "approved": "Approved", "used": "Used"}, default="draft", filter=True, badge=True),
        f("sizes", "keyvalue", label="Size ratio"),
    ]),
    dict(model="CutPlan", group="Cutting", icon="heroicon-o-scissors", module="cutting", roles=["cutting_master", "production_manager"], sort=2, fields=[
        rel("factory_id", "factory", "name", required=True), rel("marker_id", "marker", "name", required=True, filter=True),
        rel("buyer_order_id", "buyerOrder", "po_number", required=True, filter=True), f("plan_date", "date", required=True),
        f("lay_no", required=True, search=True, label="Lay no"), f("plies", "number"), f("shade"), f("lot"), f("cut_qty", "number"),
        f("status", "select", options={"planned": "Planned", "spreading": "Spreading", "cutting": "Cutting", "bundled": "Bundled", "closed": "Closed"}, default="planned", filter=True, badge=True),
    ]),
    dict(model="Bundle", group="Cutting", icon="heroicon-o-qr-code", module="cutting", roles=["cutting_master", "production_manager", "qc"], sort=3, fields=[
        rel("factory_id", "factory", "name", required=True), rel("cut_plan_id", "cutPlan", "lay_no", required=True, filter=True),
        rel("buyer_order_id", "buyerOrder", "po_number", required=True, filter=True), f("bundle_no", required=True, search=True),
        f("qr_code", required=True, search=True, label="QR / barcode"), f("color", required=True, filter=True), f("size", "select", options="sizes", required=True),
        f("quantity", "number", required=True), f("shade"), f("lot"),
        f("current_section", "select", options="sections", default="cutting", filter=True, badge=True),
        f("status", "select", options={"open": "Open", "in_process": "In process", "completed": "Completed", "hold": "Hold"}, default="open", filter=True, badge=True),
    ]),
    dict(model="BundleComponent", group="Cutting", icon="heroicon-o-puzzle-piece", module="cutting", roles=["cutting_master"], sort=4, fields=[
        rel("bundle_id", "bundle", "bundle_no", required=True, filter=True), f("component", "select", options="components", required=True, filter=True),
        f("quantity", "number", required=True), f("shade"),
    ]),
    dict(model="Operation", group="Sewing Floor", icon="heroicon-o-wrench-screwdriver", module="sewing", roles=["production_manager", "merchandiser"], sort=1, fields=[
        rel("style_id", "style", "name", required=True, filter=True), f("section", "select", options="sections", required=True, filter=True),
        f("name", required=True, search=True), f("sequence", "number", default=1), f("smv", "decimal"), f("machine_type"), f("piece_rate", "decimal"),
    ]),
    dict(model="BundleScan", group="Sewing Floor", icon="heroicon-o-arrow-path", module="sewing", roles=["production_manager", "cutting_master", "qc"], sort=2, fields=[
        rel("bundle_id", "bundle", "bundle_no", required=True, filter=True), rel("operation_id", "operation", "name"),
        rel("sewing_line_id", "sewingLine", "code", filter=True), rel("employee_id", "employee", "name"),
        f("direction", "select", options={"in": "In", "out": "Out"}, required=True, filter=True, badge=True),
        f("section", "select", options="sections", required=True, filter=True), f("scanned_at", "datetime", required=True), f("quantity", "number", required=True), f("note", "textarea"),
    ]),
    dict(model="ProductionLog", group="Sewing Floor", icon="heroicon-o-chart-bar", module="sewing", roles=["production_manager"], sort=3, fields=[
        rel("factory_id", "factory", "name", required=True), rel("sewing_line_id", "sewingLine", "code", required=True, filter=True),
        rel("buyer_order_id", "buyerOrder", "po_number", required=True, filter=True), rel("employee_id", "employee", "name"),
        f("log_date", "date", required=True), f("hour_slot", "number"), f("section", "select", options="sections", required=True, filter=True),
        f("target_qty", "number"), f("actual_qty", "number"), f("smv", "decimal"), f("efficiency", "decimal"), f("defect_qty", "number"),
    ]),
    dict(model="LinePlan", group="Sewing Floor", icon="heroicon-o-calendar", module="planning", roles=["production_manager"], sort=4, fields=[
        rel("factory_id", "factory", "name", required=True), rel("sewing_line_id", "sewingLine", "code", required=True, filter=True),
        rel("buyer_order_id", "buyerOrder", "po_number", required=True, filter=True), f("plan_date", "date", required=True),
        f("planned_qty", "number", required=True), f("allocated_operators", "number"), f("notes", "textarea"),
    ]),
    dict(model="Inspection", group="Quality", icon="heroicon-o-check-badge", module="quality", roles=["qc", "production_manager"], sort=1, fields=[
        rel("factory_id", "factory", "name", required=True), rel("buyer_order_id", "buyerOrder", "po_number", required=True, filter=True),
        rel("bundle_id", "bundle", "bundle_no"), f("type", "select", options="inspection_types", required=True, filter=True, badge=True),
        f("inspected_qty", "number", required=True), f("passed_qty", "number"), f("rejected_qty", "number"), f("aql_level", label="AQL"),
        f("result", "select", options={"pass": "Pass", "fail": "Fail", "hold": "Hold"}, default="hold", filter=True, badge=True),
        f("inspected_at", "datetime"), f("notes", "textarea"),
    ]),
    dict(model="Defect", group="Quality", icon="heroicon-o-exclamation-triangle", module="quality", roles=["qc", "production_manager"], sort=2, fields=[
        rel("inspection_id", "inspection", "type", required=True, filter=True), rel("buyer_order_id", "buyerOrder", "po_number", required=True, filter=True),
        f("code", required=True, search=True), f("name", required=True, search=True),
        f("category", "select", options="defect_categories", required=True, filter=True, badge=True),
        f("quantity", "number", default=1), f("severity", "select", options={"minor": "Minor", "major": "Major", "critical": "Critical"}, default="major", filter=True, badge=True),
        f("photo_path", "file", label="Photo"), f("ai_category"), f("root_cause", "textarea"),
    ]),
    dict(model="WashRecipe", group="Wash & Pack", icon="heroicon-o-beaker", module="washing", roles=["production_manager", "merchandiser"], sort=1, fields=[
        rel("style_id", "style", "name", required=True, filter=True), f("name", required=True, search=True), f("process"),
        f("temperature_c", "decimal", label="Temperature °C"), f("duration_min", "number"), f("chemicals", "keyvalue"), f("notes", "textarea"),
    ]),
    dict(model="WashLot", group="Wash & Pack", icon="heroicon-o-sparkles", module="washing", roles=["production_manager", "qc"], sort=2, fields=[
        rel("factory_id", "factory", "name", required=True), rel("buyer_order_id", "buyerOrder", "po_number", required=True, filter=True),
        rel("wash_recipe_id", "washRecipe", "name"), f("lot_no", required=True, search=True), f("quantity", "number", required=True),
        f("status", "select", options={"queued": "Queued", "washing": "Washing", "drying": "Drying", "done": "Done"}, default="queued", filter=True, badge=True),
        f("started_at", "datetime"), f("finished_at", "datetime"),
    ]),
    dict(model="FinishingBatch", group="Wash & Pack", icon="heroicon-o-fire", module="washing", roles=["production_manager", "qc"], sort=3, fields=[
        rel("factory_id", "factory", "name", required=True), rel("buyer_order_id", "buyerOrder", "po_number", required=True, filter=True),
        f("batch_no", required=True, search=True), f("quantity", "number", required=True), f("pressed_qty", "number"), f("trimmed_qty", "number"),
        f("status", "select", options={"queued": "Queued", "pressing": "Pressing", "trimming": "Trimming", "done": "Done"}, default="queued", filter=True, badge=True),
        f("finished_at", "datetime"),
    ]),
    dict(model="Carton", group="Wash & Pack", icon="heroicon-o-cube", module="packing", roles=["production_manager", "merchandiser"], sort=4, fields=[
        rel("factory_id", "factory", "name", required=True), rel("buyer_order_id", "buyerOrder", "po_number", required=True, filter=True),
        rel("shipment_id", "shipment", "shipment_no"), f("carton_no", required=True, search=True), f("color"), f("size"),
        f("quantity", "number", required=True), f("gross_weight", "decimal"), f("net_weight", "decimal"), f("barcode"), 
        f("status", "select", options={"open": "Open", "packed": "Packed", "loaded": "Loaded"}, default="open", filter=True, badge=True),
    ]),
    dict(model="Shipment", group="Wash & Pack", icon="heroicon-o-paper-airplane", module="shipment", roles=["merchandiser", "buyer", "production_manager"], sort=5, fields=[
        rel("factory_id", "factory", "name", required=True), rel("buyer_order_id", "buyerOrder", "po_number", required=True, filter=True),
        f("shipment_no", required=True, search=True), f("mode", "select", options={"sea": "Sea", "air": "Air", "road": "Road"}, default="sea", filter=True),
        f("container_no"), f("vessel"), f("etd", "date", label="ETD"), f("eta", "date", label="ETA"),
        f("status", "select", options={"planned": "Planned", "loaded": "Loaded", "in_transit": "In transit", "delivered": "Delivered"}, default="planned", filter=True, badge=True),
        f("invoice_no"), f("destination"),
    ]),
    dict(model="Machine", group="Maintenance", icon="heroicon-o-cog-6-tooth", module="maintenance", roles=["production_manager"], sort=1, fields=[
        rel("factory_id", "factory", "name", required=True), rel("sewing_line_id", "sewingLine", "code", filter=True),
        f("code", required=True, search=True), f("name", required=True, search=True), f("type"), f("brand"),
        f("status", "select", options="machine_statuses", default="running", filter=True, badge=True),
        f("last_service_on", "date"), f("next_service_on", "date"),
    ]),
    dict(model="Breakdown", group="Maintenance", icon="heroicon-o-bolt", module="maintenance", roles=["production_manager"], sort=2, fields=[
        rel("machine_id", "machine", "code", required=True, filter=True), rel("factory_id", "factory", "name", required=True),
        f("reported_at", "datetime", required=True), f("resolved_at", "datetime"), f("minutes_down", "number"),
        f("status", "select", options={"open": "Open", "resolved": "Resolved"}, default="open", filter=True, badge=True),
        f("reason", "textarea"), f("action_taken", "textarea"),
    ]),
    dict(model="FactoryIssue", group="Issues", icon="heroicon-o-bell-alert", module="issues", roles=["merchandiser", "production_manager", "cutting_master", "qc", "store", "hr"], sort=1, fields=[
        rel("factory_id", "factory", "name", required=True, filter=True), f("department"),
        f("type", "select", options="issue_types", required=True, filter=True, badge=True),
        f("severity", "select", options={"low": "Low", "medium": "Medium", "high": "High", "critical": "Critical"}, default="medium", filter=True, badge=True),
        f("title", required=True, search=True), f("status", "select", options="issue_statuses", default="open", filter=True, badge=True),
        f("fingerprint", helper="Leave blank for manual issues; detectors fill this."), f("source", default="manual"),
        f("detail", "textarea"), f("ai_recommendation", "textarea", label="AI recommendation"), f("detected_at", "datetime"), f("resolved_at", "datetime"),
    ]),
    dict(model="IssueAction", group="Issues", icon="heroicon-o-check-circle", module="issues", roles=["merchandiser", "production_manager", "cutting_master", "qc", "store", "hr"], sort=2, fields=[
        rel("factory_issue_id", "factoryIssue", "title", required=True, filter=True), f("title", required=True, search=True),
        f("owner_role", "select", options="roles", filter=True), f("status", "select", options={"open": "Open", "done": "Done"}, default="open", filter=True, badge=True),
        f("due_on", "date"), f("completed_at", "datetime"), f("notes", "textarea"),
    ]),
    dict(model="EscalationRule", group="Issues", icon="heroicon-o-arrow-up-circle", module="issues", roles=["admin", "production_manager"], sort=3, fields=[
        f("issue_type", "select", options="issue_types", required=True, filter=True), f("severity"),
        f("escalate_after_hours", "number", default=8), f("notify_role", "select", options="roles", required=True),
        f("auto_create_task", "toggle", default=True), f("is_active", "toggle", default=True),
    ]),
    dict(model="Employee", group="People", icon="heroicon-o-identification", module="hr", roles=["hr", "production_manager"], sort=1, fields=[
        rel("factory_id", "factory", "name", required=True, filter=True), rel("department_id", "department", "name"),
        rel("sewing_line_id", "sewingLine", "code", filter=True), rel("shift_id", "shift", "name"),
        f("employee_code", required=True, search=True), f("name", required=True, search=True), f("designation"), f("skill_grade"),
        f("piece_rate", "decimal"), f("hourly_rate", "decimal"), f("joining_date", "date"), f("phone"), f("nid", label="NID"),
        f("is_operator", "toggle", filter=True), f("is_active", "toggle", default=True),
    ]),
    dict(model="Shift", group="People", icon="heroicon-o-clock", module="hr", roles=["hr"], sort=2, fields=[
        rel("factory_id", "factory", "name", required=True, filter=True), f("name", required=True), f("start_time", "time", required=True),
        f("end_time", "time", required=True), f("break_minutes", "number", default=60), f("is_night", "toggle"),
    ]),
    dict(model="LeaveRequest", group="People", icon="heroicon-o-calendar-date-range", module="hr", roles=["hr"], sort=3, fields=[
        rel("employee_id", "employee", "name", required=True, filter=True), f("type", "select", options={"casual": "Casual", "sick": "Sick", "earned": "Earned", "unpaid": "Unpaid"}, required=True),
        f("start_date", "date", required=True), f("end_date", "date", required=True), f("days", "decimal", required=True),
        f("status", "select", options={"pending": "Pending", "approved": "Approved", "rejected": "Rejected"}, default="pending", filter=True, badge=True),
        f("reason", "textarea"),
    ]),
    dict(model="WorkerAttendance", group="People", icon="heroicon-o-finger-print", module="hr", roles=["hr", "production_manager"], sort=4, fields=[
        rel("factory_id", "factory", "name", required=True), rel("employee_id", "employee", "name", required=True, filter=True),
        f("punched_at", "datetime", required=True), f("direction", "select", options={"in": "In", "out": "Out"}, required=True, filter=True, badge=True),
        f("source", "select", options={"manual": "Manual", "biometric": "Biometric", "gps": "GPS", "qr": "QR"}, default="manual", filter=True),
        f("latitude", "decimal"), f("longitude", "decimal"), f("device_name"),
    ]),
    dict(model="PayrollPeriod", group="People", icon="heroicon-o-banknotes", module="hr", roles=["hr"], sort=5, fields=[
        rel("factory_id", "factory", "name", required=True, filter=True), f("name", required=True, search=True),
        f("start_date", "date", required=True), f("end_date", "date", required=True),
        f("status", "select", options={"draft": "Draft", "calculated": "Calculated", "approved": "Approved"}, default="draft", filter=True, badge=True),
    ]),
    dict(model="PayrollLine", group="People", icon="heroicon-o-receipt-percent", module="hr", roles=["hr"], sort=6, fields=[
        rel("payroll_period_id", "payrollPeriod", "name", required=True, filter=True), rel("employee_id", "employee", "name", required=True, filter=True),
        f("present_days", "decimal"), f("overtime_hours", "decimal"), f("pieces", "number"), f("piece_amount", "decimal"),
        f("attendance_amount", "decimal"), f("gross", "decimal"), f("notes", "textarea"),
    ]),
    dict(model="Post", group="Website", icon="heroicon-o-newspaper", module="website", roles=["merchandiser", "admin"], sort=1, fields=[
        f("title", required=True, search=True), f("locale", "select", options={"en": "English", "bn": "Bengali"}, default="en", filter=True),
        f("excerpt", "textarea"), f("body", "textarea", required=True), f("published_at", "datetime"), f("is_published", "toggle", filter=True),
    ]),
    dict(model="Inquiry", group="Website", icon="heroicon-o-envelope", module="website", roles=["merchandiser", "admin"], sort=2, fields=[
        f("type", "select", options={"inquiry": "Inquiry", "sample": "Sample request", "tour": "Factory tour"}, default="inquiry", filter=True, badge=True),
        f("name", required=True, search=True), f("company"), f("email", required=True), f("phone"), f("style_interest"),
        f("status", "select", options={"new": "New", "contacted": "Contacted", "closed": "Closed"}, default="new", filter=True, badge=True),
        f("locale"), f("message", "textarea", required=True),
    ]),
    dict(model="Certification", group="Website", icon="heroicon-o-shield-check", module="website", roles=["admin", "merchandiser"], sort=3, fields=[
        rel("company_id", "company", "name", required=True), f("name", required=True, search=True), f("issuer"), f("certificate_no"),
        f("valid_until", "date"), f("summary", "textarea"),
    ]),
    dict(model="FactoryHoliday", group="Organization", icon="heroicon-o-sun", module="hr", roles=["hr", "production_manager"], sort=7, fields=[
        rel("factory_id", "factory", "name"), f("holiday_date", "date", required=True), f("name", required=True, search=True), f("region", default="BD"),
    ]),
    dict(model="AuditLog", group="Organization", icon="heroicon-o-clipboard-document-check", module="organization", roles=["admin"], sort=9, readonly=True, fields=[
        f("auditable_type", search=True), f("auditable_id", "number"), f("event", filter=True, badge=True), f("ip"), f("created_at", "datetime"),
    ]),
]

TEMPLATE = r'''<?php

namespace App\Filament\Resources;

use App\Filament\Support\FieldBuilder;
use App\Filament\Support\GuardsModule;
use App\Models\{model};
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class {model}Resource extends Resource
{{
    use GuardsModule;

    protected static ?string $model = {model}::class;

    protected static ?string $navigationIcon = '{icon}';

    protected static ?string $navigationGroup = '{group}';

    protected static ?int $navigationSort = {sort};

    protected static ?string $navigationLabel = '{label}';

    public static function module(): string
    {{
        return '{module}';
    }}

    public static function roles(): array
    {{
        return {roles};
    }}

    public static function allowsChanges(): bool
    {{
        return {changes};
    }}

    public static function fields(): array
    {{
        return {fields};
    }}

    public static function form(Form $form): Form
    {{
        return $form->schema(FieldBuilder::form(static::fields()))->columns(2);
    }}

    public static function table(Table $table): Table
    {{
        return $table
            ->columns(FieldBuilder::columns(static::fields()))
            ->filters(FieldBuilder::filters(static::fields()))
            ->defaultSort('id', 'desc')
            ->actions({actions})
            ->bulkActions({bulk});
    }}

    public static function getPages(): array
    {{
        return [
            'index' => List{model}::route('/'),
            'create' => Create{model}::route('/create'),
            'edit' => Edit{model}::route('/{{record}}/edit'),
        ];
    }}
}}

class List{model} extends ListRecords
{{
    protected static string $resource = {model}Resource::class;

    protected function getHeaderActions(): array
    {{
        return {header};
    }}
}}

class Create{model} extends CreateRecord
{{
    protected static string $resource = {model}Resource::class;
}}

class Edit{model} extends EditRecord
{{
    protected static string $resource = {model}Resource::class;
}}
'''

for spec in RESOURCES:
    model = spec["model"]
    readonly = spec.get("readonly", False)
    label = spec.get("label", model)
    if model == "BuyerOrder":
        label = "Buyer POs"
    if model == "FactoryIssue":
        label = "Issue log"
    if model == "TnaMilestone":
        label = "TNA"
    if model == "WorkerAttendance":
        label = "Attendance"
    if model == "OrderMatrixLine":
        label = "Size / colour matrix"
    actions = "[]" if readonly else "[Tables\\Actions\\EditAction::make(), Tables\\Actions\\DeleteAction::make()]"
    bulk = "[]" if readonly else "[Tables\\Actions\\DeleteBulkAction::make()]"
    header = "[]" if readonly else "[\\Filament\\Actions\\CreateAction::make()]"
    php_text = TEMPLATE.format(
        model=model,
        icon=spec["icon"],
        group=spec["group"],
        sort=spec["sort"],
        label=label,
        module=spec["module"],
        roles=php(spec["roles"]),
        changes="false" if readonly else "true",
        fields=php(spec["fields"], 2),
        actions=actions,
        bulk=bulk,
        header=header,
    )
    (OUT / f"{model}Resource.php").write_text(php_text)
    print(model)

print("resources", len(RESOURCES))
