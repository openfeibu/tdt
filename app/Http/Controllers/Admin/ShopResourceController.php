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
        $search_status = isset($search['status']) ? $search['status'] : '';
        $search_sn = isset($search['sn']) ? $search['sn'] : '';

        if($search_sn)
        {
            $id = ltrim(ltrim($search_sn,'t'),'0');
            $shops = $shops->where(function ($query) use ($id){
                return $query->where('id',$id);
            });
        }
        if($search_name)
        {
            $shops = $shops->where(function ($query) use ($search_name){
                return $query->where('name','like','%'.$search_name.'%')->orWhere('leader','like','%'.$search_name.'%');
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
        if($search_status)
        {
            $shops = $shops->where(function ($query) use ($search_status){
                return $query->where('status',$search_status);
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
            $attributes = $this->handleShopAttributes($attributes);
            $protection_shops = $this->repository->getRangeShop($attributes['longitude'],$attributes['latitude'],$attributes['protection_km']);
            if($protection_shops)
            {
                return $this->response->message("??????????????????????????????")
                    ->code(400)
                    ->data(['shops' => $protection_shops])
                    ->status('error')
                    ->url(guard_url('shop/create'))
                    ->redirect();
            }
            $shop = $this->repository->create($attributes);
            if(isset($attributes['signer']) && $attributes['signer'])
            {
                Signer::addSigner($attributes['signer']);
            }

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

	public function handleShopAttributes($attributes)
    {
        $map_data = $this->lbs_service->geocode_regeo($attributes['longitude'],$attributes['latitude']);
        $attributes = $this->getAttributesByRegeo($map_data,$attributes);

        return $attributes;
    }
    public function getAttributesByRegeo($map_data,$attributes)
    {
        $province_name = $map_data['result']['address_component']['province'] ;
        if(strstr($province_name,'??????') || strstr($province_name,'??????') || strstr($province_name,'??????'))
        {
            $province_code = hmt_code($province_name);
            $province = app('area_repository')->where('code',$province_code)->first();
			$province_code = $province->code;
			$province_name = $province->name;
            $city_name = $province_name;
            $city_code = $province->code;
            $towncode = '';
            $district_name = '';
            $adcode = '';
            $city_grade = 'hmt';
        }
        else{
            $adcode = $map_data['result']['ad_info']['adcode'];

            $district_name = $map_data['result']['address_component']['district'];
            $towncode = $map_data['result']['address_reference']['town']['id'] ?? '' ;

            $district = app('area_repository')->where('code',$adcode)->first();
            $city = app('area_repository')->where('code',$district->parent_code)->first();
            $city_name = $city->name;
            $city_code = $city->code;
            $province = app('area_repository')->where('code',$city->parent_code)->first();
			$province_code = $province->code;
			$province_name = $province->name;
			if($province->code == 100000)
			{
				$city_code = $district->code;
				$city_name = $district->name;
				$province_code = $city->code;
				$province_name = $city->name;
			}
            if($district->city_grade == 'county-city')
            {
                $city_grade = $district->city_grade;
            }else{
                $city_grade = $city->city_grade;
            }
        }
        $km = setting($city_grade);
        $km = $km ? $km : setting('default_km');

        $attributes['adcode'] = $adcode;
        $attributes['district_name'] = $district_name;
        $attributes['towncode'] = $towncode;
        $attributes['province_name'] = $province_name;
        $attributes['province_code'] = $province_code;
        $attributes['city_name'] = $city_name;
        $attributes['city_code'] = $city_code;
        $attributes['protection_km'] = $km;
        return $attributes;
    }
	public function getAttributesByGeo($map_data,$attributes)
    {
        $province_name = $map_data['result']['address_components']['province'] ;
        if(strstr($province_name,'??????') || strstr($province_name,'??????') || strstr($province_name,'??????'))
        {
            $province_code = hmt_code($province_name);
            $province = app('area_repository')->where('code',$province_code)->first();
            $city_name = $province_name;
            $city_code = $province->code;
            $towncode = '';
            $district_name = '';
            $adcode = '';
        }
        else{
            $adcode = $map_data['result']['ad_info']['adcode'];

            $district_name = $map_data['result']['address_components']['district'];
            $towncode = $map_data['result']['address_reference']['town']['id'] ?? '' ;

            $district = app('area_repository')->where('code',$adcode)->first();
            $city = app('area_repository')->where('code',$district->parent_code)->first();
            $city_name = $city->name;
            $city_code = $city->code;
            $province = app('area_repository')->where('code',$city->parent_code)->first();

        }
        $attributes['adcode'] = $adcode;
        $attributes['district_name'] = $district_name;
        $attributes['towncode'] = $towncode;
        $attributes['province_name'] = $province->name;
        $attributes['province_code'] = $province->code;
        $attributes['city_name'] = $city_name;
        $attributes['city_code'] = $city_code;
        return $attributes;
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
			
            $attributes = $this->handleShopAttributes($attributes);
			
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
        //ini_set("error_reporting","E_ALL & ~E_NOTICE");
        set_time_limit(0);
        $all_res = (new ShopImport())->toArray($request->file);
        
        $head_key_arr = ['management_region' => '????????????','name' => '??????' ,'leader' => '?????????','mobile' => '??????','inviter' => '?????????','first' => '??????','signer' => '??????','address' => '??????????????????????????????','cooperation_date' => '????????????','is_full' => '??????','status' => '??????','postscript' => '??????'];
		$all_sheet_count = count($all_res);
        $all_count = 0;
        $all_success_count = 0;
        $all_empty_count = 0;
		$error_message = "";
		$all_request_count = 0;
		$all_shop_attributes = [];
		$signers = [];
        for ($i = 0; $i <= $all_sheet_count-1; $i++)
        {
            $res = $all_res[$i];
            $count = 0;
            $success_count = 0;
            $empty_count = 0;
			$request_count = 0;
            $excel_data = [];
            foreach ( $res as $k => $v ) {
				$attributes = [];
                if(count(array_filter($v)) <= 1)
                {
                    continue;
                }
                if(!isset($keys) && ($k==0 || $k==1))
                {
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
                if($v[2] == '??????')
                {
                    continue;
                }
                $count++;
                // if($request_count >= 100)
                // {
                    // break;
                // }
				// $request_count++;

                foreach ($head_key_arr as $data_field => $data_value)
                {
                    if(in_array($data_field,['cooperation_date']) && !empty(trim($v[$keys[$data_field]])))
                    {
                        try{
                            $v[$keys[$data_field]] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($v[$keys[$data_field]]))->format('Y-m-d');
                        }catch (Exception $e)
                        {
                            $v[$keys[$data_field]] = '';
                        }
                    }
                    $attributes[$data_field] = $excel_data[$k][$data_field]  = isset($keys[$data_field]) && isset($v[$keys[$data_field]]) ? trim($v[$keys[$data_field]]) : '';
                }
                if($attributes['address'])
                {
                    $attributes['name'] = $attributes['name'] ? $attributes['name'] : '?????????';
                    $attributes['price'] = '';
                    if($attributes['is_full'] != "???" && !empty($attributes['is_full']))
                    {
                        $attributes['price'] = $attributes['is_full'];
                        $attributes['is_full'] = 0;
                    }else{
                        $attributes['is_full'] = 1;
                    }
                    $status_arr = trans('shop.status');

                    $attributes['status'] = array_search($attributes['status'],$status_arr);
					if(!$attributes['status'])
					{
						$error_message .= "\n???".($i+1)."????????????".($k+1)."??? ?????? ????????????;";
                        continue;
					}
                    $map_data = $this->lbs_service->geocode_geo($excel_data[$k]['address']);
                    if(isset($map_data['error']) && $map_data['error'])
                    {
                        $error_message .= "\n???".($i+1)."????????????".($k+1)."???????????????'".$excel_data[$k]['address']."',".$map_data['message'].";";
                        continue;
                    }
                    $location = $map_data['result']['location'];

                    $attributes['longitude'] = $location['lng'];
                    $attributes['latitude'] = $location['lat'];
					
                    $attributes = $this->handleShopAttributes($attributes);
                    unset($attributes['protection_km']);
                    $all_shop_attributes[] = $attributes;
					$signers[] = $attributes['signer'];
                    $success_count++;
                }else{
                    $empty_count++;
                    if($empty_count >=3)
                    {
                        break;
                    }
                }
                
				sleep(0.2);
            }

            $all_count = $all_count + $count;
            $all_empty_count = $all_empty_count + $empty_count;
            $all_success_count = $all_success_count + $success_count;
			$all_request_count = $all_request_count + $request_count;
        }
		 // var_dump($error_message);
		 // var_dump($all_shop_attributes);
		 // exit;
        Shop::insert($all_shop_attributes);
		Signer::addSigners($signers);
        $message = "?????????".$all_count."????????????????????????????????????????????????????????????".$all_success_count."???;";
        $message = $error_message ? $message.$error_message."(??????????????????????????????)" : $message;
        return $this->response->message($message)
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
        $ids = $request->input('ids',[]);
        $shops = $this->repository;
        if($status != 'all')
        {
            $shops = $shops->where('status',$status);
        }
        if($ids)
        {
            $shops = $shops->whereIn('id',$ids);
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
        return Excel::download(new DemoExport($export_data),"????????????".time().'.xlsx');


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
                ->code(0)
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
    public function checkValidShop(Request $request)
    {
        $attributes = $request->all();
        $this->lbs_service->debug = false;
        $attributes = $this->handleShopAttributes($attributes);
        //$attributes['protection_km'] = 1;
        $protection_shops = $this->repository->getRangeShop($attributes['longitude'],$attributes['latitude'],$attributes['protection_km']);
        if($protection_shops)
        {
            $message = "?????????????????????????????????";
            foreach ($protection_shops as $key => $protection_shop)
            {
                $message .= $protection_shop['name'].'???';
            }
            return $this->response->message($message)
                ->code(400)
                ->data(['shops' => $protection_shops])
                ->status('error')
                ->url(guard_url('shop/create'))
                ->redirect();
        }
        return $this->response->message("???????????????")
            ->code(0)
            ->status('success')
            ->url(guard_url('shop/'))
            ->redirect();
    }
}