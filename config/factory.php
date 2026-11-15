<?php

return [

    'name' => env('FACTORY_NAME', 'Padma Trouser Mills'),

    'audit' => env('FACTORY_AUDIT', true),

    'timezone' => env('FACTORY_TIMEZONE', 'Asia/Dhaka'),

    /*
    | Modules can be switched off without deleting their tables.
    | Filament resources and public pages honour this map.
    */
    'modules' => [
        'organization' => true,
        'styles' => true,
        'orders' => true,
        'procurement' => true,
        'inventory' => true,
        'planning' => true,
        'cutting' => true,
        'sewing' => true,
        'quality' => true,
        'washing' => true,
        'packing' => true,
        'shipment' => true,
        'maintenance' => true,
        'issues' => true,
        'hr' => true,
        'website' => true,
    ],

    'ai' => [
        'enabled' => env('FACTORY_AI', false),
    ],

    'efficiency' => [
        'low_threshold' => (float) env('FACTORY_LOW_EFFICIENCY', 70),
        'imbalance_ratio' => (float) env('FACTORY_IMBALANCE_RATIO', 1.25),
    ],

    'quality' => [
        'recurring_defect_count' => (int) env('FACTORY_RECURRING_DEFECTS', 3),
        'recurring_window_days' => 7,
    ],

    'roles' => [
        'admin' => 'Admin',
        'merchandiser' => 'Merchandiser',
        'production_manager' => 'Production Manager',
        'cutting_master' => 'Cutting Master',
        'qc' => 'QC',
        'store' => 'Store',
        'hr' => 'HR',
        'buyer' => 'Buyer',
    ],

];
