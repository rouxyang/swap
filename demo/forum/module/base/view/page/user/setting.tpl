<?php static::link_script('jquery.js'); ?>
<?php static::link_script('jquery.form.js'); ?>

<div id="page">
    <form id="setting_form" action="<?php static::php_url('user/do_setting'); ?>" method="POST" enctype="multipart/form-data">
        <p id="setting_form_tips"></p>
        <?php static::csrf_field('user'); ?>
        <p><label for="setting_form_pass">输入原密码</label><input type="password" id="setting_form_pass" name="pass"></p>
        <p><label for="setting_form_new_pass">输入新密码</label><input type="password" id="setting_form_new_pass" name="new_pass"></p>
        <p><label for="setting_form_re_pass">确认新密码</label><input type="password" id="setting_form_re_pass" name="re_pass"></p>
        <p><label for="setting_form_avatar">上传新头像</label><input type="file" id="setting_form_avatar" name="avatar"></p>
        <p><input type="submit" id="setting_form_submit" name="save" value="保存"></p>
    </form>
</div>
