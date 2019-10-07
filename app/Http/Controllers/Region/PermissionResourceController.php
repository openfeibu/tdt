<?php

namespace App\Http\Controllers\Region;

use Tree,Auth;
use App\Http\Controllers\Region\ResourceController as BaseController;
use Illuminate\Http\Request;
use App\Repositories\Eloquent\RegionPermissionRepositoryInterface;
use App\Models\RegionPermission;
/**
 * Resource controller class for permission.
 */
class PermissionResourceController extends BaseController
{
    /**
     * Initialize permission resource controller.
     *
     * @param type RegionPermissionRepositoryInterface $permission
     *
     */
    public function __construct(RegionPermissionRepositoryInterface $permission)
    {
        parent::__construct();
        $this->repository = $permission;
        $this->repository
            ->pushCriteria(\App\Repositories\Criteria\RequestCriteria::class);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($this->response->typeIs('json')) {
            $data = $this->repository->orderBy('order','asc')->orderBy('id','asc')->all()->toArray();
            $data = Tree::getTree($data);
            return $this->response
                ->success()
                ->data($data)
                ->output();
        }

        return $this->response->title(trans('permission.names'))
            ->view('permission.index', true)
            ->output();
    }

    /**
     * @param Request $request
     * @param RegionPermission $permission
     * @return mixed
     */
    public function show(Request $request, RegionPermission $permission)
    {

        if ($permission->exists) {
            $view = 'permission.show';
        } else {
            $view = 'permission.new';
        }
        $father = Auth::user()->menus();

        return $this->response->title(trans('app.view') . ' ' . trans('permission.name'))
            ->data(compact('permission','father'))
            ->view($view, true)
            ->output();
    }

    /**
     * Show the form for creating a new permission.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $permission = $this->repository->newInstance([]);
        $father = Auth::user()->menus();

        return $this->response->title(trans('app.new') . ' ' . trans('permission.name'))
            ->view('permission.create', true)
            ->data(compact('permission','father'))
            ->output();
    }

    /**
     * Create new permission.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $attributes              = $request->all();
            $permission                 = $this->repository->create($attributes);

            return $this->response->message(trans('messages.success.created', ['Module' => trans('permission.name')]))
                ->code(204)
                ->status('success')
                ->url(guard_url('permission'))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('/permission'))
                ->redirect();
        }

    }

    /**
     * Show permission for editing.
     *
     * @param Request $request
     * @param RegionPermission   $permission
     *
     * @return Response
     */
    public function edit(Request $request, RegionPermission $permission)
    {
        return $this->response->title(trans('app.edit') . ' ' . trans('permission.name'))
            ->view('permission.edit', true)
            ->data(compact('permission'))
            ->output();
    }

    /**
     * Update the permission.
     *
     * @param Request $request
     * @param RegionPermission   $permission
     *
     * @return Response
     */
    public function update(Request $request, RegionPermission $permission)
    {
        try {
            $attributes = $request->all();
            isset($attributes['name']) ? $attributes['name'] = trim($attributes['name'], chr(0xc2) . chr(0xa0)) : '';
            $permission->update($attributes);
            return $this->response->message(trans('messages.success.updated', ['Module' => trans('permission.name')]))
                ->code(0)
                ->status('success')
                ->url(guard_url('permission/' . $permission->id))
                ->redirect();
        } catch (Exception $e) {
            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('permission/' . $permission->id))
                ->redirect();
        }

    }

    /**
     * @param Request $request
     * @param RegionPermission $permission
     * @return mixed
     */
    public function destroy(Request $request, RegionPermission $permission)
    {
        try {

            $permission->forceDelete();
            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('permission.name')]))
                ->code(0)
                ->status('success')
                ->url(guard_url('permission'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->code(400)
                ->status('error')
                ->url(guard_url('permission'))
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

            return $this->response->message(trans('messages.success.deleted', ['Module' => trans('permission.name')]))
                ->status("success")
                ->code(202)
                ->url(guard_url('permission'))
                ->redirect();

        } catch (Exception $e) {

            return $this->response->message($e->getMessage())
                ->status("error")
                ->code(400)
                ->url(guard_url('permission'))
                ->redirect();
        }
    }

}
