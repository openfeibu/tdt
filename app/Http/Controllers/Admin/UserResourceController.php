<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\ResourceController as BaseController;
use Auth;
use Illuminate\Http\Request;
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
            $users = $this->repository;
            if(!empty($search_name))
            {
                $data = $users->where(function ($query) use ($search_name){
                    return $query->where('email','like','%'.$search_name.'%')->orWhere('phone','like','%'.$search_name.'%')->orWhere('name','like','%'.$search_name.'%');
                });
            }
            $users = $users
                ->orderBy('id','desc')
                ->paginate($limit);

            return $this->response
                ->success()
                ->count($users->total())
                ->data($users->toArray()['data'])
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
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $attributes              = $request->all();
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

        foreach ( $res as $k => $v ) {
            if(trim($v['??????']))
            {
                $user = User::where('phone',trim($v['??????']))->first();
                if(!$user)
                {
                    $success_count++;
                    $excel_data[$k] = [
                        'name' => isset($v['??????']) ? trim($v['??????']) : '',
                        'phone' => isset($v['??????']) ? trim($v['??????']) : '',
                        'wechat' => isset($v['??????']) ? trim($v['??????']) : '',
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

        return $this->response->message("?????????".$count."?????????????????????????????????????????????????????????".$success_count."???")
            ->status("success")
            ->code(200)
            ->url(guard_url('user'))
            ->redirect();

    }
}