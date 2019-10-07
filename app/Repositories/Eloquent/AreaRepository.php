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
    public function getRegionProvinces($area_code_arr)
    {
        $provinces = $this->model->where('parent_code','100000')->whereIn('code',$area_code_arr)->orderBy('pinyin','asc')->get()->toArray();
        return $provinces;
    }
}