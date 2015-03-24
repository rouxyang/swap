<?php
/**
 * Web 应用程序各类组件所在的位置定义。可以任意安排各组件的位置。
 */
# Web 应用根目录
define('kern\root_dir',    DIRECTORY_SEPARATOR === '\\' ? str_replace('\\', '/', __DIR__) : __DIR__);
# 框架核心
define('kern\kern_dir',    kern\root_dir . '/../kern');
# 配置文件
define('kern\config_dir',  kern\root_dir . '/config');
# 相关数据
define('kern\data_dir',    kern\root_dir . '/data');
# 通用库
define('kern\library_dir', kern\root_dir . '/library');
# 业务逻辑
define('kern\logic_dir',   kern\root_dir . '/logic');
# 各个模块
define('kern\module_dir',  kern\root_dir . '/module');
# 三方库
define('kern\vendor_dir',  kern\root_dir . '/vendor');
# Web 应用入口
define('kern\web_dir',     kern\root_dir . '/web');
