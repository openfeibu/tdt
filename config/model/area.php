<?php

return [

/*
 * Modules .
 */
    'modules'  => ['area'],


/*
 * Views for the page  .
 */
    'views'    => ['default' => 'Default', 'left' => 'Left menu', 'right' => 'Right menu'],

// Modale variables for page module.
    'area'     => [
        'model'        => 'App\Models\Area',
        'table'        => 'areas',
        'primaryKey'   => 'id',
        'hidden'       => [],
        'visible'      => [],
        'guarded'      => ['*'],
        'fillable'     => ['code', 'parent_code', 'remark_two'],
        'translate'    => [''],
        'upload_folder' => 'region',
        'encrypt'      => ['id'],
        'revision'     => [],
        'perPage'      => '20',
        'search'        => [
            'title'  => 'like',
        ],
    ],

];
