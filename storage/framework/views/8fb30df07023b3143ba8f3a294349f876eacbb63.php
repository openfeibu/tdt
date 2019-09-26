<div class="main">
    <div class="layui-card fb-minNav">
        <div class="layui-breadcrumb" lay-filter="breadcrumb" style="visibility: visible;">
            <a href="<?php echo e(guard_url('home')); ?>">主页</a><span lay-separator="">/</span>
            <a><cite><?php echo e(trans("user.name")); ?></cite></a><span lay-separator="">/</span>
    </div>
    <div class="main_full">
        <div class="layui-col-md12">
            <?php echo Theme::partial('message'); ?>

            <div class="fb-main-table">
                <form class="layui-form" action="<?php echo e(guard_url('user')); ?>" method="post" lay-filter="fb-form">
                    <div class="layui-form-item">
                        <label class="layui-form-label"><?php echo e(trans("user.label.phone")); ?></label>
                        <div class="layui-input-inline">
                            <input type="text" name="phone" value="<?php echo e($user->phone); ?>" lay-verify="title" autocomplete="off" placeholder="请输入<?php echo e(trans("user.label.phone")); ?>" class="layui-input" >
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label"><?php echo e(trans("user.label.name")); ?></label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" value="<?php echo e($user->name); ?>" lay-verify="title" autocomplete="off" placeholder="请输入<?php echo e(trans("user.label.name")); ?>" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">头像</label>
                        <?php echo $user->files('avatar')->field('avatar_url')
                        ->url($user->getUploadUrl('avatar'))
                        ->uploader(); ?>

                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label"><?php echo e(trans("user.label.password")); ?></label>
                        <div class="layui-input-inline">
                            <input type="password" name="password" placeholder="请输入<?php echo e(trans("user.label.password")); ?>" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">请输入密码，至少六位数</div>
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
<script>
    layui.use('form', function(){
        var form = layui.form;

        form.render();
    });
</script>

