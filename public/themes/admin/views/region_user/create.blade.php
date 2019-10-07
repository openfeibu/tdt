<div class="main">
    <div class="layui-card fb-minNav">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a href="{{ guard_url('home') }}">主页</a><span lay-separator="">/</span>
            <a><cite>{{ trans("region_user.name") }}</cite></a><span lay-separator="">/</span>
    </div>
    <div class="main_full">
        <div class="layui-col-md12">
            {!! Theme::partial('message') !!}
            <div class="fb-main-table">
                <form class="layui-form" action="{{guard_url('region_user')}}" method="post" lay-filter="fb-form">
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans("region.name") }}</label>
                        <div class="layui-input-inline">
                            <select name="region_id">
                                @foreach($regions as $key => $region)
                                    <option value="{{ $region->id }}" @if($region->id == $region_user->region_id) selected @endif>{{ $region->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans("region_user.label.phone") }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="phone" value="{{ $region_user->phone }}" lay-verify="required" autocomplete="off" placeholder="请输入{{ trans("region_user.label.phone") }}" class="layui-input" >
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans("region_user.label.name") }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" value="{{ $region_user->name }}" lay-verify="required" autocomplete="off" placeholder="请输入{{ trans("region_user.label.name") }}" class="layui-input" >
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans("region_user.label.password") }}</label>
                        <div class="layui-input-inline">
                            <input type="password" name="password" placeholder="请输入{{ trans("region_user.label.password") }}" autocomplete="off" class="layui-input" lay-verify="required">
                        </div>
                        <div class="layui-form-mid layui-word-aux">请输入密码，至少六位数</div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans("region_user.label.roles") }}</label>
                        <div class="layui-input-block">
                            <?php $i=1 ?>
                            @foreach($roles as $key => $role)
                            <input type="radio" name="roles[]" value="{{ $role->id }}" title="{{ $role->name }}" @if($i == 1) checked @endif >
                             <?php $i++ ?>
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
<script>
    layui.use('form', function(){
        var form = layui.form;

        form.render();
    });
</script>

