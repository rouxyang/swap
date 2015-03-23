<?php static::link_script('jquery.js'); ?>
<?php static::link_script('jquery.form.js'); ?>

<div id="page">
    <form id="edit_form" action="<?php static::php_url('member/edit?id=' . $member->id); ?>" method="POST" enctype="application/x-www-form-urlencoded">
        <p id="edit_form_tips"></p>
        <?php static::csrf_field('member'); ?>
        <p><label for="edit_form_name">用户昵称</label><input type="text" id="edit_form_name" name="name" value="<?php echo $member->name; ?>"></p>
        <p><label for="edit_form_pass">修改密码</label><input type="password" id="edit_form_pass" name="pass"></p>
        <p><label for="edit_form_repass">重复密码</label><input type="password" id="edit_form_repass" name="repass"></p>
        <p><input type="submit" id="edit_form_submit" name="submit" value="确定"></p>
    </form>
</div>
