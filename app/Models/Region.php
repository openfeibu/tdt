<?php

namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\BaseModel;
use App\Traits\Database\Slugger;
use App\Traits\Filer\Filer;
use App\Traits\Hashids\Hashids;
use App\Traits\Trans\Translatable;

class Region extends BaseModel
{
    use Filer, Hashids, Slugger, Translatable, LogsActivity;

    /**
     * Configuartion for the model.
     *
     * @var array
     */
    protected $config = 'model.region.region';

    public static function updateShopCount($id)
    {
        $area_code_arr = app(RegionArea::class)->getRegionAreaCodes($id);
        $shop_count = Shop::whereIn('province_code',$area_code_arr)->count();
        self::where('id',$id)->update(['shop_count' => $shop_count]);
    }

}