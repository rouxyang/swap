<?php static::link_script('jquery.js'); ?>
<?php static::link_script('jquery.form.js'); ?>

<div id="page">
    <form id="edit_form" action="<?php static::php_url('setting/edit'); ?>" method="POST" enctype="application/x-www-form-urlencoded">
        <p id="edit_form_tips"></p>
        <?php static::csrf_field('member'); ?>
        <p><label for="edit_form_<?php echo setting_model::id_blog_name; ?>">博客名称</label><input type="text" id="edit_form_<?php echo setting_model::id_blog_name; ?>" name="<?php echo setting_model::id_blog_name; ?>" value="<?php echo $settings[setting_model::id_blog_name]->value; ?>"></p>
        <p><label for="edit_form_<?php echo setting_model::id_blog_description; ?>">博客描述</label><input type="text" id="edit_form_<?php echo setting_model::id_blog_description; ?>" name="<?php echo setting_model::id_blog_description; ?>" value="<?php echo $settings[setting_model::id_blog_description]->value; ?>"></p>
        <p><label for="edit_form_<?php echo setting_model::id_blog_keywords; ?>">博客关键字</label><input type="text" id="edit_form_<?php echo setting_model::id_blog_keywords; ?>" name="<?php echo setting_model::id_blog_keywords; ?>" value="<?php echo $settings[setting_model::id_blog_keywords]->value; ?>"></p>
        <p><label for="edit_form_<?php echo setting_model::id_copyright; ?>">版权信息</label><input type="text" id="edit_form_<?php echo setting_model::id_copyright; ?>" name="<?php echo setting_model::id_copyright; ?>" value="<?php echo $settings[setting_model::id_copyright]->value; ?>"></p>
        <p><label for="edit_form_<?php echo setting_model::id_captcha_question; ?>">防止机器人而准备的问题</label><input type="text" id="edit_form_<?php echo setting_model::id_captcha_question; ?>" name="<?php echo setting_model::id_captcha_question; ?>" value="<?php echo $settings[setting_model::id_captcha_question]->value; ?>"></p>
        <p><label for="edit_form_<?php echo setting_model::id_captcha_answer; ?>">防止机器人的问题的答案</label><input type="text" id="edit_form_<?php echo setting_model::id_captcha_answer; ?>" name="<?php echo setting_model::id_captcha_answer; ?>" value="<?php echo $settings[setting_model::id_captcha_answer]->value; ?>"></p>
        <p><input type="submit" id="edit_form_submit" name="submit" value="确定"></p>
    </form>
</div>
