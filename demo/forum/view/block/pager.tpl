<?php
/**
 * 接口变量：
 *
 *  target       - 目标标志
 *  record_count - 总记录数
 *  page_count   - 总页数
 *  current_page - 当前页号
 *  page_size    - 每页记录数
 */
extract($pager);
$target = new swap\target(r($target));
$page_url = function ($page) use ($target) {
    $target->set_param('page', $page);
    swap\router::build_php_url($target, true, true);
};
$current_page = (int)$current_page;
?>
<div class="block_pager">
    <ul>
        
        <?php if ($current_page > 1): ?>
            <li><a href="<?php $page_url($current_page - 1); ?>">上一页</a></li>
        <?php endif; ?>
        
        <?php if ($page_count < 8): ?>
        
            <?php for ($i = 1; $i <= $page_count; $i++): ?>
                <?php if ($i === $current_page): ?>
                    <li><a href="<?php $page_url($i); ?>" class="current"><?php echo $i; ?></a></li>
                <?php else: ?>
                    <li><a href="<?php $page_url($i); ?>"><?php echo $i; ?></a></li>
                <?php endif; ?>
            <?php endfor; ?>
            
        <?php else: ?>
        
            <?php if ($current_page <= 5): ?>
            
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <?php if ($i == $current_page): ?>
                        <li><a href="<?php $page_url($i); ?>" class="current"><?php echo $i; ?></a></li>
                    <?php else: ?>
                        <li><a href="<?php $page_url($i); ?>"><?php echo $i; ?></a></li>
                    <?php endif; ?>
                <?php endfor; ?>
                <li>...</li>
                <li><a href="<?php $page_url($page_count); ?>"><?php echo $page_count; ?></a></li>
                
            <?php elseif ($current_page >= $page_count - 4): ?>
            
                <li><a href="<?php $page_url(1); ?>">1</a></li>
                <li>...</li>
                <?php for ($i = $page_count - 5; $i <= $page_count; $i++): ?>
                    <?php if ($i == $current_page): ?>
                        <li><a href="<?php $page_url($i); ?>" class="current"><?php echo $i; ?></a></li>
                    <?php else: ?>
                        <li><a href="<?php $page_url($i); ?>"><?php echo $i; ?></a></li>
                    <?php endif; ?>
                <?php endfor; ?>
                
            <?php else: ?>
            
                <li><a href="<?php $page_url(1); ?>">1</a></li>
                <li>...</li>
                <?php for ($i = $current_page - 2; $i <= $current_page + 2; $i++): ?>
                    <?php if ($i == $current_page): ?>
                        <li><a href="<?php $page_url($i); ?>" class="current"><?php echo $i; ?></a></li>
                    <?php else: ?>
                        <li><a href="<?php $page_url($i); ?>"><?php echo $i; ?></a></li>
                    <?php endif; ?>
                <?php endfor; ?>
                <li>...</li>
                <li><a href="<?php $page_url($page_count); ?>"><?php echo $page_count; ?></a></li>
                
            <?php endif; ?>
            
        <?php endif; ?>
        
        <?php if ($current_page < $page_count): ?>
            <li><a href="<?php $page_url($current_page + 1); ?>">下一页</a></li>
        <?php endif; ?>
    
    </ul>
</div>
