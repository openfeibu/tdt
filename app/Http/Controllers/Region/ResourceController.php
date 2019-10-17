<?php

namespace App\Http\Controllers\Region;

use App\Models\RegionArea;
use App\Models\Shop;
use App\Models\User;
use App\Models\Region;
use Route,Auth;
use App\Http\Controllers\Region\Controller as BaseController;
use App\Traits\AdminUser\AdminUserPages;
use App\Http\Response\ResourceResponse;
use App\Traits\Theme\ThemeAndViews;
use App\Traits\AdminUser\RoutesAndGuards;

class ResourceController extends BaseController
{
    use AdminUserPages,ThemeAndViews,RoutesAndGuards;

    public function __construct()
    {
        parent::__construct();
        if (!empty(app('auth')->getDefaultDriver())) {
            $this->middleware('auth:' . app('auth')->getDefaultDriver());
           // $this->middleware('role:' . $this->getGuardRoute());
            $this->middleware('permission:' .Route::currentRouteName());
            $this->middleware('active');
        }
      //
        //$this->region_id = Auth::user()->region_id;
        $this->response = app(ResourceResponse::class);
        $this->setTheme();
    }
    /**
     * Show dashboard for each user.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        $region_id = Auth::user()->region_id;
        $area_code_arr = app(RegionArea::class)->getRegionAreaCodes($region_id);
        $shop_count = Shop::whereIn('province_code',$area_code_arr)->count();

        $normal_shop_count = Shop::where('status','normal')->whereIn('province_code',$area_code_arr)->count();
        $earnest_shop_count = Shop::where('status','earnest')->whereIn('province_code',$area_code_arr)->count();
        $cancel_shop_count = Shop::where('status','cancel')->whereIn('province_code',$area_code_arr)->count();
        $block_shop_count = Shop::where('status','block')->whereIn('province_code',$area_code_arr)->count();
        $new_shop_count = Shop::where('cooperation_date','>=',date('Y-m-d',strtotime("-1 month")))->whereIn('province_code',$area_code_arr)->count();

        return $this->response->title(trans('app.admin.panel'))
            ->view('home')
            ->data(compact('shop_count','normal_shop_count','earnest_shop_count','cancel_shop_count','block_shop_count','new_shop_count'))
            ->output();
    }
    public function dashboard()
    {
        return $this->response->title('测试')
            ->view('dashboard')
            ->output();
    }
}
