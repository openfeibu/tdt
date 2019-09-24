<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Eloquent\PageRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\Banner;
use App\Models\Setting;
use Log;

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
    public function index(Request $request)
    {
        $limit = $request->input('limit');
        $data = $this->repository
            ->setPresenter(\App\Repositories\Presenter\PageListPresenter::class)
            ->getDataTable($limit);

        return [
            'code' => '200',
            'data' => $data,
        ];
    }
    public function getBanners(Request $request)
    {
        $banners = Banner::orderBy('order','asc')->orderBy('id','asc')->get();
        foreach ($banners as $key => $val)
        {
            $banners[$key]['image'] = config('app.image_url').'/image/original'.$val['image'];
        }
        return response()->json([
            'code' => '200',
            'data' => $banners,
        ]);
    }
    public function getVideoVid(Request $request)
    {
        $video_vid = Setting::where('slug','video_vid')->value('value');
        return response()->json([
            'code' => '200',
            'data' => $video_vid,
        ]);
    }
    public function test(Request $request)
    {
        Log::info('Log message', ['context' => 'Other helpful information']);
    }
}
