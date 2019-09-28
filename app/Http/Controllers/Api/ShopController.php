<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Eloquent\ShopRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use App\Services\LBSService;

class ShopController extends BaseController
{
    public function __construct(ShopRepositoryInterface $shop)
    {
        parent::__construct();
        $this->repository = $shop;
    }
    public function getShops(Request $request)
    {
        $user = User::getUser();
        $longitude = $request->longitude;
        $latitude = $request->latitude;

        $lbs_service = new LBSService();
        $map_data = $lbs_service->geocode_regeo($longitude,$latitude);

        $adcode = $map_data['result']['ad_info']['adcode'];
        $district = app('area_repository')->where('code',$adcode)->first();
        var_dump($map_data);exit;
        $city = app('area_repository')->where('code',$district->parent_code)->first();


        if($district->city_grade == 'county-city')
        {

        }

        User::where('id',$user->id)->update([
            'longitude' => $longitude,
            'latitude' => $latitude,
        ]);

        $shops = $this->repository
            ->orderBy('id','desc')
            ->all();

        return $this->response->success()->data([
            'longitude' => $location['lng'],
            'latitude' => $location['lat'],
        ])->json();
    }
}