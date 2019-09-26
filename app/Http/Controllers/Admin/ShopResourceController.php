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
 * Resource controller class for region.
 */
class ShopResourceController extends BaseController
{

    /**
     * Initialize region resource controller.
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

            $amap_service = new AmapService();

            $map_data = $amap_service->geocode_regeo($attributes['longitude'].','.$attributes['latitude']);

            $adcode = $map_data['regeocode']['addressComponent']['adcode'];
            $district_name = $map_data['regeocode']['addressComponent']['district'];
            $towncode = $map_data['regeocode']['addressComponent']['towncode'];
            $province_name = $map_data['regeocode']['addressComponent']['province'];

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

            return $this->response->message(trans('messages.success.created', ['Module' => trans('shop.name')]))
                ->code(0)
                ->status('success')
                ->url(guard_url('shop' ))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('shop'))
                ->redirect();
        }

    }
}