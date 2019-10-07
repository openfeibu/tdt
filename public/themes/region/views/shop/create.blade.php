<div class="main">
    <div class="layui-card fb-minNav">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a href="{{ route('region.home') }}">主页</a><span lay-separator="">/</span>
            <a><cite>{{ trans('shop.name') }}</cite></a><span lay-separator="">/</span>
            <a><cite>添加{{ trans('shop.name') }}</cite></a>
        </div>
    </div>
    <div class="main_full">
        <div class="layui-col-md12">
            {!! Theme::partial('message') !!}
            <div class="fb-main-table">
                <form class="layui-form" action="{{guard_url('shop')}}" method="POST" lay-filter="fb-form">
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.name') }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" lay-verify="required" autocomplete="off" placeholder="请输入{{ trans('shop.label.name') }}" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.leader') }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="leader" lay-verify="required" autocomplete="off" placeholder="请输入{{ trans('shop.label.leader') }}" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.mobile') }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="mobile" lay-verify="required" autocomplete="off" placeholder="请输入{{ trans('shop.label.mobile') }}" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.inviter') }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="inviter" lay-verify="required" autocomplete="off" placeholder="请输入{{ trans('shop.label.inviter') }}" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.first') }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="first" lay-verify="required" autocomplete="off" placeholder="请输入{{ trans('shop.label.first') }}" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.signer') }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="signer" lay-verify="required" autocomplete="off" placeholder="请输入{{ trans('shop.label.signer') }}" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.cooperation_date') }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="cooperation_date" lay-verify="required" autocomplete="off" placeholder="请输入{{ trans('shop.label.cooperation_date') }}" class="layui-input" id="cooperation_date" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.is_full') }}</label>
                        <div class="layui-input-inline">
                            <select name="is_full" class="layui-select">
                                <option value="1">是</option>
                                <option value="0">否</option>
                            </select>
                            <input type="text" name="price" autocomplete="off" placeholder="非全款请输入{{ trans('app.price') }}" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.contract_date') }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="contract_date" lay-verify="required" autocomplete="off" placeholder="请输入{{ trans('shop.label.contract_date') }}" class="layui-input" id="contract_date">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.status') }}</label>
                        <div class="layui-input-inline">
                            <select name="status" class="layui-select">
                                @foreach(config('model.shop.shop.status') as $status)
                                <option value="{{ $status }}">{{ trans("shop.status.".$status) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">经纬度</label>
                        <div class="layui-input-inline">
                            <input type="text" name="longitude" autocomplete="off" placeholder="" class="layui-input" value="{{$shop['longitude']}}" readonly>
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="latitude" autocomplete="off" placeholder="" class="layui-input" value="{{$shop['latitude']}}" readonly>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.address') }}</label>
                        <div class="layui-input-inline">
                            <input id="keyword" name="address" type="textbox"  class="layui-input"  value="" lay-verify="required" >
                            <input type="button" value="搜索" class="layui-button-mapsearch"  onclick="searchKeyword()">
                            <div class="layui-form-mid layui-word-aux">点击地图快速获取经纬度</div>
                        </div>

                        <div id="map"></div>
                    </div>

                    <div class="layui-form-item layui-form-text">
                        <label class="layui-form-label">店铺详情</label>
                        <div class="layui-input-block">
                            <script type="text/plain" id="content" name="content" style="width:1000px;height:240px;">

                            </script>
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
<script>
      var geocoder,map,markers = [];
    var init = function() {
        var center = new qq.maps.LatLng(23.15641,113.3318);
        map = new qq.maps.Map(document.getElementById('map'),{
            center: center,
            zoom: 15
        });
      
        //调用Poi检索类
        geocoder = new qq.maps.Geocoder({
            
            complete : function(result){
				console.log(result)
				map.setCenter(result.detail.location);
				var marker = new qq.maps.Marker({
					map:map,
					position: result.detail.location
				});
				markers.push(marker)
				document.getElementsByName('longitude')[0].value = result.detail.location.lng;
				document.getElementsByName('latitude')[0].value = result.detail.location.lat;
	
				qq.maps.event.addListener(marker,'click',function(event) {
					document.getElementsByName('longitude')[0].value = event.latLng.getLng();
					document.getElementsByName('latitude')[0].value = event.latLng.getLat();
				})
                
               
            },
			//若服务请求失败，则运行以下函数
			error: function() {
				alert("无法获取地址，请检查地址是否正确");
			}
        });
        qq.maps.event.addListener(map,'click',function(event) {
            document.getElementsByName('longitude')[0].value = event.latLng.getLng();
            document.getElementsByName('latitude')[0].value = event.latLng.getLat();
            console.log(event)
        })
    }
    //清除地图上的marker
    function clearOverlays(overlays) {
        var overlay;
        while (overlay = overlays.pop()) {
            overlay.setMap(null);
        }
    }
    //调用poi类信接口
    function searchKeyword() {
        var keyword = document.getElementById("keyword").value;
        //region = new qq.maps.LatLng(39.936273,116.44004334);
        clearOverlays(markers);
		
        // searchService.setPageCapacity(5);
        geocoder.getLocation(keyword);//根据中心点坐标、半径和关键字进行周边检索。
		
    }

      layui.use('laydate', function(){
          var laydate = layui.laydate;

          //执行一个laydate实例
          laydate.render({
              elem: '#cooperation_date' //指定元素
              ,type: 'date'
          });
          laydate.render({
              elem: '#contract_date' //指定元素
              ,type: 'date'
          });

      });
</script>
