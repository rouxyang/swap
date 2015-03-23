<?php static::link_script('jquery.js'); ?>
<?php static::link_script('jquery.form.js'); ?>

<div id="page">
    <form id="edit_form" action="<?php static::php_url('post/edit?id=' . $post->id); ?>" method="POST" enctype="application/x-www-form-urlencoded">
        <p id="edit_form_tips"></p>
        <?php static::csrf_field('member'); ?>
        <p><span>类别</span><select name="category_id">
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category->id; ?>"<?php if ($category->id === $post->category_id): ?> selected="selected"<?php endif; ?>><?php echo $category->name; ?></option>
            <?php endforeach; ?>
        </select></p>
        <p><label for="edit_form_title">标题</label><input type="text" id="edit_form_title" name="title" value="<?php echo $post->title; ?>"></p>
        <p><label for="edit_form_content">内容</label><textarea id="edit_form_content" name="content" cols="64" rows="12"><?php echo $post->content; ?></textarea></p>
        <p><label for="edit_form_tags">标签</label><input type="text" id="edit_form_tags" name="tags" value="<?php echo $post->tags; ?>"><span>多个标签之间使用英文逗号(,)分隔</span></p>
        <p><input type="submit" id="edit_form_submit" name="submit" value="修改"></p>
    </form>
</div>
