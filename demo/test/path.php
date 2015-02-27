<?php
define('swap\root_dir',       DIRECTORY_SEPARATOR === '\\' ? str_replace('\\', '/', __DIR__) : __DIR__);
define('swap\swap_dir',       swap\root_dir . '/../../swap');
define('swap\controller_dir', swap\root_dir . '/controller');
