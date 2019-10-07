<div class="main">
    <div class="main_full">
        <div class="layui-col-md12">
            <div class="layui-card">
                <!-- <div class="layui-card-header">待办事项</div> -->
                <div class="layui-card-body">

                    <div class="fb-carousel fb-backlog " lay-anim="" lay-indicator="inside" lay-arrow="none" >
                        <div carousel-item="">
                            <ul class="layui-row fb-clearfix ">
                                <li class="layui-col-xs3">
                                    <a lay-href="" class="fb-backlog-body">
                                        <h3>总门店</h3>
                                        <p><cite>{{ $shop_count }}</cite></p>
                                    </a>
                                </li>
                                <li class="layui-col-xs3">
                                    <a lay-href="" class="fb-backlog-body">
                                        <h3>正常合作门店</h3>
                                        <p><cite>{{ $normal_shop_count }}</cite></p>
                                    </a>
                                </li>

                                <li class="layui-col-xs3">
                                    <a lay-href="" class="fb-backlog-body">
                                        <h3>定金发货门店</h3>
                                        <p><cite>{{ $earnest_shop_count }}</cite></p>
                                    </a>
                                </li>
                                <li class="layui-col-xs3">
                                    <a lay-href="" class="fb-backlog-body">
                                        <h3>取消合作门店</h3>
                                        <p><cite>{{ $cancel_shop_count }}</cite></p>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>