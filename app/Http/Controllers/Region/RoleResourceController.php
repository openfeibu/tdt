<?php

namespace App\Http\Controllers\Region;

use App\Http\Controllers\Region\ResourceController as BaseController;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\Request;
use App\Repositories\Eloquent\RegionPermissionRepositoryInterface;
use App\Repositories\Eloquent\RegionRoleRepositoryInterface;
use App\Models\RegionRole;

/**
 * Resource controller class for role.
 */
class RoleResourceController extends BaseController
{

    /**
     * Initialize role resource controller.
     *
     * @param type RegionRoleRepositoryInterface $role
     * @param type RegionPermissionRepositoryInterface $permission
     *
     */
    public function __construct(
        RegionRoleRepositoryInterface       $role,
        RegionPermissionRepositoryInterface $permission
    ) {
        parent::__construct();
        $this->repository = $role;
        $this->permission = $permission;
        $this->repository
            ->pushCriteria(\App\Repositories\Criteria\RequestCriteria::class);
    }

    /**
     * Display a list of role.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit',config('app.limit'));

        if ($this->response->typeIs('json')) {

            $data = $this->repository
                ->orderBy('id','asc')
                ->setPresenter(\App\Repositories\Presenter\RegionRoleListPresenter::class)
                ->getDataTable($limit);
            return $this->response
                ->success()
                ->count($data['recordsTotal'])
                ->data($data['data'])
                ->output();
        }

        return $this->response->title(trans('role.names'))
            ->view('role.index', true)
            ->output();
    }

    /**
     * Display role.
     *
     * @param Request $request
     * @param RegionRole   $role
     *
     * @return Response
     */
    public function show(Request $request, RegionRole $role)
    {
        if ($role->exists) {
            $view = 'role.show';
        } else {
            $view = 'role.new';
        }
        $permissions = $this->permission->allPermissions();
        return $this->response->title(trans('app.view') . ' ' . trans('role.name'))
            ->data(compact('role', 'permissions'))
            ->view($view, true)
            ->output();
    }

    /**
     * Show the form for creating a new role.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $permissions = $this->permission->allPermissions();
        $role = $this->repository->newInstance([]);
        return $this->response->title(trans('app.new') . ' ' . trans('role.name'))
            ->view('role.create', true)
            ->data(compact('role', 'permissions'))
            ->output();
    }

    /**
     * Create new role.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $attributes              = $request->all();
            $role                    = $this->repository->create($attributes);
            $role->permissions()->sync($attributes['permissions']);
            return $this->response->message(trans('messages.success.created', ['Module' => trans('role.name')]))
                ->code(0)
                ->status('success')
                ->url(guard_url('role'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('/role'))
                ->redirect();
        }

    }

    /**
     * Show role for editing.
     *
     * @param Request $request
     * @param RegionRole   $role
     *
     * @return Response
     */
    public function edit(Request $request, RegionRole $role)
    {
        $permissions     = $this->permission->groupedPermissions(true);
        return $this->response->title(trans('app.edit') . ' ' . trans('roles::role.name'))
            ->view('roles::role.edit', true)
            ->data(compact('role', 'permissions'))
            ->output();
    }

    /**
     * Update the role.
     *
     * @param Request $request
     * @param RegionRole   $role
     *
     * @return Response
     */
    public function update(Request $request, RegionRole $role)
    {
        try {
            $attributes = $request->all();
            $permissions = $request->input('permissions');

            $role->update($attributes);
            $role->permissions()->sync($permissions);

            return $this->response->message(trans('messages.success.updated', ['Module' => trans('role.name')]))
                ->code(0)
                ->status('success')
                ->url(guard_url('role/' . $role->id))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('role/' . $role->id))
                ->redirect();
        }

    }

    /**
     * Remove the role.
     *
     * @param Request $request
     * @param RegionRole   $role
     *
     * @return Response
     */
    public function destroy(Request $request, RegionRole $role)
    {
        try {
            $role->forceDelete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('role.name')]))
                ->code(202)
                ->status('success')
                ->url(guard_url('role'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('role'))
                ->redirect();
        }

    }
    public function destroyAll(Request $request)
    {
        try {
            $data = $request->all();
            $ids = $data['ids'];
            $this->repository->forceDelete($ids);

            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('role.name')]))
                ->status("success")
                ->code(202)
                ->url(guard_url('role'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->status("error")
                ->code(400)
                ->url(guard_url('role'))
                ->redirect();
        }
    }

}
