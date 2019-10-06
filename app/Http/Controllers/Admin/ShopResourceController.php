<?php
namespace App\Http\Controllers\Admin;

use App\Exports\DemoExport;
use App\Imports\ShopImport;
use Excel;
use App\Http\Controllers\Admin\ResourceController as BaseController;
use App\Models\Area;
use App\Models\Signer;
use Auth;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Services\AmapService;
use App\Services\LBSService;
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
        $this->lbs_service = new LBSService();
        $this->lbs_service->debug = true;
    }
    public function index(Request $request){
        return $this->shopList($request,"all");
    }

    public function normalShopList(Request $request)
    {
        return $this->shopList($request,"normal");
    }

    public function shopList(Request $request,$status)
    {
        $limit = $request->input('limit',config('app.limit'));
        $search = $request->input('search',[]);
        $signers = Signer::get()->toArray();
        if ($this->response->typeIs('json')) {
            $shops = $this->repository;
            if($status != 'all')
            {
                $shops = $shops->where('status',$status);
            }
            $shops = $this->search($shops,$search);

            $shops = $shops
                ->orderBy('id','desc')
                ->paginate($limit);

            return $this->response
                ->success()
                ->count($shops->total())
                ->data($shops->toArray()['data'])
                ->output();
        }
        $view = $status == 'all' ? 'index' : $status;
        return $this->response->title(trans('app.admin.panel'))
            ->view('shop.'.$view)
            ->data(compact('status','signers'))
            ->output();
    }
    private function search($shops,$search)
    {
        $search_name = isset($search['name']) ? $search['name'] : '';
        $search_inviter = isset($search['inviter']) ? $search['inviter'] : '';
        $search_signer = isset($search['signer']) ? $search['signer'] : '';
        $search_province_code = isset($search['province_code']) ? $search['province_code'] : '';

        if($search_name)
        {
            $shops = $shops->where(function ($query) use ($search_name){
                return $query->where('name','like','%'.$search_name.'%');
            });
        }
        if($search_signer)
        {
            $shops = $shops->where(function ($query) use ($search_signer){
                return $query->where('signer',$search_signer);
            });
        }
        if($search_province_code)
        {
            $shops = $shops->where(function ($query) use ($search_province_code){
                return $query->where('province_code',$search_province_code);
            });
        }

        if($search_inviter)
        {
            $shops = $shops->where(function ($query) use ($search_inviter){
                return $query->where('inviter','like','%'.$search_inviter.'%');
            });
        }
        return $shops;
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
            $this->lbs_service->debug = false;
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
        $map_data = $this->lbs_service->geocode_regeo($attributes['longitude'],$attributes['latitude']);

        $adcode = $map_data['result']['ad_info']['adcode'];
        $district_name = $map_data['result']['address_component']['district'];
        $towncode = $map_data['result']['address_reference']['town']['id'] ?? '' ;

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
        if(isset($attributes['signer']) && $attributes['signer'])
        {
            Signer::addSigner($attributes['signer']);
        }
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
            $this->lbs_service->debug = false;
            $map_data = $this->lbs_service->geocode_regeo($attributes['longitude'],$attributes['latitude']);

            $adcode = $map_data['result']['ad_info']['adcode'];
            $district_name = $map_data['result']['address_component']['district'];
            $towncode = $map_data['result']['address_reference']['town']['id'] ?? '' ;

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
            if(isset($attributes['singer']) && $attributes['singer'])
            {
                Singer::addSinger($attributes['singer']);
            }
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
        $res = (new ShopImport())->toArray($request->file)[0];
        $count = count($res) - 1;
        $success_count = 0;
        $empty_count = 0;
        $excel_data = [];
        $error_message = "";
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

                if(in_array($data_field,['cooperation_date','contract_date']))
                {
                    try{
                        $v[$keys[$data_field]] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($v[$keys[$data_field]])->format('Y-m-d');
                    }catch (Exception $e)
                    {
                        $v[$keys[$data_field]] = '';
                    }
                }
                $attributes[$data_field] = $excel_data[$k][$data_field]  = isset($keys[$data_field]) && isset($v[$keys[$data_field]]) ? trim($v[$keys[$data_field]]) : '';
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
                $status_arr = trans('shop.status');
                $attributes['status'] = array_search($attributes['status'],$status_arr);
                $map_data = $this->lbs_service->geocode_geo($excel_data[$k]['address']);
                if(isset($map_data['error']) && $map_data['error'])
                {
                    $error_message .= "\n第".$k."行地理位置无法识别;";
                    continue;
                }
                $location = $map_data['result']['location'];

                $attributes['longitude'] = $location['lng'];
                $attributes['latitude'] = $location['lat'];

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

        return $this->response->message("共发现".$count."条数据，排除空数据及重复数据后共成功上传".$success_count."条;".$error_message)
            ->status("success")
            ->url(guard_url('shop'))
            ->redirect();

    }
    public function export(Request $request)
    {
        $search = $request->input('search',[]);
        $handle_fields = $request->input('fields',[]);
        $export_fields = config('model.shop.shop.excel_fields');
        $status = $request->status ?? 'all';
        $shops = $this->repository;
        if($status != 'all')
        {
            $shops = $shops->where('status',$status);
        }
        $shops = $this->search($shops,$search);

        $shops = $shops
            ->orderBy('id','desc')
            ->get()->toArray();
        $export_data = [];
        $field_i = 0;
        foreach ($export_fields as $key => $field)
        {

            $export_data[0][$field_i] = trans('shop.label.'.$field);
            $field_i++;
        }
        $i = 1;
        foreach ($shops as $key => $shop)
        {
            foreach($export_fields as $key => $field)
            {
                $export_data[$i][$field_i] = $shop[$field];
                $field_i++;
            }
            $i++;
        }
        return Excel::download(new DemoExport($export_data),"门店列表".time().'.xlsx');


    }
    /**
     * @param Request $request
     * @param Shop $shop
     * @return mixed
     */
    public function destroy(Request $request, Shop $shop)
    {
        try {
            $shop->forceDelete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('shop.name')]))
                ->code(202)
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

    /**
     * @param Request $request
     * @return mixed
     */
    public function destroyAll(Request $request)
    {
        try {

            $data = $request->all();
            $ids = $data['ids'];

            $this->repository->forceDelete($ids);

            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('shop.name')]))
                ->status("success")
                ->code(202)
                ->url(guard_url('shop'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->status("error")
                ->code(400)
                ->url(guard_url('shop'))
                ->redirect();
        }
    }
}