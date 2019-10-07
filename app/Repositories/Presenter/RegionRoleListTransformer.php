<?php

namespace App\Repositories\Presenter;

use League\Fractal\TransformerAbstract;
use Hashids;

class RegionRoleListTransformer extends TransformerAbstract
{
    public function transform(\App\Models\RegionRole $role)
    {
        return [
            'id'                => $role->id,
            'name'              => $role->name,
            'slug'              => $role->slug,
            'description'       => $role->description,
            'level'             => $role->level,
            'created_at'        => format_date($role->created_at),
            'updated_at'        => format_date($role->updated_at),
        ];
    }
}