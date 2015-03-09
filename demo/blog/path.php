<?php
define('swap\root_dir',       DIRECTORY_SEPARATOR === '\\' ? str_replace('\\', '/', __DIR__) : __DIR__);
define('swap\swap_dir',       swap\root_dir . '/../../swap');
define('swap\setting_dir',    swap\root_dir . '/setting');
define('swap\controller_dir', swap\root_dir . '/controller');
define('swap\run_dir',        swap\root_dir . '/run');
define('swap\filter_dir',     swap\root_dir . '/filter');
define('swap\helper_dir',     swap\root_dir . '/helper');
define('swap\utility_dir',    swap\root_dir . '/utility');
define('swap\third_dir',      swap\root_dir . '/third');
define('swap\logic_dir',      swap\root_dir . '/logic');
define('swap\view_dir',       swap\root_dir . '/view');
define('swap\web_dir',        swap\root_dir . '/web');
