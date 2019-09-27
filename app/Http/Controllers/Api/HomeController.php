<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Eloquent\PageRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\Banner;
use App\Models\Setting;
use Log;
use App\Services\AmapService;

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
        $amap_service = new AmapService();
        $map_data = $amap_service->geocode_geo($address);
        $location = $map_data['geocodes'][0]['location'];
        $location_arr = explode(',',$location);
        return $this->response->success()->data([
            'longitude' => $location_arr[0],
            'latitude' => $location_arr[1],
        ])->json();
    }

}
