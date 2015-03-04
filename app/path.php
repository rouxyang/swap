<?php
/**
 * Web 应用程序各类组件所在的位置定义。可以任意安排各组件的位置。
 */
# Web 应用根目录
define('swap\root_dir',       DIRECTORY_SEPARATOR === '\\' ? str_replace('\\', '/', __DIR__) : __DIR__);
# 框架核心目录
define('swap\swap_dir',       swap\root_dir . '/../swap');
# 配置文件
define('swap\setting_dir',    swap\root_dir . '/setting');
# 控制器
define('swap\controller_dir', swap\root_dir . '/controller');
# 相关数据
define('swap\data_dir',       swap\root_dir . '/data');
# 过滤器
define('swap\filter_dir',     swap\root_dir . '/filter');
# 控制器助手
define('swap\helper_dir',     swap\root_dir . '/helper');
# 通用库
define('swap\library_dir',    swap\root_dir . '/library');
# 三方库
define('swap\third_dir',      swap\root_dir . '/third');
# 业务逻辑
define('swap\logic_dir',      swap\root_dir . '/logic');
# 领域模型
define('swap\model_dir',      swap\logic_dir . '/model');
# 领域服务
define('swap\service_dir',    swap\logic_dir . '/service');
# 视图文件
define('swap\view_dir',       swap\root_dir . '/view');
# Web 应用公共目录
define('swap\web_dir',        swap\root_dir . '/web');
