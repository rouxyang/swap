<?php static::link_script('jquery.js'); ?>
<?php static::link_script('jquery.form.js'); ?>

<body id="page">
    <form id="login_form" action="<?php static::php_url('admin-site/do_login'); ?>" method="POST" enctype="application/x-www-form-urlencoded">
        <p id="login_form_tips"></p>
        <p><label for="login_form_name">用户</label><input type="text" id="login_form_name" name="name"></p>
        <p><label for="login_form_pass">密码</label><input type="password" id="login_form_pass" name="pass"></p>
        <p><input type="submit" id="login_form_submit" name="submit" value="登录"></p>
    </form>
    <p><a href="<?php static::php_url('site/index'); ?>">前台首页</a></p>
</body>
