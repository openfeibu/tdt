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
                    @if(Auth::user()->isSuperuser() || Auth::user()->checkPermission('region.create'))
                    <button class="layui-btn layui-btn-warm "><a href="{{ url('/admin/region/create') }}">添加{{ trans('region.name') }}</a></button>
                    @endif
                    @if(Auth::user()->isSuperuser() || Auth::user()->checkPermission('region.destroy'))
                    <button class="layui-btn layui-btn-primary " data-type="del" data-events="del">删除</button>
                    @endif
                </div>
            </div>

            <table id="fb-table" class="layui-table"  lay-filter="fb-table">

            </table>
        </div>
    </div>
</div>
<script type="text/html" id="barDemo">
    @if(Auth::user()->isSuperuser() || Auth::user()->checkPermission('region.show'))
    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
    @endif
    @if(Auth::user()->isSuperuser() || Auth::user()->checkPermission('region.destroy'))
    <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>
    @endif
</script>
<script type="text/html" id="qrcodeTEM">
    <a href="/image/download/@{{d.qrcode}}"><img src="/image/original/@{{d.qrcode}}" alt="" height="28">
</script>
<script>
    var main_url = "{{guard_url('region')}}";
    var delete_all_url = "{{guard_url('region/destroyAll')}}";
    var width = 0;
    @if(Auth::user()->isSuperuser() || Auth::user()->checkPermission('shop.show'))
        width = width + 75;
    @endif
    @if(Auth::user()->isSuperuser() || Auth::user()->checkPermission('shop.destroy'))
        width = width + 75;
    @endif
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
                ,{field:'phone',title:'{{ trans('region.label.phone') }}',edit:'text'}
                ,{field:'wechat',title:'{{ trans('region.label.wechat') }}',edit:'text'}
                ,{field:'area_names',title:'{{ trans('region.label.region_area') }}'}
                ,{field:'created_at',title:'{{ trans('app.created_at') }}'}
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