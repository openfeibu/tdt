<div class="main">
    <div class="layui-card fb-minNav">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a href="{{ route('home') }}">主页</a><span lay-separator="">/</span>
            <a><cite>{{ trans('region.name') }}</cite></a>
        </div>
    </div>
    <div class="main_full">
        <div class="layui-col-md12">
            <div class="tabel-message">
                <div class="layui-inline tabel-btn">
                    <button class="layui-btn layui-btn-warm "><a href="{{ url('/admin/region/create') }}">添加{{ trans('region.name') }}</a></button>
                    <button class="layui-btn layui-btn-primary " data-type="del" data-events="del">删除</button>
                </div>
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
<script type="text/html" id="qrcodeTEM">
    <a href="/image/download/@{{d.qrcode}}"><img src="/image/original/@{{d.qrcode}}" alt="" height="28">
</script>
<script>
    var main_url = "{{guard_url('region')}}";
    var delete_all_url = "{{guard_url('region/destroyAll')}}";
    layui.use(['jquery','element','table'], function(){
        var $ = layui.$;
        var table = layui.table;
        var form = layui.form;
        table.render({
            elem: '#fb-table'
            ,url: '{{guard_url('region')}}'
            ,cols: [[
                {checkbox: true, fixed: true}
                ,{field:'id',title:'ID', width:80, sort: true}
                ,{field:'name',title:'{{ trans('region.label.name') }}',edit:'text'}
                ,{field:'leader',title:'{{ trans('region.label.leader') }}',edit:'text'}
                ,{field:'tel',title:'{{ trans('region.label.tel') }}',edit:'text'}
                ,{field:'mobile',title:'{{ trans('region.label.mobile') }}',edit:'text'}
                ,{field:'wechat',title:'{{ trans('region.label.wechat') }}',edit:'text'}
                ,{field:'area_names',title:'{{ trans('region.label.region_area') }}'}
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