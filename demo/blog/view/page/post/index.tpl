<?php static::link_script('jquery.js'); ?>

<div id="page">
    <?php if ($posts === []): /* 无日志 */ ?>

        暂无文章
        
    <?php else: /* 有日志 */ ?>

        <ul>
            <?php foreach ($posts as $post): /* 遍历每条日志 */ ?>
            
                <li class="post">
                    <h2 class="title"><a href="<?php static::php_url('post/show?id=' . $post->id); ?>"><?php echo $post->title; ?></a></h2>
                    <p class="author"><?php echo $post->member->name; ?> 发表于 <?php echo date('Y-m-d H:i:s', $post->pub_time); ?> (<?php echo timezone(); ?>)</p>
                    <div class="article"><?php show_ubb($post->content); ?></div>
                    <ul class="meta">
                        <li>类型：<a href="<?php static::php_url('post/index?category_id=' . $post->category->id); ?>"><?php echo $post->category->name; ?></a></li>
                        <li>阅读：<?php echo $post->read_count; ?></li>
                        <li>评论：<?php echo $post->comment_count; ?></li>
                        <li>标签：
                        <?php foreach ($post->post_tag as $tag): /* 遍历每个标签 */ ?>
                            <a href="<?php static::php_url(array('post/index', array('tag' => $tag->name))); ?>"><?php echo $tag->name; ?></a>
                        <?php endforeach; ?></li>
                    </ul>
                    <?php if ($logined): /* 如果为管理员 */ ?>
                        <ul class="admin">
                            <li><a href="<?php static::php_url('post/edit?id=' . $post->id); ?>">编辑</a></li>
                            <li><a href="<?php static::csrf_url('member', 'post/delete?id=' . $post->id); ?>" onclick="return delete_post(this);">删除</a></li>
                        </ul>
                    <?php endif; ?>
                </li>
                
            <?php endforeach; ?>
        </ul>
        <?php static::include_block('pager'); /* 分页条 */ ?>
        
    <?php endif; ?>
</div>
