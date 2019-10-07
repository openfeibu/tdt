<?php
namespace App\Http\Controllers\Region;

use App\Http\Controllers\Region\ResourceController as BaseController;
use App\Models\Order;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\Eloquent\UserRepositoryInterface;

/**
 * Resource controller class for user.
 */
class UserResourceController extends BaseController
{

    /**
     * Initialize user resource controller.
     *
     * @param type UserRepositoryInterface $user
     */

    public function __construct(
        UserRepositoryInterface $user
    )
    {
        parent::__construct();
        $this->repository = $user;
        $this->repository
            ->pushCriteria(\App\Repositories\Criteria\RequestCriteria::class);
    }
    public function index(Request $request)
    {
        $limit = $request->input('limit',config('app.limit'));
        $search = $request->input('search',[]);
        $search_name = isset($search['search_name']) ? $search['search_name'] : '';

        if ($this->response->typeIs('json')) {
            $data = $this->repository
                ->where(['region_id' => Auth::user()->region_id]);
            if(!empty($search_name))
            {
                $data = $data->where(function ($query) use ($search_name){
                    return $query->where('email','like','%'.$search_name.'%')->orWhere('phone','like','%'.$search_name.'%')->orWhere('name','like','%'.$search_name.'%');
                });
            }
            $data = $data
                ->setPresenter(\App\Repositories\Presenter\UserPresenter::class)
                ->orderBy('id','desc')
                ->getDataTable($limit);
            return $this->response
                ->success()
                ->count($data['recordsTotal'])
                ->data($data['data'])
                ->output();
        }
        return $this->response->title(trans('app.admin.panel'))
            ->view('user.index')
            ->output();
    }

    public function show(Request $request,User $user)
    {
        if ($user->exists) {
            $view = 'user.show';
        } else {
            $view = 'user.new';
        }
        return $this->response->title(trans('app.view') . ' ' . trans('user.name'))
            ->data(compact('user'))
            ->view($view)
            ->output();
    }

    /**
     * Show the form for creating a new user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request)
    {

        $user = $this->repository->newInstance([]);

        return $this->response->title(trans('app.new') . ' ' . trans('user.name'))
            ->view('user.create')
            ->data(compact('user'))
            ->output();
    }

    /**
     * Create new user.
     *
     * @param UserRequest $request
     *
     * @return Response
     */
    public function store(UserRequest $request)
    {
        try {
            $attributes              = $request->all();
            $attributes['region_id'] = Auth::user()->region_id;

            $user = $this->repository->create($attributes);

            return $this->response->message(trans('messages.success.created', ['Module' => trans('user.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('user'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('user'))
                ->redirect();
        }

    }

    /**
     * Update the user.
     *
     * @param Request $request
     * @param User   $user
     *
     * @return Response
     */
    public function update(Request $request, User $user)
    {
        try {
            $attributes = $request->all();
            $user->update($attributes);
            return $this->response->message(trans('messages.success.updated', ['Module' => trans('user.name')]))
                ->code(0)
                ->status('success')
                ->url(guard_url('user/' . $user->id))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('user/' . $user->id))
                ->redirect();
        }
    }

    /**
     * @param Request $request
     * @param User $user
     * @return mixed
     */
    public function destroy(Request $request, User $user)
    {
        try {
            $order_count = Order::where('user_id',$user->id)->count();
            if($order_count)
            {
                return $this->response->message('该巡检员已分配巡检单，请勿删除或先重新分配该巡检员下的巡检单')
                    ->code(400)
                    ->status('success')
                    ->url(guard_url('user'))
                    ->redirect();
            }
            $user->forceDelete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user.name')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('user'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('user'))
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
            $order_count = Order::whereIn('user_id',$ids)->count();
            if($order_count)
            {
                return $this->response->message('巡检员已分配巡检单，请勿删除或先重新分配巡检员下的巡检单')
                    ->code(400)
                    ->status('success')
                    ->url(guard_url('user'))
                    ->redirect();
            }
            $this->repository->forceDelete($ids);

            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('user.name')]))
                ->status("success")
                ->code(202)
                ->url(guard_url('user'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->status("error")
                ->code(400)
                ->url(guard_url('user'))
                ->redirect();
        }
    }

    public function import(Request $request)
    {
        return $this->response->title(trans('user.name'))
            ->view('user.import')
            ->output();
    }

    public function submitImport(Request $request)
    {
        set_time_limit(0);
        $res = app('excel_service')->uploadExcel();

        $success_count = 0;
        $empty_count = 0;
        $count = count($res);
        $excel_data = [];
        $region_id = Auth::user()->region_id;

        foreach ( $res as $k => $v ) {
            if(trim($v['电话']))
            {
                $user = User::where('phone',trim($v['电话']))->first();
                if(!$user)
                {
                    $success_count++;
                    $excel_data[$k] = [
                        'name' => isset($v['姓名']) ? trim($v['姓名']) : '',
                        'phone' => isset($v['电话']) ? trim($v['电话']) : '',
                        'wechat' => isset($v['微信']) ? trim($v['微信']) : '',
                        'region_id' => $region_id,
                        'password' => '123456'
                    ];
                    $this->repository->create($excel_data[$k]);
                }
            }else{
                $empty_count++;
                if($empty_count >=3)
                {
                    break;
                }
            }
        }

        return $this->response->message("共发现".$count."条数据，排除空行及重复数据后共成功上传".$success_count."条")
            ->status("success")
            ->code(200)
            ->url(guard_url('user'))
            ->redirect();

    }
}