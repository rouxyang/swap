<?php static::link_script('jquery.js'); ?>
<?php static::link_script('jquery.form.js'); ?>

<div id="page">

    <div class="about">
        <h2 class="title">关于我</h2>
        <div class="article"><?php show_text($about); ?></div>
    </div>
    
    <ul class="messages">
        <?php if ($messages === []): ?>
        
            <li class="message">暂无留言</li>
            
        <?php else: ?>
        
            <?php foreach ($messages as $message): ?>
                <li class="message">
                    <p class="guest"><?php if ($message->site === ''): ?><?php echo $message->author; ?><?php else: ?><a href="<?php echo $message->site; ?>"><?php echo $message->author; ?></a><?php endif; ?><?php if ($logined): ?>(<?php echo $message->email; ?>)<?php endif; ?> 留言于 <?php echo date('Y-m-d H:i:s', $message->pub_time); ?> (<?php echo timezone(); ?>)</p>
                    <div class="content"><?php show_ubb($message->content); ?></div>
                    <?php if ($logined): ?>
                        <ul class="admin">
                            <li><a href="<?php static::php_url('message/edit?id=' . $message->id); ?>">编辑</a></li>
                            <li><a href="<?php static::csrf_url('member', 'message/delete?id=' . $message->id); ?>" onclick="return delete_message(this);">删除</a></li>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
            
        <?php endif; ?>
    </ul>
    
    <?php static::block('pager'); ?>
        
    <form id="new_form" action="<?php static::php_url('message/new'); ?>" method="POST" enctype="application/x-www-form-urlencoded">
        <h2>添加留言</h2>
        <p id="new_form_tips"></p>
        <p><label for="new_form_author">昵称</label><input type="text" id="new_form_author" maxlength="16" name="author"><span>您的大名</span></p>
        <p><label for="new_form_email">邮箱</label><input type="text" id="new_form_email" maxlength="64" name="email"><span>方便与您联系 :) 请放心，我会为您的邮箱保密</span></p>
        <p><label for="new_form_site">网站</label><input type="text" id="new_form_site" maxlength="128" name="site"><span>如果您有个人网站，可以填写，方便我们联系</span></p>
        <p><label for="new_form_captcha">验证码</label><input type="text" id="new_form_captcha" name="captcha"><span><?php echo $captcha_question; ?></span></p>
        <p><label for="new_form_content">内容</label><textarea id="new_form_content" name="content"></textarea><span>您想说的</span></p>
        <p><input type="submit" id="new_form_submit" name="submit" value="确定"></p>
    </form>
    
</div>
