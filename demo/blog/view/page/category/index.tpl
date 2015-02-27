<?php static::link_script('jquery.js'); ?>
<?php static::link_script('jquery.form.js'); ?>

<div id="page">

    <form id="new_form" action="<?php static::php_url('category/new'); ?>" method="POST" enctype="application/x-www-form-urlencoded">
        <p id="new_form_tips"></p>
        <?php static::csrf_field('member'); ?>
        <p><label for="new_form_name">分类名</label><input type="text" id="new_form_name" name="name"></p>
        <p><input type="submit" id="new_form_submit" name="submit" value="提交"></p>
    </form>
    
    <?php if ($categories === []): ?>
        
        <p>暂时没有分类。</p>
        
    <?php else: ?>
        
        <ul>
            <?php foreach ($categories as $category): ?>
                <li><?php echo $category->name; ?>
                | <a href="<?php static::php_url('category/edit?id=' . $category->id); ?>">修改</a>
                <?php if ($category->can_be_deleted()): ?>
                    | <a href="<?php static::csrf_url('member', 'category/delete?id=' . $category->id); ?>" onclick="return delete_category(this);">删除</a>
                <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        
    <?php endif; ?>
</div>
