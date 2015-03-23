<?php static::link_script('jquery.js'); ?>
<?php static::link_script('jquery.form.js'); ?>

<div id="page">

    <div class="post">
        <h2 class="title"><a href="<?php static::php_url('post/show?id=' . $post->id); ?>"><?php echo $post->title; ?></a></h2>
        <p class="author"><?php echo $post->member->name; ?> 发表于 <?php echo date('Y-m-d H:i:s', $post->pub_time); ?> (<?php echo timezone(); ?>)</p>
        <div class="article"><?php show_ubb($post->content); ?></div>
        <ul class="meta">
            <li>类型：<a href="<?php static::php_url('post/index?category_id=' . $post->category->id); ?>"><?php echo $post->category->name; ?></a></li>
            <li>阅读：<?php echo $post->read_count; ?></li>
            <li>评论：<?php echo $post->comment_count; ?></li>
            <li>标签：
            <?php foreach ($post->post_tag as $tag): /* 遍历所有标签 */ ?>
                <a href="<?php static::php_url(array('post/index', array('tag' => $tag->name))); ?>"><?php echo $tag->name; ?></a>
            <?php endforeach; ?></li>
        </ul>
        <?php if ($logined): /* 如果是管理员 */ ?>
            <ul class="admin">
                <li><a href="<?php static::php_url('post/edit?id=' . $post->id); ?>">编辑</a></li>
                <li><a href="<?php static::csrf_url('member', 'post/delete?id=' . $post->id); ?>" onclick="return delete_post(this);">删除</a></li>
            </ul>
        <?php endif; ?>
    </div>
    
    <ul class="comments">
        <?php if ($comments === []): /* 如果没有评论 */ ?>
        
            <li>暂无评论</li>
            
        <?php else: /* 如果有评论 */ ?>
        
            <?php foreach ($comments as $comment): /* 遍历所有评论 */ ?>
            
                <li class="comment" id="<?php echo $comment->id; ?>">
                    <p class="commenter"><?php if ($comment->site === ''): ?><?php echo $comment->author; ?><?php else: ?><a href="<?php echo $comment->site; ?>"><?php echo $comment->author; ?></a><?php endif; ?><?php if ($logined): ?>(<?php echo $comment->email; ?>)<?php endif; ?> 评论于 <?php echo date('Y-m-d H:i:s', $comment->pub_time); ?> (<?php echo timezone(); ?>)</p>
                    <div class="content"><?php show_ubb($comment->content); ?></div>
                    <?php if ($logined): /* 如果是管理员 */ ?>
                        <ul class="admin">
                            <li><a href="<?php static::php_url('comment/edit?id=' . $comment->id); ?>">编辑</a></li>
                            <li><a href="<?php static::csrf_url('member', 'comment/delete?id=' . $comment->id); ?>" onclick="return delete_comment(this);">删除</a></li>
                        </ul>
                    <?php endif; ?>
                </li>
                
            <?php endforeach; ?>
            
        <?php endif; ?>
    </ul>
    
    <?php static::block('pager'); ?>
    
    <form id="new_form" action="<?php static::php_url('comment/new?post_id=' . $post->id); ?>" method="POST" enctype="application/x-www-form-urlencoded">
        <h2>添加评论</h2>
        <p id="new_form_tips"></p>
        <p><label for="new_form_author">昵称</label><input type="text" id="new_form_author" maxlength="16" name="author"><span>您的大名</span></p>
        <p><label for="new_form_email">邮箱</label><input type="text" id="new_form_email" maxlength="64" name="email"><span>方便与您联系 :) 请放心，我会为您的邮箱保密</span></p>
        <p><label for="new_form_site">网站</label><input type="text" id="new_form_site" maxlength="128" name="site"><span>如果您有个人网站，可以填写，方便我们联系</span></p>
        <p><label for="new_form_captcha">验证码</label><input type="text" id="new_form_captcha" name="captcha"><span><?php echo $captcha_question; ?></span></p>
        <p><label for="new_form_content">内容</label><textarea id="new_form_content" name="content"></textarea><span>您的大作</span></p>
        <p><input type="submit" id="new_form_submit" name="submit" value="确定"></p>
    </form>
    
</div>
