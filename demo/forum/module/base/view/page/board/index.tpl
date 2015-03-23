<div id="page">
    <table>
        <thead>
            <tr>
                <th>板块名称</th>
                <th>主题数</th>
                <th>回复数</th>
                <th>最新帖子</th>
                <th>管理员</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($boards as $board): ?>
                <tr>
                    <td>
                        <a href="<?php static::php_url('board/show?id=' . $board->id); ?>"><?php echo $board->name; ?></a>
                        <br>
                        <?php echo $board->description; ?>
                    </td>
                    <td><?php echo $board->topic_count; ?></td>
                    <td><?php echo $board->reply_count; ?></td>
                    <td>
                        <?php if ($board->last_post_user === ''): ?>
                            -
                        <?php else: ?>
                            <?php echo date('Y-m-d H:i:s', $board->last_post_time); ?><br><a href="<?php static::php_url(array('user/show', array('name' => $board->last_post_user))); ?>"><?php echo $board->last_post_user; ?></a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php foreach ($board->manager as $manager): ?>
                            <a href="<?php static::php_url('user/show?id=' . $manager->id); ?>"><?php echo $manager->name; ?></a>
                        <?php endforeach; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p>在线用户数：<?php echo $online_count; ?></p>
</div>
