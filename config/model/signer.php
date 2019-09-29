<?php

return [

/*
 * Modules .
 */
    'modules'  => ['signer'],


/*
 * Views for the page  .
 */
    'views'    => ['default' => 'Default', 'left' => 'Left menu', 'right' => 'Right menu'],

// Modale variables for page module.
    'signer'     => [
        'model'        => 'App\Models\Signer',
        'table'        => 'signers',
        'primaryKey'   => 'id',
        'hidden'       => [],
        'visible'      => [],
        'guarded'      => ['*'],
        'fillable'     => ['name','created_at','updated_at'],
        'translate'    => [],
        'upload_folder' => 'shop',
        'encrypt'      => ['id'],
        'revision'     => [],
        'perPage'      => '20',
        'search'        => [
        ],
        'status' => [ 'normal' ,'earnest','cancel']
    ],
];
