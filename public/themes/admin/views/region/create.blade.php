<div class="main">
    <div class="layui-card fb-minNav">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a href="{{ route('home') }}">主页</a><span lay-separator="">/</span>
            <a><cite>{{ trans('region.name') }}</cite></a><span lay-separator="">/</span>
            <a><cite>添加{{ trans('region.name') }}</cite></a>
        </div>
    </div>
    <div class="main_full">
        <div class="layui-col-md12">
            {!! Theme::partial('message') !!}
            <div class="fb-main-table">
                <form class="layui-form" action="{{guard_url('region')}}" method="POST" lay-filter="fb-form">
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('region.label.name') }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" lay-verify="required" autocomplete="off" placeholder="请输入{{ trans('region.label.name') }}" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('region.label.leader') }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="leader" autocomplete="off" placeholder="请输入{{ trans('region.label.leader') }}" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('region.label.tel') }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="tel" autocomplete="off" placeholder="请输入{{ trans('region.label.tel') }}" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('region.label.mobile') }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="mobile"  autocomplete="off" placeholder="请输入{{ trans('region.label.mobile') }}" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('region.label.wechat') }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="wechat" autocomplete="off" placeholder="请输入{{ trans('region.label.wechat') }}" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('region.label.region_area') }}</label>
                        <div class="layui-input-block">
                            @foreach(app('area_repository')->getProvinces() as $key => $province)
                                <input type="checkbox" name="area_code[]" title="{{ $province['capital'] }}  {{ $province['name'] }}" value="{{ $province['code'] }}">
                            @endforeach
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                        </div>
                    </div>
                    {!!Form::token()!!}
                </form>
            </div>

        </div>
    </div>
</div>
