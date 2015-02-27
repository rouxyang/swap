<?php
/**
 * PHP 请求入口
 */
require_once __DIR__ . '/../path.php';
$close_file = swap\root_dir . '/close.php';
if (is_readable($close_file)) {
    require $close_file;
    exit();
}
require_once swap\swap_dir . '/swap.php';
swap\framework::serve_php_request();
