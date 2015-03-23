<div class="block_admin_header">
    <ul>
        <li><a href="<?php static::php_url('admin-setting/index'); ?>">基本设置</a></li>
        <li><a href="<?php static::php_url('admin-user/index'); ?>">用户管理</a></li>
        <li><a href="<?php static::php_url('admin-board/index'); ?>">板块管理</a></li>
        <li><a href="<?php static::csrf_url('admin', 'admin-site/logout'); ?>">注销登录</a></li>
    </ul>
</div>
