<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\ResourceController as BaseController;
use Auth,Validator;
use App\Models\RegionUser;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Requests\RegionUserRequest;
use App\Repositories\Eloquent\RegionPermissionRepositoryInterface;
use App\Repositories\Eloquent\RegionRoleRepositoryInterface;
use App\Repositories\Eloquent\RegionUserRepositoryInterface;

/**
 * Resource controller class for user.
 */
class RegionUserResourceController extends BaseController
{

    /**
     * @var Permissions
     */
    protected $permission;

    /**
     * @var roles
     */
    protected $roles;

    /**
     * Initialize region_user resource controller.
     *
     * @param type RegionUserRepositoryInterface $region_user
     * @param type RegionPermissionRepositoryInterface $permissions
     * @param type RegionRoleRepositoryInterface $roles
     */

    public function __construct(
        RegionUserRepositoryInterface $region_user,
        RegionPermissionRepositoryInterface $permissions,
        RegionRoleRepositoryInterface $roles
    )
    {
        parent::__construct();
        $this->permissions = $permissions;
        $this->roles = $roles;
        $this->repository = $region_user;
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
                ->setPresenter(\App\Repositories\Presenter\RegionUserPresenter::class);
            if(!empty($search_name))
            {
                $data = $data->where(function ($query) use ($search_name){
                    return $query->where('email','like','%'.$search_name.'%')->orWhere('phone','like','%'.$search_name.'%')->orWhere('name','like','%'.$search_name.'%');
                });
            }
            $data = $data->orderBy('id','desc')
                ->getDataTable($limit);

            return $this->response
                ->success()
                ->count($data['recordsTotal'])
                ->data($data['data'])
                ->output();
        }
        return $this->response->title(trans('app.admin.panel'))
            ->view('region_user.index')
            ->output();
    }

    public function show(Request $request,RegionUser $region_user)
    {
        if ($region_user->exists) {
            $view = 'region_user.show';
        } else {
            $view = 'region_user.new';
        }
        $roles = $this->roles->all();
        $regions = Region::orderBy('id','desc')->get();

        return $this->response->title(trans('app.view') . ' ' . trans('region_user.name'))
            ->data(compact('region_user','roles','regions'))
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
        $region_user = $this->repository->newInstance([]);
        $roles       = $this->roles->all();
        $regions = Region::orderBy('id','desc')->get();
        return $this->response->title(trans('app.new') . ' ' . trans('region_user.name'))
            ->view('region_user.create')
            ->data(compact('region_user', 'roles','regions'))
            ->output();
    }

    /**
     * Create new user.
     *
     * @param RegionUserRequest $request
     *
     * @return Response
     */
    public function store(RegionUserRequest $request)
    {
        try {
            $attributes              = $request->all();

            $roles          = $request->get('roles');
            $attributes['api_token'] = str_random(60);
            $region_user = $this->repository->create($attributes);
            $region_user->roles()->sync($roles);

            return $this->response->message(trans('messages.success.created', ['Module' => trans('region_user.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('region_user'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('region_user'))
                ->redirect();
        }

    }

    /**
     * Update the user.
     *
     * @param RegionUserRequest $request
     * @param RegionUser   $region_user
     *
     * @return Response
     */
    public function update(RegionUserRequest $request, RegionUser $region_user)
    {
        try {
            $attributes = $request->all();
            $roles          = $request->get('roles');
            $region_user->update($attributes);
            $region_user->roles()->sync($roles);
            return $this->response->message(trans('messages.success.updated', ['Module' => trans('region_user.name')]))
                ->code(0)
                ->status('success')
                ->url(guard_url('region_user/'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('region_user/' . $region_user->id))
                ->redirect();
        }
    }

    /**
     * @param Request $request
     * @param RegionUser $region_user
     * @return mixed
     */
    public function destroy(Request $request, RegionUser $region_user)
    {
        try {
            $region_user->forceDelete();

            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('region_user.name')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('region_user'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('region_user'))
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

            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('region_user.name')]))
                ->status("success")
                ->code(202)
                ->url(guard_url('region_user'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->status("error")
                ->code(400)
                ->url(guard_url('region_user'))
                ->redirect();
        }
    }
}