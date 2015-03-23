<?php static::link_script('jquery.js'); ?>
<?php static::link_script('jquery.form.js'); ?>

<div id="page">
    <form id="register_form" action="<?php static::php_url('user/do_register'); ?>" method="POST" enctype="application/x-www-form-urlencoded">
        <h2>注册新账号</h2>
        <p id="register_form_tips"></p>
        <p><label for="register_form_name">用户</label><input type="text" id="register_form_name" name="name"></p>
        <p><label for="register_form_pass">密码</label><input type="password" id="register_form_pass" name="pass"></p>
        <p><label for="register_form_re_pass">重复</label><input type="password" id="register_form_re_pass" name="re_pass"></p>
        <p><input type="submit" id="register_form_submit" name="submit" value="注册"></p>
    </form>
</div>
