<?php
/**
 * 框架的基本功能与控制
 *
 * @copyright Copyright (c) 2009-2014 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
// 框架版本
const version = 'current';
// [实体] 框架门面
class framework {
    public static function /* @index.php */ serve_php_request() {
        self::$serve_mode = self::php_mode;
        try {
            self::init_web_environment();
            dispatcher::dispatch_php();
            self::send_php_response();
        } catch (visitor_except $e) {
            self::show_exception($e);
        }
    }
    protected static function init_web_environment() {
        self::init_swap_environment();
        ob_start(); # 确保不会在无意中 echo 出内容
        ob_implicit_flush(false); # 不许隐式输出内容
    }
    protected static function init_swap_environment() {
        clock::stop();
        config::load();
        self::$log_execute_time = config::get_swap('log_execute_time', true);
        if (self::$log_execute_time) {
            self::$begin_microtime = clock::get_micro_stamp();
        }
        clock::set_timezone(config::get_swap('time_zone', 'Asia/Shanghai'));
        i18n::set_locale(config::get_swap('locale', 'en_us'));
        self::$is_debug = config::get_swap('is_debug', false);
        ini_set('display_errors', config::get_swap('display_errors', self::$is_debug));
        set_exception_handler([__CLASS__, 'exception_handler']);
        $error_reporting = config::get_swap('error_reporting', self::$is_debug ? E_ALL | E_STRICT : E_ALL & ~E_NOTICE);
        set_error_handler([__CLASS__, 'error_handler'], $error_reporting);
        logger::init();
        loader::init();
    }
    protected static function send_php_response() {
        ob_end_clean(); # 丢弃开发者无意间 echo 出的内容
        visitor::persist_roles();
        $response = visitor::get_response();
        $status = $response['status'];
        if ($status['code'] !== 0) {
            header('HTTP/1.1 ' . $status['code'] . ' ' . $status['phrase']);
        }
        foreach ($response['headers'] as $key => $value) {
            header($key . ': ' . $value);
        }
        foreach ($response['cookies'] as $c) {
            setcookie($c['name'], $c['value'], $c['expire'], $c['path'], $c['domain'], $c['secure'], $c['http_only']);
        }
        $content_type = $response['content_type'];
        self::send_response(false, $content_type, $response['content'], $content_type === 'text/html; charset=utf-8');
    }
    protected static function send_response($is_pps_mode, $content_type, $content, $gzippable = true) {
        header('Content-Type: ' . $content_type);
        if (config::get_swap('send_x_powered_by', true)) {
            header('X-Powered-By: swap-' . version);
        }
        if ($is_pps_mode) {
            if (config::get_module('view.cache_pps_in_client', false)) {
                header('Cache-Control: max-age=2592000');
            } else {
                header('Cache-Control: max-age=0');
            }
        }
        if (self::$log_execute_time) {
            $execute_time = microtime(true) - self::$begin_microtime;
            header('X-Execute-Time: ' . substr($execute_time * 1000, 0, 5) . ' ms');
        }
        if ($gzippable && extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }
        echo $content;
        ob_end_flush();
        if (self::$is_debug) {
            debug::save_required_files();
        }
    }
    protected static function show_exception(\Exception $e = null) {
        if (self::is_cli_mode()) {
            echo $e->getMessage();
        } else {
            ob_end_clean(); # 丢弃开发者无意间 echo 出的内容
            header('Content-Type: text/html; charset=utf-8');
            $_tpl_file = swap_dir . '/server/view/except/' . $e->getCode() . '.tpl';
            if (!is_readable($_tpl_file)) {
                $_tpl_file = swap_dir . '/server/view/except/500.tpl';
            }
            require $_tpl_file;
        }
    }
    public static function /* @pss.php */ serve_pss_request() {
        self::serve_pps_request(self::pss_mode);
    }
    public static function /* @pjs.php */ serve_pjs_request() {
        self::serve_pps_request(self::pjs_mode);
    }
    protected static function serve_pps_request($serve_mode) {
        self::$serve_mode = $serve_mode;
        try {
            self::init_web_environment();
            dispatcher::dispatch_pps();
            self::send_pps_response();
        } catch (visitor_except $e) {}
    }
    protected static function send_pps_response() {
        ob_end_clean(); # 丢弃开发者无意间 echo 出的内容
        $response = visitor::get_response();
        $content_type = self::$serve_mode === self::pss_mode ? 'text/css; charset=utf-8' : 'application/javascript';
        self::send_response(true, $content_type, $response['content'], true);
    }
    public static function /* @cli */ init_cli_environment() {
        self::$serve_mode = self::cli_mode;
        self::init_swap_environment();
    }
    public static function is_debug() {
        return self::$is_debug;
    }
    const /* @swap */ php_mode = 'php';
    const /* @swap */ pss_mode = 'pss';
    const /* @swap */ pjs_mode = 'pjs';
    const /* @swap */ cli_mode = 'cli';
    public static function /* @swap */ is_php_mode() {
        return self::$serve_mode === self::php_mode;
    }
    public static function /* @swap */ is_pss_mode() {
        return self::$serve_mode === self::pss_mode;
    }
    public static function /* @swap */ is_pjs_mode() {
        return self::$serve_mode === self::pjs_mode;
    }
    public static function /* @swap */ is_pps_mode() {
        return self::$serve_mode === self::pss_mode || self::$serve_mode === self::pjs_mode;
    }
    public static function /* @swap */ is_cli_mode() {
        return self::$serve_mode === self::cli_mode;
    }
    public static function /* @swap */ get_serve_mode() {
        return self::$serve_mode;
    }
    public static function /* @swap */ error_handler($error_level, $error_msg, $error_file, $error_line, array $error_context) {
        if (error_reporting() !== 0) {
            $error = new developer_error($error_msg, 500, $error_level, $error_file, $error_line);
            $error->set_context($error_context);
            throw $error;
        }
    }
    public static function /* @swap */ exception_handler(\Exception $e) {
        if (config::get_swap('log_errors', true)) {
            $msg = $e->getMessage() . ' in file: ' . $e->getFile() . ' in line: ' . $e->getLine();
            if (config::get_swap('log_with_trace', true)) {
                $msg .= ' with trace: ' . var_export($e->getTrace(), true);
            }
            logger::log_error($msg);
        }
        self::show_exception($e);
    }
    protected static $serve_mode = self::php_mode;
    protected static $is_debug = false;
    protected static $log_execute_time = false;
    protected static $begin_microtime = 0.000;
}
// [实体] 当前时钟
class clock {
    public static function set_timezone($timezone) {
        date_default_timezone_set($timezone);
    }
    public static function get_timezone() {
        return date_default_timezone_get();
    }
    public static function get($format = 'Y-m-d H:i:s') {
        return date($format, (int)self::$micro_stamp);
    }
    public static function get_date() {
        return date('Y-m-d', (int)self::$micro_stamp);
    }
    public static function get_time() {
        return date('H:i:s', (int)self::$micro_stamp);
    }
    public static function get_datetime() {
        return date('Y-m-d H:i:s', (int)self::$micro_stamp);
    }
    public static function get_stamp() {
        return (int)self::$micro_stamp;
    }
    public static function get_micro_stamp() {
        return self::$micro_stamp;
    }
    public static function /* @swap */ stop() {
        self::$micro_stamp = microtime(true);
    }
    protected static $micro_stamp = 0.000;
}
// [实体] 配置参数维护器
class config {
    public static function get_swap($key, $default_value = null) {
        return self::get('swap.' . $key, $default_value);
    }
    public static function get_base($key, $default_value = null) {
        return self::get('base.' . $key, $default_value);
    }
    public static function get_logic($key, $default_value = null) {
        return self::get('logic.' . $key, $default_value);
    }
    public static function get_module($key, $default_value = null) {
        if (self::$module_name === null) {
            throw new developer_error("you cannot use swap\config::get_module() when module_name is not set");
        }
        if (isset(self::$configs['modules'][self::$module_name])) {
            $value = self::get('modules.' . self::$module_name . '.' . $key, null);
            if ($value !== null) {
                return $value;
            }
        }
        return self::get_base($key, $default_value);
    }
    public static function get_third($key, $default_value = null) {
        return self::get('third.' . $key, $default_value);
    }
    public static function set_swap($key, $value) {
        self::set('swap.' . $key, $value);
    }
    public static function set_logic($key, $value) {
        self::set('logic.' . $key, $value);
    }
    public static function set_base($key, $value) {
        self::set('base.' . $key, $value);
    }
    public static function set_module($key, $value) {
        if (self::$module_name === null) {
            throw new developer_error("you cannot use swap\config::set_module() when module_name is not set");
        }
        self::set('modules.' . self::$module_name . '.' . $key, $value);
    }
    public static function set_third($key, $value) {
        self::set('third.' . $key, $value);
    }
    public static function /* @swap */ load() {
        if (defined('swap\config_dir')) {
            self::$configs = self::load_in_dir(config_dir);
            if (array_key_exists('modules', self::$configs)) {
                self::$module_names = array_keys(self::$configs['modules']);
            }
        }
        if (defined('swap\third_dir')) {
            self::$configs['third'] = self::load_in_dir(third_dir . '/config');
        }
        if (defined('swap\logic_dir')) {
            self::$configs['logic'] = self::load_in_dir(logic_dir . '/config');
        }
    }
    public static function /* @swap */ set_module_name($module_name) {
        self::$module_name = $module_name;
    }
    public static function /* @swap */ get_module_name() {
        return self::$module_name;
    }
    public static function /* @swap */ get_module_names() {
        return self::$module_names;
    }
    protected static function get($key, $default_value = null) {
        $value = self::$configs;
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            foreach ($keys as $next_key) {
                if (!array_key_exists($next_key, $value)) {
                    return $default_value;
                }
                $value = $value[$next_key];
            }
        } else if (!array_key_exists($key, $value)) {
            $value = $default_value;
        } else {
            $value = $value[$key];
        }
        return $value;
    }
    protected static function set($key, $value) {
        $configs =& self::$configs;
        if (in_string('.', $key)) {
            $keys = explode('.', $key);
            $last_key = array_pop($keys);
            foreach ($keys as $next_key) {
                if (!array_key_exists($next_key, $configs)) {
                    $configs[$next_key] = [];
                }
                $configs =& $configs[$next_key];
            }
            $configs[$last_key] = $value;
        } else {
            $configs[$key] = $value;
        }
    }
    protected static function load_in_dir($dir) {
        $configs = [];
        $global_file = $dir . '/global.php';
        if (is_readable($global_file)) {
            $configs = require $global_file;
            $local_file = $dir . '/local.php';
            if (is_readable($local_file)) {
                $local_configs = require $local_file;
                if (is_array($local_configs)) {
                    $configs = array_replace_recursive($configs, $local_configs);
                }
            }
        }
        return $configs;
    }
    protected static $configs = [];
    protected static $module_names = [];
    // 可更改
    protected static $module_name = null;
}
// [实体] 组件加载器
class loader {
    public static function load_library($file) {
        self::load_file(library_dir . '/' . $file);
    }
    public static function load_third($file) {
        self::load_file(third_dir . '/' . ltrim($file, '/'));
    }
    public static function load_file($_file) {
        require_once $_file;
    }
    public static function /* @swap */ init() {
        $include_pathes = implode(PATH_SEPARATOR, config::get_swap('include_pathes', []));
        if ($include_pathes !== '') {
            set_include_path(get_include_path() . PATH_SEPARATOR . $include_pathes);
        }
        spl_autoload_register(['swap\loader', 'load_class']);
    }
    public static function /* @swap */ load_class($class_name) {
        if (strpos($class_name, 'swap\\') === 0) {
            self::load_file(swap_dir . '/' . self::$swap_entities[$class_name]);
        } else if (($last_pos = strrpos($class_name, '\\')) > 0) {
            // PSR-0
            $class_name = ltrim($class_name, '\\');
            $namespace = str_replace('\\', '/', substr($class_name, 0, $last_pos));
            $class_name = str_replace('_', '/', substr($class_name, $last_pos + 1));
            self::load_third($namespace . '/' . $class_name . '.php');
        } else if (in_string('_', $class_name)) {
            if (ends_with('_model', $class_name)) {
                self::load_file(model_dir . '/' . $class_name . '.php');
            } else if (ends_with('_service', $class_name)) {
                self::load_file(service_dir . '/' . $class_name . '.php');
            } else if (ends_with('_filter', $class_name)) {
                $class_file = filter_dir . '/' . $class_name . '.php';
                if (is_readable($class_file)) {
                    self::load_file($class_file);
                }
            }
        }
    }
    protected static $swap_entities = [
        'swap\checker'                 => 'server/checker.php',
        'swap\check_failed'            => 'server/checker.php',
        'swap\lazy_checker'            => 'server/checker.php',
        'swap\greedy_checker'          => 'server/checker.php',
        'swap\instant_checker'         => 'server/checker.php',
        'swap\session'                 => 'server/session/session.php',
        'swap\session_manager'         => 'server/session/session.php',
        'swap\session_store'           => 'server/session/session.php',
        'swap\session_store_pool'      => 'server/session/session.php',
        'swap\memcached_session_store' => 'server/session/stores/memcached.php',
        'swap\redis_session_store'     => 'server/session/stores/redis.php',
        'swap\mysql_session_store'     => 'server/session/stores/mysql.php',
        'swap\pgsql_session_store'     => 'server/session/stores/pgsql.php',
        'swap\sqlite_session_store'    => 'server/session/stores/sqlite.php',
        'swap\pps_rendor'              => 'server/pps_mode.php',
        'swap\tpl_rendor'              => 'server/php_mode.php',
        'swap\controller'              => 'server/php_mode.php',
        'swap\helper'                  => 'server/php_mode.php',
        'swap\context'                 => 'server/php_mode.php',
        'swap\before_filter'           => 'server/php_mode.php',
        'swap\after_filter'            => 'server/php_mode.php',
        'swap\autoload_filter'         => 'server/php_mode.php',
        'swap\action_return'           => 'server/php_mode.php',
        'swap\action_forward'          => 'server/php_mode.php',
        'swap\dispatch_return'         => 'server/rendor.php',
        'swap\dispatcher'              => 'server/rendor.php',
        'swap\rendor'                  => 'server/rendor.php',
        'swap\router'                  => 'server/router.php',
        'swap\target'                  => 'server/router.php',
        'swap\visitor'                 => 'server/visitor.php',
        'swap\binder'                  => 'server/model.php',
        'swap\model_api'               => 'server/model.php',
        'swap\model'                   => 'server/model.php',
        'swap\cache'                   => 'tech/cache/cache.php',
        'swap\cache_pool'              => 'tech/cache/cache.php',
        'swap\filesys_cache'           => 'tech/cache/types/filesys.php',
        'swap\memcached_cache'         => 'tech/cache/types/memcached.php',
        'swap\redis_cache'             => 'tech/cache/types/redis.php',
        'swap\rdb'                     => 'tech/repo/rdb/rdb.php',
        'swap\distributed_rdb'         => 'tech/repo/rdb/rdb.php',
        'swap\rdb_node'                => 'tech/repo/rdb/rdb.php',
        'swap\rdb_node_pool'           => 'tech/repo/rdb/rdb.php',
        'swap\mysql_rdb_node'          => 'tech/repo/rdb/nodes/mysql.php',
        'swap\mysql_master_rdb_node'   => 'tech/repo/rdb/nodes/mysql.php',
        'swap\mysql_slave_rdb_node'    => 'tech/repo/rdb/nodes/mysql.php',
        'swap\mysql_rdb_node_util'     => 'tech/repo/rdb/nodes/mysql.php',
        'swap\pgsql_rdb_node'          => 'tech/repo/rdb/nodes/pgsql.php',
        'swap\pgsql_master_rdb_node'   => 'tech/repo/rdb/nodes/pgsql.php',
        'swap\pgsql_slave_rdb_node'    => 'tech/repo/rdb/nodes/pgsql.php',
        'swap\pgsql_rdb_node_util'     => 'tech/repo/rdb/nodes/pgsql.php',
        'swap\sqlite_rdb_node'         => 'tech/repo/rdb/nodes/sqlite.php',
        'swap\sqlite_master_rdb_node'  => 'tech/repo/rdb/nodes/sqlite.php',
        'swap\sqlite_slave_rdb_node'   => 'tech/repo/rdb/nodes/sqlite.php',
        'swap\sqlite_rdb_node_util'    => 'tech/repo/rdb/nodes/sqlite.php',
        'swap\rdb_conn'                => 'tech/repo/rdb/rdb.php',
        'swap\rdb_result'              => 'tech/repo/rdb/rdb.php',
        'swap\rdb_conn_pool'           => 'tech/repo/rdb/rdb.php',
        'swap\mysql_rdb_conn'          => 'tech/repo/rdb/drivers/mysql.php',
        'swap\mysql_rdb_result'        => 'tech/repo/rdb/drivers/mysql.php',
        'swap\pgsql_rdb_conn'          => 'tech/repo/rdb/drivers/pgsql.php',
        'swap\pgsql_rdb_result'        => 'tech/repo/rdb/drivers/pgsql.php',
        'swap\sqlite_rdb_conn'         => 'tech/repo/rdb/drivers/sqlite.php',
        'swap\sqlite_rdb_result'       => 'tech/repo/rdb/drivers/sqlite.php',
        'swap\redis_pool'              => 'tech/repo/redis/redis.php',
        'swap\redis_master_node'       => 'tech/repo/redis/redis.php',
        'swap\redis_slave_node'        => 'tech/repo/redis/redis.php',
        'swap\mover'                   => 'tech/mover/mover.php',
        'swap\mover_pool'              => 'tech/mover/mover.php',
        'swap\filesys_mover'           => 'tech/mover/types/filesys.php',
        'swap\ftp_mover'               => 'tech/mover/types/ftp.php',
        'swap\http_mover'              => 'tech/mover/types/http.php',
        'swap\value'                   => 'toolkit/value.php',
        'swap\email_value'             => 'toolkit/value.php',
        'swap\url_value'               => 'toolkit/value.php',
        'swap\ip_value'                => 'toolkit/value.php',
        'swap\dsn_value'               => 'toolkit/value.php',
        'swap\time_value'              => 'toolkit/value.php',
        'swap\mobile_value'            => 'toolkit/value.php',
        'swap\crypt'                   => 'toolkit/crypt.php',
        'swap\html'                    => 'toolkit/html.php',
        'swap\html_escapable'          => 'toolkit/html.php',
    ];
}
// [实体] 日志记录器
class logger {
    const notice = 'NOTICE';
    const warning = 'WARNING';
    const error = 'ERROR';
    public static function log_error($msg) {
        if (defined('swap\log_dir')) {
            @file_put_contents(self::get_log_file_for('error'), '[' . clock::get_datetime() . '] ' . $msg . "\n", FILE_APPEND);
        }
    }
    public static function log($filename, $msg, $level = self::notice) {
        if (defined('swap\log_dir')) {
            @file_put_contents(self::get_log_file_for($filename), '[' . clock::get_datetime() . '][' . $level . '] ' . $msg . "\n", FILE_APPEND);
        }
    }
    public static function /* @swap */ init() {
        self::$rotate_method = config::get_swap('log_rotate_method', '');
    }
    protected static function get_log_file_for($filename) {
        if (self::$rotate_method === 'day') {
            $log_file = log_dir . '/' . $filename . '-' . clock::get('Y-m-d') . '.log';
        } else if (self::$rotate_method === 'hour') {
            $log_file = log_dir . '/' . $filename . '-' . clock::get('Y-m-d-H') . '.log';
        } else {
            $log_file = log_dir . '/' . $filename . '.log';
        }
        return $log_file;
    }
    protected static $rotate_method = '';
}
// [实体] 国际化信息获取器
class i18n {
    public static function get($key, $default_value = '') {
        return array_key_exists($key, self::$texts) ? self::$texts[$key] : $default_value;
    }
    public static function set_locale($locale) {
        if ($locale !== self::$locale) {
            self::$locale = $locale;
            self::load();
        }
    }
    protected static function load() {
        if (defined('swap\share_dir')) {
            $_i18n_file = share_dir . '/lang/' . self::$locale . '.php';
            if (is_readable($_i18n_file)) {
                $_texts = require $_i18n_file;
                if (is_array($_texts)) {
                    self::$texts = $_texts;
                }
            }
        }
    }
    protected static $locale = '';
    protected static $texts = [];
}
// [实体] 调试器
class debug {
    public static function dump(/* ... */) {
        if (defined('swap\dbg_dir')) {
            ob_start();
            call_user_func_array('var_dump', func_get_args());
            $text = ob_get_clean();
            $file = dbg_dir . '/dump.log';
            @file_put_contents($file, '[' . clock::get_datetime() . '] ' . $text . "\n", FILE_APPEND);
        }
    }
    public static function save($filename, $text) {
        static $uri = '';
        if (!defined('swap\dbg_dir')) {
            return;
        }
        if ($uri === '' && !framework::is_cli_mode()) {
            $uri = visitor::uri();
        }
        $file = dbg_dir . '/' . $filename . '.log';
        @file_put_contents($file, '[' . clock::get_datetime() . '][' . $uri . '] - ' . $text . "\n", FILE_APPEND);
    }
    public static function /* @swap */ save_required_files() {
        $text = var_export(get_included_files(), true);
        if (DIRECTORY_SEPARATOR === '\\') {
            $text = str_replace('\\\\', '/', $text);
        }
        self::save('require_' . framework::get_serve_mode(), $text);
    }
}
// [实体] 运行时变量寄存器
class registry {
    public static function store($key, $value) {
        self::$data[$key] = $value;
    }
    public static function fetch($key, $default_value = null) {
        return self::has($key) ? self::$data[$key] : $default_value;
    }
    public static function has($key) {
        return array_key_exists($key, self::$data);
    }
    protected static $data = array();
}
// 检查字符串是否为符合框架要求的标志符
function is_identifier($str) {
    return is_string($str) && preg_match('/^[a-z][0-9a-z]*(_[0-9a-z]+)*$/', $str);
}
// 检查字符串是否为标志符构成的路径，例如：one/two/three
function is_identifier_path($str, $slash_count = -1) {
    if (!is_string($str)) {
        return false;
    }
    $parts = explode('/', $str);
    if ($slash_count !== -1 && count($parts) !== ($slash_count + 1)) {
        return false;
    }
    foreach ($parts as $part) {
        if (!is_identifier($part)) {
            return false;
        }
    }
    return true;
}
// 生成一个随机的 sha1 字符串
function random_sha1() {
    static $secret_key = null;
    if ($secret_key === null) {
        $secret_key = config::get_swap('secret_key', '');
    }
    $random_str = '';
    foreach (['REMOTE_ADDR', 'REMOTE_PORT', 'HTTP_USER_AGENT'] as $key) {
        if (isset($_SERVER[$key])) {
            $random_str .= $_SERVER[$key];
        }
    }
    $random_str .= microtime(true) . mt_rand() . uniqid($secret_key, true);
    return sha1($random_str . $secret_key);
}
// 检查 $small_str 是否在 $big_str 内
function in_string($small_str, $big_str) {
    return strpos($big_str, $small_str) !== false;
}
// 检查 $big_str 是否以 $small_str 开头
function starts_with($small_str, $big_str) {
    return strpos($big_str, $small_str) === 0;
}
// 检查 $big_str 是否以 $small_str 结尾
function ends_with($small_str, $big_str) {
    return strpos(strrev($big_str), strrev($small_str)) === 0;
}
// 去除 $str 首部的 $left
function strip_left($str, $left) {
    $pos = strpos($str, $left);
    if ($pos === 0) {
        $str = substr($str, strlen($left));
    }
    return $str;
}
// 去除 $str 尾部的 $right
function strip_right($str, $right) {
    $pos = strrpos($str, $right);
    if ($pos === strlen($str) - strlen($right)) {
        $str = substr($str, 0, $pos);
    }
    return $str;
}
// 去除 $str 外边的 $left 和 $right
function strip_edge($str, $left, $right) {
    $pos = strpos($str, $left);
    if ($pos === 0) {
        $t = substr($str, strlen($left));
        $pos = strrpos($t, $right);
        if ($pos === strlen($t) - strlen($right)) {
            $str = substr($t, 0, $pos);
        }
    }
    return $str;
}
// 计算字符串的字节数
function str_bytes($str) {
    return strlen($str);
}
// 计算字符串的字符数
function str_chars($str, $encoding = 'UTF-8') {
    return iconv_strlen($str, $encoding);
}
// 将字符串拆成一个个字符构成的数组
function str_units($str, $encoding = 'UTF-8') {
    if ($encoding !== 'UTF-8') {
        throw new developer_error('目前只支持 UTF-8 编码的字符串');
    }
    preg_match_all('/./su', $str, $chars);
    return $chars[0];
}
// 截取子字符串
function str_sub($str, $begin, $length, $encoding = 'UTF-8') {
    return iconv_substr($str, $begin, $length, $encoding);
}
// [类型] 程序硬错误
abstract class error extends \ErrorException {
    public function set_context(array $context) {
        $this->context = $context;
    }
    public function get_context() {
        return $this->context;
    }
    protected $context = [];
}
// [类型] 开发者代码编写错误
class developer_error extends error {}
// [类型] 代码运行时环境错误
class environment_error extends error {}
// [类型] 运行时异常
abstract class except extends \RuntimeException {}
// [类型] 运行时来访者异常
class visitor_except extends except {
    public function set_value($key, $value) {
        $this->values[$key] = $value;
    }
    public function get_value($key) {
        return $this->values[$key];
    }
    protected $values = [];
}
// [类型] 运行时服务器异常
class server_except extends except {}
// [类型] 运行时本地服务器异常
class local_except extends server_except {}
// [类型] 运行时远程服务器异常
class remote_except extends server_except {}
