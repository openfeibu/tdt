<?php

return [

/*
 * Modules .
 */
    'modules'  => ['region'],


/*
 * Views for the page  .
 */
    'views'    => ['default' => 'Default', 'left' => 'Left menu', 'right' => 'Right menu'],

// Modale variables for page module.
    'region'     => [
        'model'        => 'App\Models\Region',
        'table'        => 'regions',
        'primaryKey'   => 'id',
        'hidden'       => [],
        'visible'      => [],
        'guarded'      => ['*'],
        'fillable'     => ['name', 'leader', 'tel','mobile','wechat','created_at','updated_at'],
        'translate'    => [],
        'upload_folder' => 'region',
        'encrypt'      => ['id'],
        'revision'     => ['name'],
        'perPage'      => '20',
        'search'        => [
        ],
    ],
    'region_area'     => [
        'model'        => 'App\Models\RegionArea',
        'table'        => 'region_areas',
        'primaryKey'   => 'id',
        'hidden'       => [],
        'visible'      => [],
        'guarded'      => ['*'],
        'fillable'     => ['region_id', 'area_code'],
        'translate'    => [],
        'upload_folder' => '',
        'encrypt'      => ['id'],
        'revision'     => ['name'],
        'perPage'      => '20',
        'search'        => [
        ],
    ],
];
