<div class="layui-side layui-side-menu">
    <div class="layui-side-scroll">
        <div class="logo">{{ Auth::user()->region->name }}管理后台</div>
        <ul class="layui-nav layui-nav-tree" lay-filter="test">
            {{--{!!Menu::menu('admin', 'menu.menu.admin')!!}--}}
            @inject('permissionPresenter','App\Repositories\Presenter\RegionPermissionPresenter')

            {!! $permissionPresenter->menus() !!}
        </ul>

    </div>
</div>

<!-- 左侧菜单结束 -->

