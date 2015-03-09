<?php
/**
 * URL 解析器和构建器
 *
 * @copyright Copyright (c) 2009-2014 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
router::__init__();
// [实体] URL 解析器和构建器
class router {
    const /* @swap */ default_version_key = 'v';
    const /* @swap */ default_version = 0;
    const /* @swap */ default_target_key = 't';
    const /* @swap */ default_csrf_key = 's';
    const /* @swap */ default_controller_name = 'site';
    const /* @swap */ default_action_name = 'index';
    const /* @swap */ lazy_unit_pattern = '([^/]*?)';
    const /* @swap */ greedy_unit_pattern = '([^/]*)';
    public static function /* @swap */ __init__() {
        // @todo: lazy initialization?
        self::$prefix = visitor::get_prefix();
        self::$static_domain = setting::get_swap('static_domain', '');
        self::$upload_domain = setting::get_swap('upload_domain', '');
        self::$version_key = setting::get_swap('version_key', self::default_version_key);
        self::$version = setting::get_swap('version', self::default_version);
        self::$base_is_https = setting::get_base('url.is_https', false);
        self::$base_domains = setting::get_base('url.domains', []);
        foreach (self::$base_domains as $domain) {
            # base 模块的 module_name 统一为 ''
            self::$domain_modules[$domain] = '';
        }
        self::$base_enable_rewrite = setting::get_base('url.enable_rewrite', false);
        self::$base_csrf_key = setting::get_base('url.csrf_key', self::default_csrf_key);
        self::$base_target_key = setting::get_base('url.target_key', self::default_target_key);
        self::$base_routes = setting::get_base('url.routes', []);
        self::$base_flipped_routes = array_flip(self::$base_routes);
        foreach (setting::get_module_names() as $module_name) {
            setting::set_module_name($module_name);
            self::$module_is_https[$module_name] = setting::get_module('url.is_https', false);
            $domains = setting::get_module('url.domains', []);
            self::$module_domains[$module_name] = $domains;
            foreach ($domains as $domain) {
                self::$domain_modules[$domain] = $module_name;
            }
            self::$module_enable_rewrites[$module_name] = setting::get_module('url.enable_rewrite', false);
            self::$module_csrf_keys[$module_name] = setting::get_module('url.csrf_key', self::default_csrf_key);
            self::$module_target_keys[$module_name] = setting::get_module('url.target_key', self::default_target_key);
            self::$module_routes[$module_name] = setting::get_module('url.routes', []);
            self::$module_flipped_routes[$module_name] = array_flip(self::$module_routes[$module_name]);
        }
        setting::set_module_name(null);
    }
    public static function /* @swap */ parse_php_uri($uri, $host) {
        $at_module_name = isset(self::$domain_modules[$host]) ? self::$domain_modules[$host] : '';
        if ($at_module_name === '') {
            $enable_rewrite = self::$base_enable_rewrite;
        } else {
            $enable_rewrite = self::$module_enable_rewrites[$at_module_name];
        }
        setting::set_module_name($at_module_name);
        return $enable_rewrite ? self::parse_rewrited_uri($uri, $at_module_name) : self::parse_standard_uri($uri, $at_module_name);
    }
    protected static function parse_standard_uri($uri, $at_module_name) {
        // @todo: on invalid return 404?
        $target_module = $at_module_name;
        $target_controller = self::default_controller_name;
        $target_action = self::default_action_name;
        $target_params = [];
        $mark_pos = strpos($uri, '?');
        if ($mark_pos !== false && $mark_pos !== strlen($uri) - 1) {
            parse_str(substr($uri, $mark_pos + 1), $target_params);
        }
        $target_key = $at_module_name === '' ? self::$base_target_key : self::$module_target_keys[$at_module_name];
        if (isset($target_params[$target_key])) {
            $target_path = $target_params[$target_key];
            if ($at_module_name === '' && in_string('-', $target_path)) {
                $parts = explode('-', $target_path, 2);
                if (isset(self::$module_domains[$parts[0]]) && self::$module_domains[$parts[0]] === [] && is_identifier($parts[0])) {
                    $target_module = $parts[0];
                    $target_path = $parts[1];
                }
            }
            if ($target_path !== '') {
                $parts = explode('/', $target_path, 2);
                if (count($parts) === 1) {
                    if (is_identifier($target_path)) {
                        $target_controller = $target_path;
                    }
                } else {
                    if (is_identifier($parts[0])) {
                        $target_controller = $parts[0];
                    }
                    if (is_identifier($parts[1])) {
                        $target_action = $parts[1];
                    }
                }
            }
        }
        $target_name = $target_controller . '/' . $target_action;
        if ($target_module !== '') {
            $target_name = $target_module . '-' . $target_name;
        }
        return new target([$target_name, $target_params]);
    }
    protected static function parse_rewrited_uri($uri, $at_module_name) {
        if (in_string('?', $uri)) {
            list($request_path, $query_str) = explode('?', $uri, 2);
            parse_str($query_str, $target_params);
        } else {
            $request_path = $uri;
            $target_params = [];
        }
        // 进行 request_path 解析。先尝试匹配路由规则
        $routes = $at_module_name === '' ? self::$base_routes : self::$module_routes[$at_module_name];
        foreach ($routes as $match_pattern => $target_pattern) {
            $match_pattern = str_replace('.', '\\.', $match_pattern); # “.” 号特殊处理
            // 将形式正则改成实际正则
            if (in_string('*', $match_pattern)) {
                $match_pattern = str_replace('*', self::lazy_unit_pattern, $match_pattern);
                $rep_pos = strrpos($match_pattern, self::lazy_unit_pattern);
                $rep_len = strlen(self::lazy_unit_pattern);
                $match_pattern = substr_replace($match_pattern, self::greedy_unit_pattern, $rep_pos, $rep_len);
            }
            $match_pattern = '!^' . $match_pattern . '$!';
            // 检查该正则是否匹配 request_path ？
            if (preg_match($match_pattern, $request_path, $matches)) {
                // 匹配。接着使用真实值替换预定义的 $N 参数
                $match_count = count($matches);
                $args = [];
                if ($match_count > 1) {
                    for ($i = 1; $i < $match_count; $i++) {
                        $args['$' . $i] = urldecode($matches[$i]);
                    }
                }
                // 构建 target 并返回
                $pattern_target = new target($target_pattern);
                foreach ($pattern_target->get_params() as $key => $value) {
                    $target_params[$key] = isset($args[$value]) ? $args[$value] : $value;
                }
                return new target([$pattern_target->get_target_name(), $target_params]);
            }
        }
        // 没有找到匹配的路由规则。那么我们使用普通规则
        $target_module = $at_module_name;
        $target_controller = self::default_controller_name;
        $target_action = self::default_action_name;
        if ($at_module_name === '' && in_string('-', $request_path)) {
            $parts = explode('-', $request_path, 2);
            $parts[0] = ltrim($parts[0], '/');
            if (isset(self::$module_domains[$parts[0]]) && self::$module_domains[$parts[0]] === [] && is_identifier($parts[0])) {
                $target_module = $parts[0];
                $request_path = '/' . $parts[1];
            }
        }
        $request_path = substr($request_path, 1);
        if ($request_path !== '') {
            $parts = explode('/', $request_path, 2);
            if (count($parts) === 1) {
                if (is_identifier($request_path)) {
                    $target_controller = $request_path;
                }
            } else {
                if (is_identifier($parts[0])) {
                    $target_controller = $parts[0];
                }
                if (is_identifier($parts[1])) {
                    $target_action = $parts[1];
                }
            }
        }
        $target_name = $target_controller . '/' . $target_action;
        if ($target_module !== '') {
            $target_name = $target_module . '-' . $target_name;
        }
        return new target([$target_name, $target_params]);
    }
    public static function /* @swap */ parse_pps_uri($uri) {
        setting::set_module_name('');
        $target_name = '';
        $mark_pos = strpos($uri, '?');
        if ($mark_pos === false) {
            return new target($target_name);
        }
        $query_str = substr($uri, $mark_pos + 1);
        $param_strs = explode(';', $query_str);
        $dirty_params = [];
        foreach ($param_strs as $param_str) {
            if (in_string('=', $param_str)) {
                list($param_key, $param_value) = explode('=', $param_str, 2);
                if ($param_key !== '') {
                    $dirty_params[$param_key] = $param_value;
                }
            }
        }
        $target_module = '';
        if (isset($dirty_params['page']) && is_string($dirty_params['page'])) {
            $page = $dirty_params['page'];
            if (substr_count($page, '/') === 1) {
                if (in_string('-', $page)) {
                    list($module_name, $page) = explode('-', $page, 2);
                    if (is_identifier($module_name)) {
                        $target_module = $module_name;
                    }
                }
                list($target_controller, $target_action) = explode('/', $page);
                if (is_identifier($target_controller) && is_identifier($target_action)) {
                    $target_name = $target_controller . '/' . $target_action;
                    if ($target_module !== '') {
                        setting::set_module_name($target_module);
                        $target_name = $target_module . '-' . $target_name;
                    }
                }
            }
        }
        $target_params = [];
        if (isset($dirty_params['layout']) && is_string($dirty_params['layout'])) {
            $arg = $dirty_params['layout'];
            if (is_identifier_path($arg)) {
                $target_params['layout'] = $arg;
            }
        }
        foreach (['link', 'block'] as $arg_name) {
            if (isset($dirty_params[$arg_name]) && is_string($dirty_params[$arg_name])) {
                $arg = $dirty_params[$arg_name];
                $args = in_string(',', $arg) ? explode(',', $arg) : [$arg];
                $target_params[$arg_name] = [];
                foreach ($args as $arg) {
                    if (is_identifier_path($arg)) {
                        $target_params[$arg_name][] = $arg;
                    }
                }
            }
        }
        if (isset($dirty_params[self::$version_key]) && is_numeric($dirty_params[self::$version_key])) {
            $target_params[self::$version_key] = $dirty_params[self::$version_key];
        }
        return new target([$target_name, $target_params]);
    }
    public static function /* @php */ build_php_url($target, $echo = true, $for_html = true, $as_absolute = true) {
        if (!$target instanceof target) {
            $target = new target($target);
        }
        if ($target->has_module()) {
            $target_module = $target->get_module_name();
            if (self::$module_domains[$target_module] !== [] && !$as_absolute) {
                throw new developer_error($target_module . '模块有自己的独立域名，只能生成绝对URL，不能生成相对URL');
            }
            $target_enable_rewrite = self::$module_enable_rewrites[$target_module];
        } else {
            $target_module = '';
            $target_enable_rewrite = self::$base_enable_rewrite;
        }
        $and_char = $for_html ? '&amp;' : '&';
        list($base_url, $query_char) = self::build_base_url($target, $and_char, $as_absolute, $target_enable_rewrite);
        $left_url_use_normal = true;
        if ($target_enable_rewrite) {
            $left_url = self::build_routed_left_url($target);
            if ($left_url !== null) {
                $left_url_use_normal = false;
            }
        }
        if ($left_url_use_normal) {
            $left_url = self::build_normal_left_url($target, $query_char, $and_char, $target_enable_rewrite);
        }
        $url = $base_url . $left_url;
        if ($echo) {
            echo $url;
        } else {
            return $url;
        }
    }
    protected static function build_base_url(target $target, $and_char, $as_absolute, $target_enable_rewrite) {
        if ($target->has_module()) {
            $target_module = $target->get_module_name();
            $target_key = self::$module_target_keys[$target_module];
        } else {
            $target_module = '';
            $target_key = self::$base_target_key;
        }
        $query_char = '?';
        if ($as_absolute) {
            if ($target->has_module() && self::$module_domains[$target_module] !== []) {
                $target_scheme = self::$module_is_https[$target_module] ? 'https://' : 'http://';
                $base_url = $target_scheme . self::$module_domains[$target_module][0] . self::$prefix . '/';
            } else {
                $base_url = self::$prefix . '/';
                if ($target->has_module()) {
                    if ($target_enable_rewrite) {
                        $base_url .= $target_module . '-';
                    } else {
                        $base_url .= $query_char . $target_key . '=' . $target_module . '-';
                        $query_char = $and_char;
                    }
                }
                if (self::$base_domains !== []) {
                    $target_scheme = self::$base_is_https ? 'https://' : 'http://';
                    $base_url = $target_scheme . self::$base_domains[0] . $base_url;
                }
            }
        } else {
            $base_url = '/';
            if ($target->has_module()) {
                if ($target_enable_rewrite) {
                    $base_url .= $target_module . '-';
                } else {
                    $base_url .= $query_char . $target_key . '=' . $target_module . '-';
                    $query_char = $and_char;
                }
            }
        }
        return [$base_url, $query_char];
    }
    protected static function build_normal_left_url(target $target, $query_char, $and_char, $target_enable_rewrite) {
        $left_url = '';
        $target_controller = $target->get_controller_name();
        $target_action = $target->get_action_name();
        if ($target_controller !== self::default_controller_name || $target_action !== self::default_action_name) {
            $target_path = $target_controller . '/' . $target_action;
            if ($target_enable_rewrite) {
                $left_url .= $target_path;
            } else {
                if ($target->has_module()) {
                    $left_url .= $target_path;
                } else {
                    $left_url .= $query_char . self::$base_target_key . '=' . $target_path;
                }
                $query_char = $and_char;
            }
        }
        $target_params = [];
        foreach ($target->get_params() as $key => $value) {
            $target_params[] = urlencode($key) . '=' . urlencode($value);
        }
        if ($target_params !== []) {
            $left_url .= $query_char . implode($and_char, $target_params);
        }
        return $left_url;
    }
    protected static function build_routed_left_url(target $target) {
        $target_params = $target->get_params();
        $target_name = $target->get_target_name();
        if ($target->has_module()) {
            $target_module = $target->get_module_name();
            $flipped_routes = self::$module_flipped_routes[$target_module];
        } else {
            $target_module = '';
            $flipped_routes = self::$base_flipped_routes;
        }
        if ($target_params === []) {
            if (isset($flipped_routes[$target_name])) {
                $result_pattern = $flipped_routes[$target_name];
                return $target_module === '' ? ltrim($result_pattern, '/') : substr($result_pattern, strpos($result_pattern, '-') + 1);
            }
        } else {
            $target_param_keys = $target->get_param_keys();
            foreach ($flipped_routes as $match_token => $result_pattern) {
                $match_target = new target($match_token);
                if ($match_target->get_target_name() === $target_name && $match_target->get_param_keys() === $target_param_keys) {
                    $args = [];
                    $match = true;
                    foreach ($match_target->get_params() as $key => $value) {
                        if ($value[0] === '$') {
                            $number = substr($value, 1);
                            $args[$number] = $target_params[$key];
                        } else if ($value !== $target_params[$key]) {
                            $match = false;
                            break;
                        }
                    }
                    if ($match) {
                        ksort($args);
                        if ($target->has_module()) {
                            $result_pattern = substr($result_pattern, strpos($result_pattern, '-') + 1);
                        }
                        $request_parts = explode('*', $result_pattern);
                        $result_uri = '';
                        for ($i = 0, $n = count($args); $i < $n; $i++) {
                            $result_uri .= $request_parts[$i] . urlencode($args[$i + 1]);
                        }
                        $result_uri .= $request_parts[$i];
                        return ltrim($result_uri, '/');
                    }
                }
            }
        }
        return null;
    }
    public static function /* @php */ build_csrf_url($csrf_role, $target, $echo = true, $for_html = true, $as_absolute = true) {
        if (!$target instanceof target) {
            $target = new target($target);
        }
        if (visitor::has_role($csrf_role)) {
            $role_secret = visitor::get_role_secret($csrf_role);
            $csrf_key = self::$base_csrf_key;
            if ($target->has_module()) {
                $csrf_key = self::$module_csrf_keys[$target->get_module_name()];
            }
            $target->set_param($csrf_key, $role_secret);
        }
        return self::build_php_url($target, $echo, $for_html, $as_absolute);
    }
    public static function /* @swap */ web_url($path) {
        $web_url = self::$prefix;
        if ($path !== '' && $path !== '/') {
            $web_url .= '/' . ltrim($path, '/');
        }
        $at_module_name = setting::get_module_name();
        $domains = $at_module_name === '' ? self::$base_domains : self::$module_domains[$at_module_name];
        if ($domains !== []) {
            $is_https = $at_module_name === '' ? self::$base_is_https : self::$module_is_https[$at_module_name];
            $web_url = ($is_https ? 'https://' : 'http://') . $domains[0] . $web_url;
        }
        return $web_url;
    }
    public static function /* @swap */ static_url($static_file, $for_html = null, $echo = true) {
        if (self::$static_domain === '') {
            $static_url = self::web_url('/static'); # 包含 prefix
        } else {
            $at_module_name = setting::get_module_name();
            $is_https = $at_module_name === '' ? self::$base_is_https : self::$module_is_https[$at_module_name];
            $static_url = ($is_https ? 'https://' : 'http://') . self::$static_domain . '/static'; # 独立域名不包含 prefix
        }
        $static_url .= '/' . ltrim($static_file, '/');
        if (self::$version !== 0) {
            $and_char = self::get_and_char($for_html);
            $static_url .= (strpos($static_url, '?') === false ? '?' : $and_char) . self::$version_key . '=' . self::$version;
        }
        if ($echo) {
            echo $static_url;
        } else {
            return $static_url;
        }
    }
    public static function /* @swap */ upload_url($upload_file, $echo = true) {
        if (self::$upload_domain === '') {
            $upload_url = self::web_url('/upload'); # 包含 prefix
        } else {
            $at_module_name = setting::get_module_name();
            $is_https = $at_module_name === '' ? self::$base_is_https : self::$module_is_https[$at_module_name];
            $upload_url = ($is_https ? 'https://' : 'http://') . self::$upload_domain . '/upload';
        }
        $upload_url .= '/' . ltrim($upload_file, '/');
        if ($echo) {
            echo $upload_url;
        } else {
            return $upload_url;
        }
    }
    public static function /* @swap */ pps_url($pps_uri, $echo) {
        if (self::$static_domain === '') {
            $pps_url = self::web_url('/'); # 包含 prefix
        } else {
            $at_module_name = setting::get_module_name();
            $is_https = $at_module_name === '' ? self::$base_is_https : self::$module_is_https[$at_module_name];
            $pps_url = ($is_https ? 'https://' : 'http://') . self::$static_domain; # 独立域名不包含 prefix
        }
        $pps_url .= '/' . ltrim($pps_uri, '/') . ';' . self::$version_key . '=' . self::$version;
        if ($echo) {
            echo $pps_url;
        } else {
            return $pps_url;
        }
    }
    protected static function get_and_char($for_html) {
        if ($for_html === null) {
            return framework::is_pps_mode() ? '&' : '&amp;';
        } else {
            return $for_html ? '&amp;' : '&';
        }
    }
    public static function /* @view */ get_version_key() {
        return self::$version_key;
    }
    public static function /* @view */ get_version() {
        return self::$version;
    }
    protected static $prefix = '';
    protected static $static_domain = '';
    protected static $upload_domain = '';
    protected static $version_key = self::default_version_key;
    protected static $version = self::default_version;
    protected static $base_is_https = false;
    protected static $base_domains = [];
    protected static $base_enable_rewrite = false;
    protected static $base_csrf_key = self::default_csrf_key;
    protected static $base_target_key = self::default_target_key;
    protected static $base_routes = [];
    protected static $base_flipped_routes = [];
    protected static $module_is_https = [];
    protected static $module_domains = [];
    protected static $module_enable_rewrites = [];
    protected static $module_csrf_keys = [];
    protected static $module_target_keys = [];
    protected static $module_routes = [];
    protected static $module_flipped_routes = [];
    protected static $domain_modules = [];
}
/**
 * [类型] 目标
 *
 * target_token 有两种格式，一种字符串，一种数组。
 * 两种又区分带参数与不带参数。
 *
 * 举例：
 *
 *   'site/login'
 *   'admin-user/delete?arg1=int1&arg2=int2'
 *   ['site/login']
 *   ['admin-user/delete', ['&' => '>', '<' => '?', '?' => '=']]
 */
class target implements html_escapable {
    public function __construct($target_token) {
        if (is_array($target_token)) {
            if ($target_token === []) {
                return;
            }
            $target_name = $target_token[0];
            if (isset($target_token[1]) && is_array($target_token[1])) {
                $this->params = $target_token[1];
            }
        } else if (is_string($target_token)) {
            if ($target_token === '') {
                return;
            }
            $params = [];
            if (in_string('?', $target_token)) {
                list($target_name, $params_str) = explode('?', $target_token, 2);
                parse_str($params_str, $params);
            } else {
                $target_name = $target_token;
            }
            $this->params = $params;
        } else {
            throw new developer_error('bad target_token for target');
        }
        $this->target_name = $target_name;
        if ($target_name !== '') {
            if (in_string('-', $target_name)) {
                $this->target_file = str_replace('-', '/', $target_name);
                list($module_name, $target_path) = explode('-', $target_name, 2);
                if (is_identifier($module_name)) {
                    $this->has_module = true;
                    $this->module_name = $module_name;
                }
            } else {
                $this->target_file = $target_name;
                $target_path = $target_name;
            }
            $this->target_path = $target_path;
            list($this->controller_name, $this->action_name) = explode('/', $target_path, 2);
            $this->target_pair = [$this->controller_name, $this->action_name];
        }
    }
    public function html_escape() {
        $that = clone $this;
        $that->params = html::escape($that->params);
        return $that;
    }
    public function html_unescape() {
        $that = clone $this;
        $that->params = html::unescape($that->params);
        return $that;
    }
    public function as_array() {
        return [$this->target_name, $this->params];
    }
    public function has_module() {
        return $this->has_module;
    }
    public function get_module_name() {
        return $this->module_name;
    }
    public function get_target_name() {
        return $this->target_name;
    }
    public function get_target_path() {
        return $this->target_path;
    }
    public function get_target_pair() {
        return $this->target_pair;
    }
    public function get_target_file($file_ext = '') {
        return $this->target_file . $file_ext;
    }
    public function get_controller_name() {
        return $this->controller_name;
    }
    public function get_action_name() {
        return $this->action_name;
    }
    public function has_params() {
        return $this->params === [];
    }
    public function get_params() {
        return $this->params;
    }
    public function set_params(array $params) {
        $this->params = $params;
    }
    public function get_param($key, $default_value = null) {
        return array_key_exists($key, $this->params) ? $this->params[$key] : $default_value;
    }
    public function set_param($key, $value) {
        $this->params[$key] = $value;
    }
    public function get_param_keys() {
        return array_keys($this->params);
    }
    protected $target_token = '';    # '',    'site/login?a=b',  'admin-user/delete?%11=%22&%33=%44'
    protected $target_name = '';     # '',    'site/login',      'admin-user/delete'
    protected $has_module = false;   # false, false,             true
    protected $module_name = '';     # '',    '',                'admin'
    protected $controller_name = ''; # '',    'site',            'user'
    protected $action_name = '';     # '',    'login',           'delete'
    protected $target_path = '';     # '',    'site/login',      'user/delete'
    protected $target_pair = [];     # [],    ['site', 'login'], ['user', 'delete']
    protected $target_file = '';     # '',    'site/login',      'admin/user/delete'
    protected $params = [];          # [],    ['a' => 'b'],      ['?' => '&', '=' => '#']
}
