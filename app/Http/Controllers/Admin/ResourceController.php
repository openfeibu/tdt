<?php

namespace App\Http\Controllers\Admin;

use Route;
use App\Models\Shop;
use App\Http\Controllers\Admin\Controller as BaseController;
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
        $shop_count = Shop::count();
        $normal_shop_count = Shop::where('status','normal')->count();
        $earnest_shop_count = Shop::where('status','earnest')->count();
        $cancel_shop_count = Shop::where('status','cancel')->count();
        return $this->response->title(trans('app.admin.panel'))
            ->view('home')
            ->data(compact('shop_count','normal_shop_count','earnest_shop_count','cancel_shop_count'))
            ->output();
    }
    public function dashboard()
    {
        return $this->response->title('测试')
            ->view('dashboard')
            ->output();
    }
}
