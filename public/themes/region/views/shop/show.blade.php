<div class="main">
    <div class="layui-card fb-minNav">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a href="{{ route('region.home') }}">主页</a><span lay-separator="">/</span>
            <a><cite>{{ trans('shop.name') }}</cite></a><span lay-separator="">/</span>
            <a><cite>编辑{{ trans('shop.name') }}</cite></a>
        </div>
    </div>
    <div class="main_full">
        <div class="layui-col-md12">
            {!! Theme::partial('message') !!}
            <div class="fb-main-table">
                <form class="layui-form" action="{{guard_url('shop/'.$shop->id)}}" method="POST" lay-filter="fb-form">
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.name') }}：</label>
                        <div class="layui-input-inline">
                            <p class="input-p">{{ $shop['name'] }}</p>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.leader') }}：</label>
                        <div class="layui-input-inline">
                            <p class="input-p">{{ $shop['leader'] }}</p>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.mobile') }}：</label>
                        <div class="layui-input-inline">
                            <p class="input-p">{{ $shop['name'] }}</p>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.inviter') }}：</label>
                        <div class="layui-input-inline">
                            <p class="input-p">{{ $shop['inviter'] }}</p>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.first') }}：</label>
                        <div class="layui-input-inline">
                            <p class="input-p">{{ $shop['first'] }}</p>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.signer') }}：</label>
                        <div class="layui-input-inline">
                            <p class="input-p">{{ $shop['signer'] }}</p>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.cooperation_date') }}：</label>
                        <div class="layui-input-inline">
                            <p class="input-p">{{ $shop['cooperation_date'] }}</p>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.is_full') }}：</label>
                        <div class="layui-input-inline">
                            <p class="input-p">{{ $shop['is_full_desc'] }}</p>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.status') }}：</label>
                        <div class="layui-input-inline">
                            <p class="input-p">{{ $shop['status_desc'] }}</p>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.address') }}：</label>
                        <div class="layui-input-inline">
                            <p class="input-p">{{ $shop['address'] }}</p>
                        </div>

                    </div>

                    <div class="layui-form-item layui-form-text">
                        <label class="layui-form-label">店铺详情：</label>
                        <div class="layui-input-block">
                            <script type="text/plain" id="content" name="content" style="width:1000px;height:240px;">

                            </script>
                        </div>
                    </div>

                    {!!Form::token()!!}
                    <input type="hidden" name="_method" value="PUT">
                </form>
            </div>

        </div>
    </div>
</div>


<script charset="utf-8" src="https://map.qq.com/api/js?v=2.exp&key={{ config('common.qq_map_key') }}"></script>
{!! Theme::asset()->container('ueditor')->scripts() !!}
<script>
    var ue = getUe();
    window.onload = function(){
        init();
    }
    layui.use('laydate', function() {
        var laydate = layui.laydate;
        laydate.render({
            elem: '#business_time'
            ,type:'time'
            ,format:'HH:mm'
            , range: true
        });
    });
</script>
