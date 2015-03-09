<?php
use swap\framework;
require_once __DIR__ . '/../../path.php';
require_once swap\swap_dir . '/swap.php';
framework::init_cli_environment();
// 至此，已经在命令行下进入框架。接下来在这下面写你的代码
// 比如，输出某个配置项
echo swap\setting::get_swap('secret_key') . "\n";
