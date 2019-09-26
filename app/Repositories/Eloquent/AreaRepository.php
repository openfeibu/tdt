<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Eloquent\AreaRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class AreaRepository extends BaseRepository implements AreaRepositoryInterface
{
    public function model()
    {
        return config('model.area.area.model');
    }
    public function getProvinces()
    {
        $provinces = $this->model->where('parent_code','100000')->orderBy('pinyin','asc')->get()->toArray();
        return $provinces;
    }
}