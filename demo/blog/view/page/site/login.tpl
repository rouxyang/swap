<?php static::link_script('jquery.js'); ?>
<?php static::link_script('jquery.form.js'); ?>

<div id="page">
    <form id="login_form" action="<?php static::php_url('site/do_login'); ?>" method="POST" enctype="application/x-www-form-urlencoded">
        <h2>用户登录</h2>
        <p id="login_form_tips"></p>
        <p><label for="login_form_name">用户：</label><input type="text" id="login_form_name" maxlength="16" name="name"></p>
        <p><label for="login_form_pass">密码：</label><input type="password" id="login_form_pass" name="pass"></p>
        <p><input type="checkbox" id="login_form_remember" name="remember" value="on"><label for="login_form_remember">记住登录状态</label></p>
        <p><input type="submit" id="login_form_submit" name="submit" value="登录"></p>
    </form>
</div>
