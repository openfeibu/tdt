<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\ResourceController as BaseController;
use App\Models\Area;
use Auth;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Services\AmapService;
use App\Repositories\Eloquent\ShopRepositoryInterface;

/**
 * Resource controller class for shop.
 */
class ShopResourceController extends BaseController
{

    /**
     * Initialize shop resource controller.
     *
     * @param type ShopRepositoryInterface $shop
     */

    public function __construct(
        ShopRepositoryInterface $shop
    )
    {
        parent::__construct();
        $this->repository = $shop;
        $this->repository
            ->pushCriteria(\App\Repositories\Criteria\RequestCriteria::class);
        $this->amap_service = new AmapService();
    }
    public function normalShopList(Request $request)
    {
        return $this->shopList($request,"normal");
    }

    public function shopList(Request $request,$status)
    {
        $limit = $request->input('limit',config('app.limit'));
        $search = $request->input('search',[]);
        $search_name = isset($search['name']) ? $search['name'] : '';
        $search_inviter = isset($search['inviter']) ? $search['inviter'] : '';
        if ($this->response->typeIs('json')) {
            $shops = $this->repository->where('status',$status);
            if($search_name)
            {
                $shops = $shops->where(function ($query) use ($search_name){
                    return $query->where('name','like','%'.$search_name.'%');
                });
            }
            if($search_inviter)
            {
                $shops = $shops->where(function ($query) use ($search_inviter){
                    return $query->where('name','like','%'.$search_inviter.'%');
                });
            }
            $shops = $shops
                ->orderBy('id','desc')
                ->paginate($limit);

            return $this->response
                ->success()
                ->count($shops->total())
                ->data($shops->toArray()['data'])
                ->output();
        }
        return $this->response->title(trans('app.admin.panel'))
            ->view('shop.'.$status)
            ->data(compact('status'))
            ->output();
    }
    public function index(Request $request){
        $limit = $request->input('limit',config('app.limit'));
        $search = $request->input('search',[]);
        $search_name = isset($search['name']) ? $search['name'] : '';

        if ($this->response->typeIs('json')) {
            $shops = $this->repository;

            if(!empty($search_name))
            {
                $shops = $shops->where(function ($query) use ($search_name){
                    return $query->where('name','like','%'.$search_name.'%');
                });
            }

            $shops = $shops->orderBy('id','desc')
                ->paginate($limit);

            return $this->response
                ->success()
                ->count($shops->total())
                ->data($shops->toArray()['data'])
                ->output();

        }
        return $this->response->title(trans('app.name'))
            ->view('shop.index')
            ->output();
    }
    public function create(Request $request)
    {
        $shop = $this->repository->newInstance([]);

        return $this->response->title(trans('app.new') . ' ' . trans('shop.name'))
            ->view('shop.create')
            ->data(compact('shop'))
            ->output();
    }
    public function store(Request $request)
    {
        try {
            $attributes = $request->all();

            $shop = $this->submitShop($attributes);

            return $this->response->message(trans('messages.success.created', ['Module' => trans('shop.name')]))
                ->code(0)
                ->status('success')
                ->url(guard_url('shop'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('shop'))
                ->redirect();
        }

    }
    public function submitShop($attributes)
    {
        $map_data = $this->amap_service->geocode_regeo($attributes['longitude'].','.$attributes['latitude']);

        $adcode = $map_data['regeocode']['addressComponent']['adcode'];
        $district_name = $map_data['regeocode']['addressComponent']['district'];
        $towncode = $map_data['regeocode']['addressComponent']['towncode'];

        $district = app('area_repository')->where('code',$adcode)->first();
        $city = app('area_repository')->where('code',$district->parent_code)->first();
        $city_name = $city->name;
        $city_code = $city->code;
        $province = app('area_repository')->where('code',$city->parent_code)->first();

        $attributes['adcode'] = $adcode;
        $attributes['district_name'] = $district_name;
        $attributes['towncode'] = $towncode;
        $attributes['province_name'] = $province->name;
        $attributes['province_code'] = $province->code;
        $attributes['city_name'] = $city_name;
        $attributes['city_code'] = $city_code;

        $shop = $this->repository->create($attributes);
        return $shop;
    }
    public function show(Request $request,Shop $shop)
    {
        if ($shop->exists) {
            $view = 'shop.show';
        } else {
            $view = 'shop.new';
        }
        return $this->response->title(trans('app.view') . ' ' . trans('shop.name'))
            ->data(compact('shop'))
            ->view($view)
            ->output();
    }
    public function update(Request $request,Shop $shop)
    {
        try {
            $attributes = $request->all();

            $map_data = $this->amap_service->geocode_regeo($attributes['longitude'].','.$attributes['latitude']);

            $adcode = $map_data['regeocode']['addressComponent']['adcode'];
            $district_name = $map_data['regeocode']['addressComponent']['district'];
            $towncode = $map_data['regeocode']['addressComponent']['towncode'];
            //$province_name = $map_data['regeocode']['addressComponent']['province'];

            $district = app('area_repository')->where('code',$adcode)->first();
            $city = app('area_repository')->where('code',$district->parent_code)->first();
            $city_name = $city->name;
            $city_code = $city->code;
            $province = app('area_repository')->where('code',$city->parent_code)->first();

            $attributes['adcode'] = $adcode;
            $attributes['district_name'] = $district_name;
            $attributes['towncode'] = $towncode;
            $attributes['province_name'] = $province->name;
            $attributes['province_code'] = $province->code;
            $attributes['city_name'] = $city_name;
            $attributes['city_code'] = $city_code;

            $shop->update($attributes);

            return $this->response->message(trans('messages.success.created', ['Module' => trans('shop.name')]))
                ->code(0)
                ->status('success')
                ->url(guard_url('shop/'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('shop/'))
                ->redirect();
        }
    }
    public function import(Request $request)
    {
        return $this->response->title(trans('shop.name'))
            ->view('shop.import')
            ->output();
    }
    public function submitImport(Request $request)
    {
        set_time_limit(0);
        $res = app('excel_service')->uploadExcel();
        $count = count($res);
        $success_count = 0;
        $empty_count = 0;
        $excel_data = [];

        foreach ( $res as $k => $v ) {
            if($k == 0)
            {
                $head_key_arr = ['name' => '店名' ,'leader' => '负责人','mobile' => '电话','inviter' => '邀约人','first' => '首次','signer' => '签单','address' => '销售区域（门店地址）','cooperation_date' => '合作时间','is_full' => '全款','status' => '备注','contract_date' => '合同签约'];
                $keys = [];
                foreach ($v as $head_k => $head_v)
                {
                    if(in_array(trim($head_v),array_values($head_key_arr)))
                    {
                        $keys[array_search(trim($head_v),$head_key_arr)] = $head_k;
                    }
                }
                continue;
            }

            foreach ($head_key_arr as $data_field => $data_value)
            {
                $attributes[$data_field] = $excel_data[$k][$data_field]  = isset($keys[$data_field]) && isset($v[$keys[$data_field]]) ? $v[$keys[$data_field]] : '';
            }
            if($attributes['address'])
            {
                $attributes['name'] = $attributes['name'] ? $attributes['name'] : '头道汤';
                if($attributes['is_full'] != "是")
                {
                    $attributes['price'] = $attributes['is_full'];
                    $attributes['is_full'] = 0;
                }else{
                    $attributes['is_full'] = 1;
                }
                $map_data = $this->amap_service->geocode_geo($excel_data[$k]['address']);
                $location = $map_data['geocodes'][0]['location'];
                $location_arr = explode(',',$location);
                $attributes['longitude'] = $location_arr[0];
                $attributes['latitude'] = $location_arr[1];
var_dump($attributes);exit;
                $this->submitShop($attributes);
                $success_count++;
            }else{
                $empty_count++;
                if($empty_count >=3)
                {
                    break;
                }
            }
        }

        return $this->response->message("共发现".$count."条数据，排除空数据后共成功上传".$success_count."条")
            ->status("success")
            ->code(200)
            ->url(guard_url('provider'))
            ->redirect();

    }

}