<div class="block_nav">

    <!--<h2>搜索</h2>
    <form action="<?php static::php_url('site/search'); ?>" method="get">
        <ul>
            <li><input type="text" name="keyword"></li>
            <li><input type="submit" value="站内搜索"></li>
        </ul>
    </form>-->
    
    <h2>分类</h2>
    <ul>
        <?php foreach ($_categories as $_category): ?>
            <li><a href="<?php static::php_url('post/index?category_id=' . $_category->id); ?>"><?php echo $_category->name; ?></a> (<?php echo $_category->post_count; ?>)</li>
        <?php endforeach; ?>
    </ul>
    
    <h2>标签</h2>
    <ul>
        <?php if ($_tags === []): ?>
        
            <li>暂无标签</li>
            
        <?php else: ?>
        
            <?php foreach ($_tags as $_tag): ?>
                <li><a href="<?php static::php_url(array('post/index', array('tag' => $_tag->name))); ?>"><?php echo $_tag->name; ?></a>(<?php echo $_tag->refer_count; ?>)</li>
            <?php endforeach; ?>
            
        <?php endif; ?>
    </ul>
    
    <h2>评论</h2>
    <ul>
        <?php if ($_comments === []): ?>
        
            <li>暂无评论</li>
            
        <?php else: ?>
        
            <?php foreach ($_comments as $_comment): ?>
                <li><a href="<?php static::php_url('post/show?id=' . $_comment->post->id); ?>#<?php echo $_comment->id; ?>"><?php echo $_comment->author; ?> 评论了 <?php echo $_comment->post->title; ?></a></li>
            <?php endforeach; ?>
            
        <?php endif; ?>
    </ul>
    
    <?php if ($_links !== null): ?>
    
        <h2>链接</h2>
        <ul>
            <?php foreach ($_links as $_link): ?>
                <li><a href="<?php echo $_link->url; ?>"><?php echo $_link->name; ?></a></li>
            <?php endforeach; ?>
        </ul>
        
    <?php endif; ?>

</div>
