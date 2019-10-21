<div class="main">
    <div class="layui-card fb-minNav">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a href="{{ route('region.home') }}">主页</a><span lay-separator="">/</span>
            <a><cite>{{ trans('user.name') }}管理</cite></a>
        </div>
    </div>
    <div class="main_full">
        <div class="layui-col-md12">
            {!! Theme::partial('message') !!}
            <div class="tabel-message">
                <form class="form-horizontal" method="POST" action="{{ guard_url('user_submit_import') }}" enctype="multipart/form-data"  id="user_submit_import_form">
                    <div class="layui-inline tabel-btn">
                        <button class="layui-btn layui-btn-warm "><a href="{{url('image/original/system/user/user_muban.xlsx')}}">下载模板</a></button>
                    </div>
                     <div class=" tabel-btn mt20">
                        {{ csrf_field() }}
                       
						<div class="input-file" >
							选择文件
							<input id="file" type="file" class="form-control" name="file" required>
						</div>
                         <label class="fileText">未选中文件</label>
                        <button type="button" class="layui-btn layui-btn-normal user_submit_import_btn">确定</button>
                        <span class="layui-word-aux des_content">（注意：巡检人员帐号为手机号码。默认密码为：123456;请严格按照模板格式；提交后将直接录入数据库，请谨慎操作！）</span>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>



<script>

    layui.use(['jquery','element','table'], function(){
        var table = layui.table;
        var form = layui.form;
        var $ = layui.$;
        $(".user_submit_import_btn").click(function(){
            var fileFlag = false;

            $("input[name='file']").each(function(){
                if($(this).val()!="") {
                    fileFlag = true;
                }
            });
            if(!fileFlag) {
                layer.msg("请选择文件");
                return false;
            }

            layer.msg('上传中', {
                icon: 16
                ,shade: 0.01
                ,time:0
            });
            $("#user_submit_import_form").submit();
        });
	$(".input-file input").on('change', function( e ){
				//e.currentTarget.files 是一个数组，如果支持多个文件，则需要遍历
				var name = e.currentTarget.files[0].name;
				$(".fileText").text(name)
			});
    });
</script>
