<?php

namespace App\Repositories\Transformers;

use League\Fractal\TransformerAbstract;

/**
 * Class PermissionTransformer
 * @package namespace App\Transformers;
 */
class RegionPermissionTransformer extends TransformerAbstract
{

    /**
     * Transform the \Permission entity
     * @param \App\Models\RegionPermission $permission
     *
     * @return array
     */
    public function transform(\App\Models\RegionPermission $permission)
    {
        return [
            'id'         => (int) $permission->id,
        ];
    }
}
