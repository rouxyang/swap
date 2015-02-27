<div class="block_header"><div class="block_header_container">
    <h1><a href="<?php static::php_url('site/index'); ?>">论坛名称</a></h1>
    <div class="nav">
        <?php if ($_logined): ?>
            <ul>
                <li><a href="<?php static::csrf_url('user', 'site/logout'); ?>">退出</a></li>
                <li><a href="<?php static::php_url('user/setting'); ?>">设定</a></li>
                <li><a href="<?php static::php_url('user/info'); ?>">信息</a></li>
                <?php if ($_user->has_admin_privilege()): ?>
                    <li><a href="<?php static::php_url('admin-site/login'); ?>">后台管理</a></li>
                <?php endif; ?>
            </ul>
            <p>欢迎您，<?php echo $_user->name; ?></p>
        <?php else: ?>
            <ul>
                <li><a href="<?php static::php_url('site/about'); ?>">关于</a></li>
                <li><a href="<?php static::php_url('user/register'); ?>">注册</a></li>
                <li><a href="<?php static::php_url('site/login'); ?>">登录</a></li>
            </ul>
        <?php endif; ?>
    </div>
</div></div>
