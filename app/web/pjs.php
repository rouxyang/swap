<?php
/**
 * PJS 请求入口
 */
require_once __DIR__ . '/../path.php';
if (is_readable(kern\root_dir . '/close.php')) {
    header('Content-Type: application/javascript');
    exit('');
}
require_once kern\kern_dir . '/kern.php';
kern\framework::serve_pjs_request();
