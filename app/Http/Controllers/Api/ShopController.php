<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\OutputServerMessageException;
use App\Repositories\Eloquent\ShopRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use App\Models\Shop;
use App\Services\LBSService;

class ShopController extends BaseController
{
    public function __construct(ShopRepositoryInterface $shop,LBSService $lbs_service)
    {
        parent::__construct();
        $this->repository = $shop;
        $this->lbs_service = $lbs_service;
    }
    public function getShops(Request $request)
    {
        $user = User::getUser();
        $longitude = $request->longitude;
        $latitude = $request->latitude;

        $map_data = $this->lbs_service->geocode_regeo($longitude,$latitude);

        $adcode = $map_data['result']['ad_info']['adcode'];
        $district = app('area_repository')->where('code',$adcode)->first();

        if(!$district)
        {
            throw new OutputServerMessageException("地址找不到，请联系管理员更新");
        }

        $city = app('area_repository')->where('code',$district->parent_code)->first();

        $city_grade = '';

        $site = $city->city_grade;
        if($district->city_grade == 'county-city')
        {
            $site = $district->city_grade;
            $city_grade = $district->city_grade;
        }else{
            $city_grade = $city->city_grade;
        }

        $km = setting($city_grade);
        $km = $km ? $km : setting('default_km');

        $shops = Shop::selectRaw("*,(acos(sin((".$latitude."*3.1415)/180) * sin((latitude*3.1415)/180) + cos((".$latitude."*3.1415)/180) * cos((latitude*3.1415)/180) * cos((".$longitude."*3.1415)/180 - (longitude*3.1415)/180))*6370.996) as distance")
            ->whereRaw("(acos(sin((".$latitude."*3.1415)/180) * sin((latitude*3.1415)/180) + cos((".$latitude."*3.1415)/180) * cos((latitude*3.1415)/180) * cos((".$longitude."*3.1415)/180 - (longitude*3.1415)/180))*6370.996) <= ".$km)
            ->get()
            ->toArray();

        foreach ($shops as $key => $shop)
        {
            if($shop['latitude'] == $latitude && $shop['longitude'] =  $longitude)
            {
                $shops[$key]['walking_distance'] = 0;
            }else{
                $direction = $this->lbs_service->direction('walking',$latitude,$longitude,$shop['latitude'],$shop['longitude']);

                $shops[$key]['walking_distance'] = $direction['result']['routes']['0']['distance'] ?? '未知';
            }

            $shops[$key]['distance'] = ceil($shop['distance'] * 1000);
        }

        User::where('id',$user->id)->update([
            'longitude' => $longitude,
            'latitude' => $latitude,
        ]);

        return $this->response->success()->data([
            'site' => $site,
            'km' => $km,
            'shops' => $shops
        ])->count(count($shops))->json();

    }
}