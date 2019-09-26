<div class="main">
    <div class="layui-card fb-minNav">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a href="{{ route('home') }}">主页</a><span lay-separator="">/</span>
            <a><cite>{{ trans('shop.name') }}</cite></a><span lay-separator="">/</span>
            <a><cite>添加{{ trans('shop.name') }}</cite></a>
        </div>
    </div>
    <div class="main_full">
        <div class="layui-col-md12">
            {!! Theme::partial('message') !!}
            <div class="fb-main-table">
                <form class="layui-form" action="{{guard_url('shop/'.$shop->id)}}" method="POST" lay-filter="fb-form">
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.shop_name') }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="shop_name" lay-verify="required" autocomplete="off" placeholder="请输入{{ trans('shop.label.shop_name') }}" class="layui-input" value="{{ $shop->shop_name }}">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.city_name') }}</label>
                        <div class="layui-input-inline">
                            <select name="city_code" lay-verify="required" lay-search>
                                <option value=""></option>
                                @foreach(app('city_repository')->getCities() as $key => $city)
                                    <option value="{{ $city['city_code'] }}" @if($city['city_code'] == $shop['city_code']) selected @endif>{{ $city['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item shopAccout">
                        <label class="layui-form-label">店铺头像（100*100）</label>
                        {!! $shop->files('image')
                        ->url($shop->getUploadUrl('image'))
                        ->uploader()!!}
                    </div>

                    <div class="layui-form-item shopBanner">
                        <label class="layui-form-label">轮播图(750*375)</label>
                        {!! $shop->files('images',true)
                        ->url($shop->getUploadUrl('images'))
                        ->uploaders()!!}
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">{{ trans('shop.label.business_time') }}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="business_time" lay-verify="required" autocomplete="off" placeholder="请输入{{ trans('shop.label.business_time') }}" class="layui-input" id="business_time"  value="{{ date('H:i',strtotime($shop['opening_time'])) }} - {{ date('H:i',strtotime($shop['closing_time'])) }}">
                        </div>
                        <div class="layui-form-mid layui-word-aux">（09:00 - 22:00）</div>
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
                            <input id="keyword" name="address" type="textbox"  class="layui-input"  value="{{ $shop['address'] }}">
                            <input type="button" value="搜索" class="layui-button-mapsearch"  onclick="searchKeyword()">
                            <div class="layui-form-mid layui-word-aux">点击地图快速获取经纬度</div>
                        </div>
						
                        <div id="map"></div>
                    </div>

                    <div class="layui-form-item layui-form-text">
                        <label class="layui-form-label">店铺详情</label>
                        <div class="layui-input-block">
                            <script type="text/plain" id="content" name="content" style="width:1000px;height:240px;">{!! $shop['content'] !!}</script>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
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
				document.getElementsByName('longitude')[0].value = result.detail.location.lng;
				document.getElementsByName('latitude')[0].value = result.detail.location.lat;
				markers.push(marker)
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
</script>