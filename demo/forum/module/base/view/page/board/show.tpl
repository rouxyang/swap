<div id="page">
    <p>
        <a href="<?php static::php_url('site/index'); ?>">论坛首页</a> &gt;
        <a href="<?php static::php_url('board/show?id=' . $board->id); ?>"><?php echo $board->name; ?></a>
    </p>
    <p>
        <a href="<?php static::php_url('topic/new?board_id=' . $board->id); ?>">发新主题</a>
    </p>
    <table>
        <thead>
            <tr>
                <th>主题</th>
                <th>回复数</th>
                <th>作者</th>
                <th>最新回复</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($topics as $topic): ?>
                <tr>
                    <td><a href="<?php static::php_url('topic/show?id=' . $topic->id); ?>"><?php echo $topic->title; ?></a></td>
                    <td><?php echo $topic->reply_count; ?></td>
                    <td><a href="<?php static::php_url('user/show?id=' . $topic->user->id); ?>"><?php echo $topic->user->name; ?></a></td>
                    <td>
                        <?php if ($topic->last_post_user === ''): ?>
                            -
                        <?php else: ?>
                            <?php echo date('Y-m-d H:i:s', $topic->last_post_time); ?><br>
                            <a href="<?php static::php_url(array('user/show', array('name' => $topic->last_post_user))); ?>"><?php echo $topic->last_post_user; ?></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <?php static::block('pager'); ?>
</div>
