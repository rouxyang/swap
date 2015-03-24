<?php
define('kern\root_dir',       DIRECTORY_SEPARATOR === '\\' ? str_replace('\\', '/', __DIR__) : __DIR__);
define('kern\kern_dir',       kern\root_dir . '/../../kern');
define('kern\config_dir',     kern\root_dir . '/config');
define('kern\controller_dir', kern\root_dir . '/controller');
define('kern\run_dir',        kern\root_dir . '/run');
define('kern\filter_dir',     kern\root_dir . '/filter');
define('kern\helper_dir',     kern\root_dir . '/helper');
define('kern\utility_dir',    kern\root_dir . '/utility');
define('kern\vendor_dir',     kern\root_dir . '/vendor');
define('kern\logic_dir',      kern\root_dir . '/logic');
define('kern\view_dir',       kern\root_dir . '/view');
define('kern\web_dir',        kern\root_dir . '/web');
