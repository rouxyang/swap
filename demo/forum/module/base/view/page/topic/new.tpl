<?php static::link_script('jquery.js'); ?>
<?php static::link_script('jquery.form.js'); ?>

<div id="page">
    <form id="new_form" action="<?php static::php_url('topic/do_new?board_id=' . $board_id); ?>" method="POST" enctype="application/x-www-form-urlencoded">
        <p id="new_form_tips"></p>
        <?php static::csrf_field('user'); ?>
        <p><label for="new_form_title">标题</label><input type="text" id="new_form_title" name="title"></p>
        <p><label for="new_form_content">内容</label><textarea id="new_form_content" name="content"></textarea></p>
        <p><input type="submit" id="new_form_submit" name="submit" value="发布"></p>
    </form>
</div>
