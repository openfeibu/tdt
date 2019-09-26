<?php

namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\BaseModel;
use App\Traits\Database\Slugger;
use App\Traits\Filer\Filer;
use App\Traits\Hashids\Hashids;
use App\Traits\Trans\Translatable;

class Shop extends BaseModel
{
    use Filer, Hashids, Slugger, Translatable, LogsActivity;

    /**
     * Configuartion for the model.
     *
     * @var array
     */
    protected $config = 'model.shop.shop';

    protected $appends = ['status_desc','is_full_desc'];       // 表里没有的字段

    public function getStatusDescAttribute()
    {
        return trans('shop.status.'.$this->attributes['status']);
    }
    public function getIsFullDescAttribute()
    {
        return $this->attributes['is_full'] ? "是" : $this->attributes['price'] ;
    }
}