<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Eloquent\RegionRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class RegionRepository extends BaseRepository implements RegionRepositoryInterface
{
    public function model()
    {
        return config('model.region.region.model');
    }
}