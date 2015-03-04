<?php
define('swap\root_dir',       DIRECTORY_SEPARATOR === '\\' ? str_replace('\\', '/', __DIR__) : __DIR__);
define('swap\swap_dir',       swap\root_dir . '/../../swap');
define('swap\setting_dir',    swap\root_dir . '/setting');
define('swap\controller_dir', swap\root_dir . '/controller');
define('swap\data_dir',       swap\root_dir . '/data');
define('swap\filter_dir',     swap\root_dir . '/filter');
define('swap\helper_dir',     swap\root_dir . '/helper');
define('swap\library_dir',    swap\root_dir . '/library');
define('swap\third_dir',      swap\root_dir . '/third');
define('swap\logic_dir',      swap\root_dir . '/logic');
define('swap\view_dir',       swap\root_dir . '/view');
define('swap\web_dir',        swap\root_dir . '/web');
