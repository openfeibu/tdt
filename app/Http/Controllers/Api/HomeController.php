<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\OutputServerMessageException;
use App\Repositories\Eloquent\PageRepositoryInterface;
use App\Services\LBSService;
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

}
