<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\OutputServerMessageException;
use App\Repositories\Eloquent\PageRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\Banner;
use App\Models\Setting;
use Log;
use App\Services\AmapService;
use App\Services\LBSService;

class HomeController extends BaseController
{
    public function __construct(PageRepositoryInterface $page)
    {
        parent::__construct();
        $this->repository = $page;
        $this->repository
            ->pushCriteria(\App\Repositories\Criteria\RequestCriteria::class)
            ->pushCriteria(\App\Repositories\Criteria\PageResourceCriteria::class);
    }

    public function getCoordinates(Request $request)
    {
        $address = $request->address;
        $lbs_service = new LBSService();
        $map_data = $lbs_service->geocode_geo($address);

        if(!isset($map_data['result']))
        {
            throw new OutputServerMessageException("请输入正确地址");
        }
        $location = $map_data['result']['location'];

        return $this->response->success()->data([
            'longitude' => $location['lng'],
            'latitude' => $location['lat'],
        ])->json();
    }
    /* 高德地图
    public function getCoordinates(Request $request)
    {
        $address = $request->address;
        $amap_service = new AmapService();
        $map_data = $amap_service->geocode_geo($address);
        if(!isset($map_data['geocodes'][0]))
        {
            throw new OutputServerMessageException("请输入正确地址");
        }
        $location = $map_data['geocodes'][0]['location'];
        $location_arr = explode(',',$location);
        return $this->response->success()->data([
            'longitude' => $location_arr[0],
            'latitude' => $location_arr[1],
        ])->json();
    }
    */
}
