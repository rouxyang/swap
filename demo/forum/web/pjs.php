<?php
require_once __DIR__ . '/../path.php';
if (is_readable(swap\root_dir . '/close.php')) {
    header('Content-Type: application/javascript');
    exit('');
}
require_once swap\swap_dir . '/swap.php';
swap\framework::serve_pjs_request();
