<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\ResourceController as BaseController;
use App\Models\Area;
use App\Models\RegionRole;
use App\Models\RegionUser;
use Auth;
use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\RegionArea;
use App\Repositories\Eloquent\RegionRepositoryInterface;

/**
 * Resource controller class for region.
 */
class RegionResourceController extends BaseController
{

    /**
     * Initialize region resource controller.
     *
     * @param type RegionRepositoryInterface $region
     */

    public function __construct(
        RegionRepositoryInterface $region
    )
    {
        parent::__construct();
        $this->repository = $region;
        $this->repository
            ->pushCriteria(\App\Repositories\Criteria\RequestCriteria::class);
    }
    public function index(Request $request)
    {
        $limit = $request->input('limit',config('app.limit'));
        $search = $request->input('search',[]);
        $search_name = isset($search['search_name']) ? $search['search_name'] : '';

        if ($this->response->typeIs('json')) {
            $regions = $this->repository;
            if(!empty($search_name))
            {
                $regions = $regions->where(function ($query) use ($search_name){
                    return $query->Where('name','like','%'.$search_name.'%');
                });
            }
            $regions = $regions
                ->orderBy('id','desc')
                ->paginate($limit);

            foreach ($regions as $key => $region)
            {
                $region_area_codes = RegionArea::where('region_id',$region->id)->pluck('area_code')->toArray();
                $area_name_arr = Area::whereIn('code',$region_area_codes)->pluck('name')->toArray();
                $region->area_names = $area_name_arr ? implode('、',$area_name_arr) : '';
            }
            return $this->response
                ->success()
                ->count($regions->total())
                ->data($regions->toArray()['data'])
                ->output();
        }
        return $this->response->title(trans('app.admin.panel'))
            ->view('region.index')
            ->output();
    }

    public function show(Request $request,Region $region)
    {
        if ($region->exists) {
            $view = 'region.show';
        } else {
            $view = 'region.new';
        }
        $region_area_codes = RegionArea::where('region_id',$region->id)->pluck('area_code')->toArray();
        return $this->response->title(trans('app.view') . ' ' . trans('region.name'))
            ->data(compact('region','region_area_codes'))
            ->view($view)
            ->output();
    }

    /**
     * Show the form for creating a new region.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request)
    {

        $region = $this->repository->newInstance([]);

        return $this->response->title(trans('app.new') . ' ' . trans('region.name'))
            ->view('region.create')
            ->data(compact('region'))
            ->output();
    }

    /**
     * Create new region.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $attributes = $request->all();

            $area_codes = $attributes['area_code'];

            $region = $this->repository->create($attributes);

            $region_areas = [];
            foreach ($area_codes as $area_code)
            {
                $region_area = RegionArea::where('area_code',$area_code)->value('id');
//                if(!$region_area)
//                {
                    $region_areas[] = [
                        'area_code' => $area_code,
                        'region_id' => $region->id
                    ];
                //}

            }
            $region_areas ? RegionArea::insert($region_areas) : [];

            $phone = RegionUser::where('phone',$attributes['phone'])->value('phone');
            if(!$phone && $region)
            {
                $region_user = RegionUser::create([
                    'phone' => $attributes['phone'],
                    'name' => $attributes['leader'],
                    'region_id' => $region->id,
                    'password' => '123456'
                ]);
                $role_id = RegionRole::where('slug','superuser')->value('id');
                $region_user->roles()->sync([$role_id]);
            }
            Region::updateShopCount($region->id);
            return $this->response->message(trans('messages.success.created', ['Module' => trans('region.name')]))
                ->code(0)
                ->status('success')
                ->url(guard_url('region/' ))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('region/'))
                ->redirect();
        }

    }

    /**
     * Update the region.
     *
     * @param Request $request
     * @param Region   $region
     *
     * @return Response
     */
    public function update(Request $request, Region $region)
    {
        try {
            $attributes = $request->all();
            $region->update($attributes);
            $area_codes = isset($attributes['area_code']) ? $attributes['area_code'] : [];

            if($area_codes)
            {
                RegionArea::where('region_id',$region->id)->whereNotIn('area_code',$area_codes)->delete();

                $region_areas = [];
                foreach ($area_codes as $area_code)
                {
                    $region_area = RegionArea::where('region_id',$region->id)->where('area_code',$area_code)->value('id');
                    if(!$region_area)
                    {
                        $region_areas[] = [
                            'area_code' => $area_code,
                            'region_id' => $region->id
                        ];
                    }
                }
                $region_areas ? RegionArea::insert($region_areas) : [];
            }
            Region::updateShopCount($region->id);
            return $this->response->message(trans('messages.success.updated', ['Module' => trans('region.name')]))
                ->code(0)
                ->status('success')
                ->url(guard_url('region/'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('region/' . $region->id))
                ->redirect();
        }
    }

    /**
     * @param Request $request
     * @param Region $region
     * @return mixed
     */
    public function destroy(Request $request, Region $region)
    {
        try {
            $region->forceDelete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('region.name')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('region'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('region'))
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

            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('region.name')]))
                ->status("success")
                ->code(202)
                ->url(guard_url('region'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->status("error")
                ->code(400)
                ->url(guard_url('region'))
                ->redirect();
        }
    }


}