<?php static::link_script('jquery.js'); ?>
<?php static::link_script('jquery.form.js'); ?>

<div id="page">
    <form id="edit_form" action="<?php static::php_url('category/edit?id=' . $category->id); ?>" method="POST" enctype="application/x-www-form-urlencoded">
        <p id="edit_form_tips"></p>
        <?php static::csrf_field('member'); ?>
        <p><label for="edit_form_name">分类名</label><input type="text" id="edit_form_name" name="name" value="<?php echo $category->name; ?>"></p>
        <p><input type="submit" id="edit_form_submit" name="submit" value="提交"></p>
    </form>
</div>
