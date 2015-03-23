<?php
/**
 * Web 应用程序各类组件所在的位置定义。可以任意安排各组件的位置。
 */
# Web 应用根目录
define('kern\root_dir',       DIRECTORY_SEPARATOR === '\\' ? str_replace('\\', '/', __DIR__) : __DIR__);
# 框架核心目录
define('kern\kern_dir',       kern\root_dir . '/../kern');
# 配置文件
define('kern\config_dir',     kern\root_dir . '/config');
# 控制器
define('kern\controller_dir', kern\root_dir . '/controller');
# 相关数据
define('kern\run_dir',        kern\root_dir . '/run');
# 过滤器
define('kern\filter_dir',     kern\root_dir . '/filter');
# 控制器助手
define('kern\helper_dir',     kern\root_dir . '/helper');
# 通用库
define('kern\utility_dir',    kern\root_dir . '/utility');
# 三方库
define('kern\third_dir',      kern\root_dir . '/third');
# 业务逻辑
define('kern\logic_dir',      kern\root_dir . '/logic');
# 视图文件
define('kern\view_dir',       kern\root_dir . '/view');
# Web 应用公共目录
define('kern\web_dir',        kern\root_dir . '/web');
