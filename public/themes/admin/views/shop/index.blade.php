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
                   <input class="layui-input search_key" name="name" placeholder="门店名称" autocomplete="off">
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
                ,{field:'name',title:'{{ trans('shop.label.name') }}'}
                //,{field:'image',title:'{{ trans('shop.label.image') }}',toolbar:'#imageTEM'}
                ,{field:'address',title:'{{ trans('shop.label.address') }}'}
                ,{field:'leader',title:'{{ trans('shop.label.leader') }}'}
                ,{field:'mobile',title:'{{ trans('shop.label.mobile') }}'}
                ,{field:'inviter',title:'{{ trans('shop.label.inviter') }}'}
                ,{field:'first',title:'{{ trans('shop.label.first') }}',width:60}
                ,{field:'signer',title:'{{ trans('shop.label.signer') }}'}
                ,{field:'cooperation_date',title:'{{ trans('shop.label.cooperation_date') }}'}
                ,{field:'is_full_desc',title:'{{ trans('shop.label.is_full') }}'}
                ,{field:'contract_date',title:'{{ trans('shop.label.contract_date') }}'}
                ,{field:'status_desc',title:'{{ trans('shop.label.status') }}'}
                //,{field:'created_at',title:'{{ trans('app.created_at') }}'}
                ,{field:'score',title:'操作', width:150, align: 'right',toolbar:'#barDemo'}
            ]]
            ,id: 'fb-table'
            ,page: true
            ,limit: 10
            ,height: 'full-200'
        });
    });
</script>
{!! Theme::partial('common_handle_js') !!}