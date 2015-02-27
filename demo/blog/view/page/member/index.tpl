<?php static::link_script('jquery.js'); ?>
<?php static::link_script('jquery.form.js'); ?>

<div id="page">
    <form id="new_form" action="<?php static::php_url('member/new'); ?>" method="POST" enctype="application/x-www-form-urlencoded">
        <p id="new_form_tips"></p>
        <?php static::csrf_field('member'); ?>
        <p><label for="new_form_name">用户昵称</label><input type="text" id="new_form_name" name="name"></p>
        <p><label for="new_form_pass">密码</label><input type="password" id="new_form_pass" name="pass"></p>
        <p><label for="new_form_repass">重复密码</label><input type="password" id="new_form_repass" name="repass"></p>
        <p><input type="submit" id="new_form_submit" name="submit" value="确定"></p>
    </form>
    <ul>
        <?php foreach ($members as $member): ?>
            <li><?php echo $member->name; ?> | <a href="<?php static::php_url('member/edit?id=' . $member->id); ?>">修改</a> | <a href="<?php static::csrf_url('member', 'member/delete?id=' . $member->id); ?>" onclick="return delete_member(this);">删除</a></li>
        <?php endforeach; ?>
    </ul>
</div>
