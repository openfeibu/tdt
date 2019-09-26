<div class="main">
    <div class="layui-card fb-minNav">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a href="<?php echo e(route('home')); ?>">主页</a><span lay-separator="">/</span>
            <a><cite><?php echo e(trans('region.name')); ?></cite></a><span lay-separator="">/</span>
            <a><cite>添加<?php echo e(trans('region.name')); ?></cite></a>
        </div>
    </div>
    <div class="main_full">
        <div class="layui-col-md12">
            <?php echo Theme::partial('message'); ?>

            <div class="fb-main-table">
                <form class="layui-form" action="<?php echo e(guard_url('region')); ?>" method="POST" lay-filter="fb-form">
                    <div class="layui-form-item">
                        <label class="layui-form-label"><?php echo e(trans('region.label.name')); ?></label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" lay-verify="required" autocomplete="off" placeholder="请输入<?php echo e(trans('region.label.name')); ?>" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"><?php echo e(trans('region.label.leader')); ?></label>
                        <div class="layui-input-inline">
                            <input type="text" name="leader" autocomplete="off" placeholder="请输入<?php echo e(trans('region.label.leader')); ?>" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"><?php echo e(trans('region.label.tel')); ?></label>
                        <div class="layui-input-inline">
                            <input type="text" name="tel" autocomplete="off" placeholder="请输入<?php echo e(trans('region.label.tel')); ?>" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"><?php echo e(trans('region.label.mobile')); ?></label>
                        <div class="layui-input-inline">
                            <input type="text" name="mobile"  autocomplete="off" placeholder="请输入<?php echo e(trans('region.label.mobile')); ?>" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"><?php echo e(trans('region.label.wechat')); ?></label>
                        <div class="layui-input-inline">
                            <input type="text" name="wechat" autocomplete="off" placeholder="请输入<?php echo e(trans('region.label.wechat')); ?>" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"><?php echo e(trans('region.label.region_area')); ?></label>
                        <div class="layui-input-block">
                            <?php $__currentLoopData = app('area_repository')->getProvinces(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <input type="checkbox" name="area_code[]" title="<?php echo e($province['name']); ?>" value="<?php echo e($province['code']); ?>">
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                        </div>
                    </div>
                    <?php echo Form::token(); ?>

                </form>
            </div>

        </div>
    </div>
</div>
