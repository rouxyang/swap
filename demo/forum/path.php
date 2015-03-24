<?php
define('kern\root_dir',    DIRECTORY_SEPARATOR === '\\' ? str_replace('\\', '/', __DIR__) : __DIR__);
define('kern\kern_dir',    kern\root_dir . '/../../kern');
define('kern\config_dir',  kern\root_dir . '/config');
define('kern\data_dir',    kern\root_dir . '/data');
define('kern\library_dir', kern\root_dir . '/library');
define('kern\logic_dir',   kern\root_dir . '/logic');
define('kern\module_dir',  kern\root_dir . '/module');
define('kern\vendor_dir',  kern\root_dir . '/vendor');
define('kern\web_dir',     kern\root_dir . '/web');
