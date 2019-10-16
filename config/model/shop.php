<?php

return [

/*
 * Modules .
 */
    'modules'  => ['shop'],


/*
 * Views for the page  .
 */
    'views'    => ['default' => 'Default', 'left' => 'Left menu', 'right' => 'Right menu'],

// Modale variables for page module.
    'shop'     => [
        'model'        => 'App\Models\Shop',
        'table'        => 'shops',
        'primaryKey'   => 'id',
        'hidden'       => [],
        'visible'      => [],
        'guarded'      => ['*'],
        'fillable'     => ['name','image', 'leader','mobile','inviter','first','signer','address','cooperation_date','is_full','price','status','contract_date','longitude','latitude','management_region','province_code','province_name','city_code','city_name','district_name','adcode','content','postscript','towncode','created_at','updated_at'],
        'excel_fields' => ['sn','province_name', 'name','leader','mobile','inviter','first','signer','address','cooperation_date','is_full_desc','status_desc','postscript'],
        'translate'    => [],
        'upload_folder' => 'shop',
        'encrypt'      => ['id'],
        'revision'     => [],
        'perPage'      => '20',
        'search'        => [
        ],
        'status' => [ 'normal' ,'earnest','cancel','block']
    ],
];
