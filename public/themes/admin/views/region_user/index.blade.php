<div class="main">
    <div class="layui-card fb-minNav">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a href="{{ guard_url('home') }}">主页</a><span lay-separator="">/</span>
            <a><cite>{{ trans("region_user.name") }}管理</cite></a>
        </div>
    </div>
    <div class="main_full">
        <div class="layui-col-md12">
            <div class="tabel-message">
                <div class="layui-inline tabel-btn">
                    @if(Auth::user()->isSuperuser() || Auth::user()->checkPermission('region_user.create'))
                    <button class="layui-btn layui-btn-warm "><a href="{{guard_url('region_user/create')}}">添加{{ trans("region_user.name") }}</a></button>
                    @endif
                    @if(Auth::user()->isSuperuser() || Auth::user()->checkPermission('region_user.destroy'))
                    <button class="layui-btn layui-btn-primary " data-type="del" data-events="del">删除</button>
                    @endif
                </div>
                <div class="layui-inline">
                    <input class="layui-input search_key" name="search_name" id="demoReload" placeholder="手机号码/姓名" autocomplete="off">
                </div>
                <button class="layui-btn" data-type="reload">搜索</button>
            </div>

            <table id="fb-table" class="layui-table"  lay-filter="fb-table">

            </table>
        </div>
    </div>
</div>

<script type="text/html" id="barDemo">
    @if(Auth::user()->isSuperuser() || Auth::user()->checkPermission('region_user.show'))
    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
    @endif
    @if(Auth::user()->isSuperuser() || Auth::user()->checkPermission('region_user.destroy'))
    <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>
    @endif
</script>


<script>
    var main_url = "{{guard_url('region_user')}}";
    var delete_all_url = "{{guard_url('region_user/destroyAll')}}";
    var width = 0;
    @if(Auth::user()->isSuperuser() || Auth::user()->checkPermission('region_user.show'))
        width = width + 75;
    @endif
    @if(Auth::user()->isSuperuser() || Auth::user()->checkPermission('region_user.destroy'))
        width = width + 75;
    @endif
    layui.use(['jquery','element','table'], function(){
        var table = layui.table;
        var form = layui.form;
        var $ = layui.$;
        table.render({
            elem: '#fb-table'
            ,url: '{{guard_url('region_user')}}'
            ,cols: [[
                {checkbox: true, fixed: true}
                ,{field:'id',title:'ID', width:80, sort: true}
                ,{field:'region_name',title:'{!! trans('region.label.name')!!}'}
                ,{field:'phone',title:'{!! trans('region_user.label.phone')!!}'}
                ,{field:'name',title:'{!! trans('region_user.label.name')!!}'}
                ,{field:'role_names',title:'{!! trans('region_user.label.roles')!!}'}
                ,{field:'score',title:'操作', width:width, align: 'right',toolbar:'#barDemo'}
            ]]
            ,id: 'fb-table'
            ,page: true
            ,limit: 10
            ,height: 'full-200'
        });


    });
</script>
{!! Theme::partial('common_handle_js') !!}