<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Eloquent\RegionRoleRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class RegionRoleRepository extends BaseRepository implements RegionRoleRepositoryInterface
{


    public function boot()
    {
        $this->fieldSearchable = config('model.region_roles.region_role.model.search');
    }

    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return config('model.region_roles.region_role.model.model');
    }

    /**
     * Find a user by its key.
     *
     * @param type $key
     *
     * @return type
     */
    public function findRoleBySlug($key)
    {
        return $this->model->whereSlug($key)->first();
    }
}
