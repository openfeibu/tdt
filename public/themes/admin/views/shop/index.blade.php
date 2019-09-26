<div class="main">
    <div class="layui-card fb-minNav">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a href="{{ route('home') }}">主页</a><span lay-separator="">/</span>
            <a><cite>{{ trans('shop.name') }}</cite></a>
        </div>
    </div>
    <div class="main_full">
        <div class="layui-col-md12">
            <div class="tabel-message">
                <div class="layui-inline tabel-btn">
                    <button class="layui-btn layui-btn-warm "><a href="{{ url('/admin/shop/create') }}">添加{{ trans('shop.name') }}</a></button>
                    <button class="layui-btn layui-btn-primary " data-type="del" data-events="del">删除</button>
                </div>
                <div class="layui-inline">
                   <input class="layui-input search_key" name="shop_name" placeholder="门店名称" autocomplete="off">
                </div>
                <button class="layui-btn" data-type="reload">搜索</button>
            </div>

            <table id="fb-table" class="layui-table"  lay-filter="fb-table">

            </table>
        </div>
    </div>
</div>
<script type="text/html" id="barDemo">
    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>
</script>
<script type="text/html" id="imageTEM">
    <img src="@{{d.image}}" alt="" height="28">
</script>
<script>
    var main_url = "{{guard_url('shop')}}";
    var delete_all_url = "{{guard_url('shop/destroyAll')}}";
    layui.use(['jquery','element','table'], function(){
        var $ = layui.$;
        var table = layui.table;
        var form = layui.form;
        table.render({
            elem: '#fb-table'
            ,url: '{{guard_url('shop')}}'
            ,cols: [[
                {checkbox: true, fixed: true}
                ,{field:'id',title:'ID', width:80, sort: true}
                ,{field:'shop_name',title:'{{ trans('shop.label.shop_name') }}'}
                ,{field:'image',title:'{{ trans('shop.label.image') }}',toolbar:'#imageTEM'}
                ,{field:'address',title:'{{ trans('shop.label.address') }}'}
                ,{field:'business_time',title:'{{ trans('shop.label.business_time') }}'}
                ,{field:'city_name',title:'{{ trans('shop.label.city_name') }}'}
                ,{field:'view_count',title:'{{ trans('shop.label.view_count') }}'}
                ,{field:'created_at',title:'{{ trans('app.created_at') }}'}
                ,{field:'score',title:'操作', width:200, align: 'right',toolbar:'#barDemo'}
            ]]
            ,id: 'fb-table'
            ,page: true
            ,limit: 10
            ,height: 'full-200'
        });
    });
</script>
{!! Theme::partial('common_handle_js') !!}