<?php static::link_script('jquery.js'); ?>
<?php static::link_script('jquery.form.js'); ?>

<div id="page">
    <form id="new_form" action="<?php static::php_url('post/do_new'); ?>" method="POST" enctype="application/x-www-form-urlencoded">
        <h2>发表文章</h2>
        <p id="new_form_tips"></p>
        <?php static::csrf_field('member'); ?>
        <p><span>类别</span><select name="category_id">
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
            <?php endforeach; ?>
        </select></p>
        <p><label for="new_form_title">标题</label><input type="text" id="new_form_title" maxlength="64" name="title"></p>
        <p><label for="new_form_content">内容</label><textarea id="new_form_content" name="content" cols="64" rows="12"></textarea></p>
        <p><label for="new_form_tags">标签</label><input type="text" id="new_form_tags" name="tags"><span>多个标签之间使用英文逗号(,)分隔</span></p>
        <p><input type="submit" id="new_form_submit" name="submit" value="发表"></p>
    </form>
</div>
