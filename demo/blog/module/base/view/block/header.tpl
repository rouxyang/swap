<div class="block_header"><div class="block_header_box">
    <h1><a href="<?php static::php_url('site/index'); ?>"><?php echo $_settings[setting_model::id_blog_name]->value; ?></a></h1>
    <h2><?php echo $_settings[setting_model::id_blog_description]->value; ?></h2>
    <ul>
        <li><a href="<?php static::php_url('site/index'); ?>">首页</a></li>
        <?php if ($_logined): ?>
        
            <li><a href="<?php static::csrf_url('member', 'site/logout'); ?>">注销</a></li>
            <li><a href="<?php static::php_url('site/admin'); ?>">管理</a></li>
            
        <?php else: ?>
        
            <li><a href="<?php static::php_url('site/login'); ?>">登录</a></li>
            
        <?php endif; ?>
        <li><a href="<?php static::php_url('site/about'); ?>">关于</a></li>
    </ul>
</div></div>
