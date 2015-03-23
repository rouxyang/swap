<?php static::link_script('jquery.js'); ?>
<?php static::link_script('jquery.form.js'); ?>

<div id="page">
    <form id="edit_form" action="<?php static::php_url('comment/edit?id=' . $comment->id); ?>" method="POST" enctype="application/x-www-form-urlencoded">
        <p id="edit_form_tips"></p>
        <?php static::csrf_field('member'); ?>
        <p><label for="edit_form_author">昵称</label><input type="text" id="edit_form_author" name="author" value="<?php echo $comment->author; ?>"><span>您的大名</span></p>
        <p><label for="edit_form_email">邮箱</label><input type="text" id="edit_form_email" name="email" value="<?php echo $comment->email; ?>"><span>方便与您联系 :) 请放心，我会为您的邮箱保密</span></p>
        <p><label for="edit_form_site">网站</label><input type="text" id="edit_form_site" name="site" value="<?php echo $comment->site; ?>"><span>如果您有个人网站，可以填写，方便我们联系</span></p>
        <p><label for="edit_form_content">内容</label><textarea id="edit_form_content" name="content"><?php echo $comment->content; ?></textarea><span>您的大作</span></p>
        <p><input type="submit" id="edit_form_submit" name="submit" value="修改"></p>
    </form>
</div>
