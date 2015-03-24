<?php
define('kern\root_dir',   DIRECTORY_SEPARATOR === '\\' ? str_replace('\\', '/', __DIR__) : __DIR__);
define('kern\kern_dir',   kern\root_dir . '/../../kern');
define('kern\module_dir', kern\root_dir . '/module');
