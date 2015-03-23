<?php
/**
 * 模板渲染器，控制器，控制助手，过滤器
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
// [实体] PHP 模式分派器
class /* @kern */ php_dispatcher {
    public static function dispatch() {
        self::load_global_file();
        $target = router::parse_php_uri(visitor::uri(), visitor::host());
        $forward_times = 0;
        while (true) {
            if ($forward_times >= 8) {
                throw new developer_error('too many forwards');
            }
            self::$global_filters = setting::get_module('global_filters', null);
            try {
                self::dispatch_target($target);
                break;
            } catch (action_forward $forward) {
                $target = $forward->get_target();
                setting::set_module_name($target->get_module_name());
                visitor::forward_cookies();
                $forward_times++;
                continue;
            } catch (dispatch_return $return) {
                return;
            }
        }
    }
    protected static function load_global_file() {
        if (defined('kern\utility_dir')) {
            $global_file = utility_dir . '/global.php';
            if (is_readable($global_file)) {
                loader::load_file($global_file);
            }
        }
    }
    protected static function dispatch_target(target $target) {
        try {
            visitor::set_target($target);
            visitor::restore_roles();
            list($controller_name, $action_name) = $target->get_target_pair();
            $controller = $controller_name . '_controller';
            $controller_file = controller_dir . '/';
            if ($target->has_module()) {
                $controller_file .= $target->get_module_name() . '/';
            }
            $controller_file .= $controller . '.php';
            if (!is_readable($controller_file)) {
                throw new visitor_except('controller "' . $controller_name . '" does not exist', 404);
            }
            loader::load_file($controller_file);
            $action = $action_name . '_action';
            if (!is_callable([$controller, $action], false)) {
                throw new visitor_except('action "' . $action_name . '" does not exist', 404);
            }
            self::run_action($controller_name, $controller, $action, null, true);
        } catch (\Exception $e) {
            self::dispatch_except($e);
        }
    }
    protected static function dispatch_except(\Exception $e) {
        if ($e instanceof except || $e instanceof error) {
            $except_code = $e->getCode();
            if (!($e instanceof visitor_except)) {
                $except_code = 500;
            }
            if (isset(self::$except_handlers[$except_code])) {
                $controller_name = 'except';
                $controller = $controller_name . '_controller';
                $controller_file = controller_dir . '/' . $controller_name . '_controller.php';
                if (is_readable($controller_file)) {
                    loader::load_file($controller_file);
                    $action_name = self::$except_handlers[$except_code];
                    $action = $action_name . '_action';
                    if (is_callable([$controller, $action], false)) {
                        visitor::set_target(new target($controller_name . '/' . $action_name));
                        self::run_action($controller_name, $controller, $action, $e, false);
                        return;
                    }
                }
            }
        }
        throw $e;
    }
    protected static function run_action($controller_name, $controller, $action, $action_arg, $run_filters) {
        controller::reset();
        if ($run_filters) {
            $have_global_filters = self::$global_filters !== null;
            if ($have_global_filters) {
                self::run_global_filters('before');
            }
            self::run_controller_filters('before', $controller_name, $action);
        }
        try {
            if (is_callable([$controller, 'before_run'], false)) {
                $controller::before_run();
            }
            $controller::$action($action_arg);
            if (is_callable([$controller, 'after_run'], false)) {
                $controller::after_run();
            }
        } catch (action_return $return) {}
        if ($run_filters) {
            self::run_controller_filters('after', $controller_name, $action);
            if ($have_global_filters) {
                self::run_global_filters('after');
            }
        }
    }
    protected static function run_global_filters($filter_type) {
        if (isset(self::$global_filters[$filter_type]) && is_array(self::$global_filters[$filter_type])) {
            foreach (self::$global_filters[$filter_type] as $filter => $filter_arg) {
                self::run_filter($filter_type, $filter, $filter_arg);
            }
        }
    }
    protected static function run_controller_filters($filter_type, $controller_name, $action) {
        $args_getter = $filter_type . '_filters';
        $controller = $controller_name . '_controller';
        if (!is_callable([$controller, $args_getter])) {
            return;
        }
        $filter_args = $controller::$args_getter();
        if (!is_array($filter_args)) {
            return;
        }
        foreach ($filter_args as $filter => $action_to_arg) {
            if (!is_array($action_to_arg)) {
                throw new developer_error('filter arg should be an assoc array with action as key, setting as value');
            }
            if (isset($action_to_arg[$action])) {
                $filter_arg = $action_to_arg[$action];
            } else if (isset($action_to_arg['*'])) {
                $filter_arg = $action_to_arg['*'];
            } else {
                continue;
            }
            self::run_filter($filter_type, $filter, $filter_arg);
        }
    }
    protected static function run_filter($filter_type, $filter, $filter_arg) {
        if (!class_exists($filter, true)) {
            $filter = 'kern\\' . $filter;
            if (!class_exists($filter, true)) {
                throw new developer_error("cannot find filter: {$filter}");
            }
        }
        if (!is_subclass_of($filter, 'kern\\' . $filter_type . '_filter')) {
            throw new developer_error("filter: {$filter} is not a " . $filter_type . ' filter');
        }
        $filter::run($filter_arg);
    }
    protected static $except_handlers = [
        403 => 'access_denied',
        404 => 'target_missing',
        405 => 'method_denied',
        406 => 'browser_denied',
        500 => 'server_except',
    ];
    protected static $global_filters = null;
}
// [实体] 模板渲染器
abstract class tpl_rendor extends rendor {
    public static function /* @kern */ reset() {
        parent::use_viewlet('tpl');
        self::$target = visitor::get_target();
        self::$skeleton = setting::get_module('view.default_skeleton', false);
        self::$linked_styles = [];
        self::$linked_scripts = ['top' => [], 'bottom' => []];
        self::$linked_psses = [];
        self::$linked_pjses = ['top' => [], 'bottom' => []];
        self::$layout_pss = '';
        self::$layout_pjs = '';
        self::$page_pss = '';
        self::$page_pjs = '';
        self::$block_psses = [];
        self::$block_pjses = [];
        self::$helper_args = [];
    }
    public static function /* @php */ skeleton($name_or_false) {
        self::$skeleton = $name_or_false;
    }
    public static function /* @php */ helper_set($key, $value) {
        self::$helper_args[$key] = $value;
    }
    protected static function /* @tpl */ link_style($style_file, $in_place = false) {
        if (self::$skeleton !== false) {
            if ($in_place) {
                self::echo_css_link(parent::static_url($style_file, null, false));
            } else if (!array_key_exists($style_file, self::$linked_styles)) {
                self::$linked_styles[$style_file] = $style_file;
            }
        }
    }
    protected static function /* @tpl */ link_script($script_file, $in_place = false, $at_top = false) {
        if (self::$skeleton !== false) {
            if ($in_place) {
                self::echo_js_link(parent::static_url($script_file, null, false));
            } else {
                $linked_scripts =& self::$linked_scripts[$at_top ? 'top' : 'bottom'];
                if (!array_key_exists($script_file, $linked_scripts)) {
                    $linked_scripts[$script_file] = $script_file;
                }
            }
        }
    }
    protected static function /* @tpl */ link_pss($pss_name, $in_place = false) {
        if (self::$skeleton !== false) {
            if ($in_place) {
                self::echo_css_link(router::pps_url('pss.php?link=' . $pss_name, false));
            } else if (!array_key_exists($pss_name, self::$linked_psses)) {
                self::$linked_psses[$pss_name] = $pss_name;
            }
        }
    }
    protected static function /* @tpl */ link_pjs($pjs_name, $in_place = false, $at_top = false) {
        if (self::$skeleton !== false) {
            if ($in_place) {
                self::echo_js_link(router::pps_url('pjs.php?link=' . $pjs_name, false));
            } else {
                $linked_pjses =& self::$linked_pjses[$at_top ? 'top' : 'bottom'];
                if (!array_key_exists($pjs_name, $linked_pjses)) {
                    $linked_pjses[$pjs_name] = $pjs_name;
                }
            }
        }
    }
    protected static function /* @tpl */ block($block_name, array $alias = []) {
        if (!in_array($block_name, self::$block_psses)) {
            self::$block_psses[] = $block_name;
            self::$block_pjses[] = $block_name;
        }
        helper::reset();
        helper::set_block_name($block_name);
        $helper_file = helper_dir . '/' . $block_name . '_helper.php';
        $helper = str_replace('/', '_', $block_name) . '_helper';
        if (is_readable($helper_file)) {
            loader::load_file($helper_file);
            $helper::run(array_merge(context::get_primary($alias), self::$helper_args));
        } else {
            helper::set_alias($alias);
            helper::render();
        }
    }
    protected static function /* @tpl */ csrf_url($csrf_role, $target, $for_html = null, $echo = true) {
        if ($for_html === null) {
            $for_html = !kernel::is_pps_mode();
        }
        return router::build_csrf_url($csrf_role, $target, $echo, $for_html);
    }
    protected static function /* @tpl */ csrf_arg($csrf_role, $echo = true) {
        $csrf_arg = '';
        if (visitor::has_role($csrf_role)) {
            $csrf_arg = setting::get_module('url.csrf_key', router::default_csrf_key) . '=' . visitor::get_role_secret($csrf_role);
        }
        if ($echo) {
            echo $csrf_arg;
        } else {
            return $csrf_arg;
        }
    }
    protected static function /* @tpl */ csrf_field($csrf_role) {
        if (visitor::has_role($csrf_role)) {
            $csrf_key = setting::get_module('url.csrf_key', router::default_csrf_key);
            $role_secret = visitor::get_role_secret($csrf_role);
            echo '<input type="hidden" name="' . $csrf_key . '" value="' . $role_secret . '">';
        }
    }
    protected static function /* @kern */ echo_css_link($href) {
        echo '<link rel="stylesheet" href="' . $href . '" type="text/css">' . "\n";
    }
    protected static function /* @kern */ echo_js_link($src) {
        echo '<script type="text/javascript" src="' . $src . '"></script>' . "\n";
    }
    protected static function /* @kern */ change_target_to(target $target) {
        self::$target = $target;
    }
    protected static function /* @kern */ render_tpl($_tpl_file, array $_args = [], $_escape_args = true) {
        if ($_escape_args) {
            $_args = html::escape($_args);
        }
        extract($_args);
        ob_start();
        try {
            require view_dir . '/' . ltrim($_tpl_file, '/');
            return ob_get_clean();
        } catch (developer_error $_e) {
            ob_get_clean();
            throw $_e;
        }
    }
    // 可 reset 的属性
    protected static $target = null;
    protected static $skeleton = false;
    protected static $linked_styles = [];
    protected static $linked_scripts = ['top' => [], 'bottom' => []];
    protected static $linked_psses = [];
    protected static $linked_pjses = ['top' => [], 'bottom' => []];
    protected static $layout_pss = '';
    protected static $layout_pjs = '';
    protected static $page_pss = '';
    protected static $page_pjs = '';
    protected static $block_psses = [];
    protected static $block_pjses = [];
    protected static $helper_args = [];
}
// [实体] 控制器
abstract class controller extends tpl_rendor {
    public static function /* @kern */ reset() {
        parent::reset();
        self::$title = setting::get_module('view.default_title', '');
        self::$keywords = setting::get_module('view.default_keywords', '');
        self::$description = setting::get_module('view.default_description', '');
        self::$author = setting::get_module('view.default_author', '');
        self::$viewport = setting::get_module('view.default_viewport', '');
        self::$metas = [];
        self::$target_block = [];
    }
    public static function /* @controller */ dump(/* ... */) {
        ob_start();
        call_user_func_array('var_dump', func_get_args());
        self::send(ob_get_clean());
    }
    public static function /* @controller */ method($method) {
        if (visitor::method() !== strtoupper($method)) {
            $except = new visitor_except('method not allowed', 405);
            $except->set_value('allow_list', $method);
            throw $except;
        }
    }
    public static function /* @controller */ role($role, $method = 'redirect_to', $target = '') {
        if (!visitor::has_role($role)) {
            if ($target === '') {
                throw new visitor_except('role forbidden', 403);
            } else {
                if ($method === 'redirect_to') {
                    self::redirect_to($target);
                } else {
                    self::forward_to($target);
                }
            }
        }
    }
    public static function /* @controller */ csrf($csrf_role) {
        if (visitor::has_role($csrf_role)) {
            $csrf_key = setting::get_module('url.csrf_key', router::default_csrf_key);
            $role_secret = null;
            if (visitor::p_has($csrf_key)) {
                $role_secret = visitor::p_str($csrf_key);
            } else if (visitor::g_has($csrf_key)) {
                $role_secret = visitor::g_str($csrf_key);
            }
            if ($role_secret !== visitor::get_role_secret($csrf_role)) {
                throw new visitor_except('csrf attack', 403);
            }
        }
    }
    public static function /* @controller */ set($key, $value) {
        context::set($key, $value);
    }
    /**
     * 把 page tpl 嵌入 layout tpl 呈现。
     *
     *   self::show_page();                           // 对应当前 action 名字的 page tpl 和 controller 的默认 layout tpl
     *   self::show_page('page_name');                // 对应 page_name.tpl 和 controller 的默认 layout tpl
     *   self::show_page('', 'layout_name');          // 对应当前 action 名字的 page tpl 和 layout_name.tpl
     *   self::show_page('', false);                  // 对应当前 action 名字的 page tpl 但不使用 layout tpl（即 page tpl 本身包含 <body></body>）
     *   self::show_page('page_name', 'layout_name'); // 对应 page_name.tpl 和 layout_name.tpl
     *   self::show_page('page_name', false);         // 对应 page_name.tpl 但不使用 layout tpl（即 page tpl 本身包含 <body></body>）
     */
    public static function /* @controller */ show_page(/* ... */) {
        $func_args = func_get_args();
        $num_args = func_num_args();
        $layout_name = null;
        if ($num_args === 0) {
            $target_name = self::get_target_name_from_page_name();
        } else if ($num_args === 1) {
            $target_name = self::get_target_name_from_page_name($func_args[0]);
        } else {
            $target_name = self::get_target_name_from_page_name($func_args[0]);
            $layout_name = $func_args[1];
        }
        parent::$page_pss = parent::$page_pjs = $target_name;
        parent::change_target_to(new target($target_name));
        self::show_with_layout($layout_name);
    }
    /**
     * 把 block tpl 嵌入 layout tpl 呈现。
     *
     *   self::show_block('block_name');                        // 对应 block_name.tpl 和 controller 的默认 layout tpl
     *   self::show_block('block_name', false);                 // 对应 block_name.tpl 但不使用 layout tpl（即 block tpl 本身包含 <body></body>）
     *   self::show_block('block_name', $alias);                // 对应 block_name.tpl 和 controller 的默认 layout tpl，同时进行别名更改
     *   self::show_block('block_name', 'layout_name');         // 对应 block_name.tpl 和 layout_name.tpl
     *   self::show_block('block_name', 'layout_name', $alias); // 对应 block_name.tpl 和 layout_name.tpl，同时进行别名更改
     *   self::show_block('block_name', false, $alias);         // 对应 block_name.tpl 但不使用 layout tpl（即 block tpl 本身包含 <body></body>）
     */
    public static function /* @controller */ show_block(/* ... */) {
        $func_args = func_get_args();
        $num_args = func_num_args();
        $target_block = ['name' => $func_args[0], 'alias' => []];
        $layout_name = null;
        if ($num_args === 1) {
            /* do nothing */
        } else if ($num_args === 2) {
            if (is_array($func_args[1])) {
                $target_block['alias'] = $func_args[1];
            } else {
                $layout_name = $func_args[1];
            }
        } else if ($num_args === 3) {
            if (is_array($func_args[2])) {
                $target_block['alias'] = $func_args[2];
                $layout_name = $func_args[1];
            } else {
                throw new developer_error('bad call to show_block');
            }
        } else {
            throw new developer_error('bad call to show_block');
        }
        self::$target_block = $target_block;
        self::show_with_layout($layout_name);
    }
    /**
     * 把 page tpl 的渲染结果当做 http 响应内容体发出去，不包含 skeleton。
     *
     *   self::send_page();                              // 
     *   self::send_page('page_name');                   // 
     *   self::send_page('page_name', $with_pps = true); // 
     */
    public static function /* @controller */ send_page(/* ... */) {
        $func_args = func_get_args();
        $num_args = func_num_args();
        $with_pps = parent::$skeleton !== false;
        if ($num_args === 0) {
            $target_name = self::get_target_name_from_page_name();
        } else if ($num_args === 1) {
            if (is_bool($func_args[0])) {
                $target_name = self::get_target_name_from_page_name();
                $with_pps = $func_args[0];
            } else {
                $target_name = self::get_target_name_from_page_name($func_args[0]);
            }
        } else {
            if (is_bool($func_args[1])) {
                $target_name = self::get_target_name_from_page_name($func_args[0]);
                $with_pps = $func_args[1];
            } else {
                throw new developer_error('bad call to send_page');
            }
        }
        $target = new target($target_name);
        $html = self::render_tpl('page/' . $target->get_target_file() . '.tpl', context::get_escaped(), false);
        if (parent::$skeleton !== false && $with_pps) {
            ob_start();
            parent::echo_css_link(router::pps_url('pss.php?page=' . $target->get_target_name(), false));
            echo $html;
            parent::echo_js_link(router::pps_url('pjs.php?page=' . $target->get_target_name(), false));
            self::send(ob_get_clean());
        } else {
            self::send($html);
        }
    }
    /**
     * 把 block tpl 的渲染结果当做 http 响应内容体发出去，不包含 skeleton。
     *
     *   self::send_block('block_name');
     *   self::send_block('block_name', $alias);
     *   self::send_block('block_name', $with_pps = true);
     *   self::send_block('block_name', $with_pps = true, $alias);
     */
    public static function /* @controller */ send_block(/* ... */) {
        $func_args = func_get_args();
        $num_args = func_num_args();
        $block_name = $func_args[0];
        $alias = [];
        $with_pps = parent::$skeleton !== false;
        if ($num_args === 1) {
            /* do nothing */
        } else if ($num_args === 2) {
            if (is_array($func_args[1])) {
                $alias = $func_args[1];
            } else if (is_bool($func_args[1])) {
                $with_pps = $func_args[1];
            } else {
                throw new developer_error('bad call to send_block');
            }
        } else if ($num_args === 3) {
            $alias = $func_args[2];
            if (is_bool($func_args[1])) {
                $with_pps = $func_args[1];
            } else {
                throw new developer_error('bad call to send_block');
            }
        } else {
            throw new developer_error('bad call to send_block');
        }
        ob_start();
        parent::block($block_name, $alias);
        $html = ob_get_clean();
        if (parent::$skeleton !== false && $with_pps) {
            ob_start();
            parent::echo_css_link(router::pps_url('pss.php?block=' . $block_name, false));
            echo $html;
            parent::echo_js_link(router::pps_url('pjs.php?block=' . $block_name, false));
            self::send(ob_get_clean());
        } else {
            self::send($html);
        }
    }
    public static function /* @controller */ json_result($result, $msg = '', $code = 0, $extra = '') {
        self::send_json(['result' => $result, 'msg' => $msg, 'code' => $code, 'extra' => $extra]);
    }
    public static function /* @controller */ send_json($value) {
        self::send(json_encode($value), visitor::is_ie() ? 'text/plain; charset=utf-8' : 'application/json');
    }
    public static function /* @controller */ puts($str) {
        visitor::add_content($str);
    }
    /**
     * 把 $content 当成 http 响应的内容体，并把 http 响应发送出去。
     */
    public static function /* @controller */ send($content, $content_type = 'text/html; charset=utf-8') {
        visitor::set_content_type($content_type);
        visitor::set_content($content);
        throw new action_return();
    }
    /**
     * 把 $file 的文件内容当成 http 响应的内容体，并把 http 响应发送出去，供客户端下载（显示的下载文件名为 $filename_ext）。
     */
    public static function /* @controller */ send_file($file, $filename_ext = '') {
        if ($filename_ext === '') {
            $filename_ext = basename($file);
        }
        if (visitor::is_ie()) {
            $filename_ext = urlencode($filename_ext);
            $content_type = 'application/force-download';
        } else {
            $content_type = 'application/octet-stream';
        }
        visitor::set_header('Content-Disposition', 'attachment; filename=' . $filename_ext);
        visitor::set_header('Content-Transfer-Encoding', 'binary');
        $content = file_get_contents($file);
        visitor::set_header('Content-Length', strlen($content));
        self::send($content, $content_type);
    }
    public static function /* @controller */ redirect_to($target) {
        self::redirect_to_url(router::build_php_url($target, false, false));
    }
    public static function /* @controller */ redirect_to_url($url) {
        visitor::redirect_to_url($url);
        throw new action_return();
    }
    public static function /* @controller */ forward_to($target, $deny_self = true) {
        if (!$target instanceof target) {
            $target = new target($target);
        }
        if ($deny_self && $target->get_target_name() === parent::$target->get_target_name()) {
            throw new developer_error('cannot forward to self');
        }
        $forward = new action_forward();
        $forward->set_target($target);
        throw $forward;
    }
    public static function /* @controller */ forward_404($msg = '') {
        throw new visitor_except($msg, 404);
    }
    public static function /* @controller */ forward_404_if($condition, $msg = '') {
        if ($condition) {
            throw new visitor_except($msg, 404);
        }
    }
    public static function /* @controller */ set_title($title, $escape_html = true) {
        self::$title = $escape_html ? html::escape($title) : $title;
    }
    public static function /* @controller */ add_title($title, $escape_html = true) {
        $new_title = self::$title . $title;
        self::$title = $escape_html ? html::escape($new_title) : $new_title;
    }
    public static function /* @controller */ set_keywords($keywords, $escape_html = true) {
        self::$keywords = $escape_html ? html::escape($keywords) : $keywords;
    }
    public static function /* @controller */ set_description($description, $escape_html = true) {
        self::$description = $escape_html ? html::escape($description) : $description;
    }
    public static function /* @controller */ set_author($author, $escape_html = true) {
        self::$author = $escape_html ? html::escape($author) : $author;
    }
    public static function /* @controller */ set_viewport($viewport, $escape_html = true) {
        self::$viewport = $escape_html ? html::escape($viewport) : $viewport;
    }
    public static function /* @controller */ add_meta($meta_key, $meta_value, $escape_html = true) {
        if ($escape_html) {
            self::$metas[html::escape($meta_key)] = html::escape($meta_value);
        } else {
            self::$metas[$meta_key] = $meta_value;
        }
    }
    protected static function /* @tpl */ content() {
        if (self::$target_block === []) { # 渲染 page
            echo self::render_tpl('page/' . parent::$target->get_target_file() . '.tpl', context::get_escaped(), false);
        } else { # 渲染 block
            parent::block(self::$target_block['name'], self::$target_block['alias']);
        }
    }
    protected static function /* @tpl */ echo_metas() {
        if (self::$keywords !== '') {
            echo '<meta name="keywords" content="' . self::$keywords . '">' . "\n";
        }
        if (self::$description !== '') {
            echo '<meta name="description" content="' . self::$description . '">' . "\n";
        }
        if (self::$author !== '') {
            echo '<meta name="author" content="' . self::$author . '">' . "\n";
        }
        if (self::$viewport !== '') {
            echo '<meta name="viewport" content="' . self::$viewport . '">' . "\n";
        }
        foreach (self::$metas as $name => $content) {
            echo '<meta name="' . $name . '" content="' . $content . '">' . "\n";
        }
    }
    protected static function /* @tpl */ echo_title() {
        echo '<title>' . self::$title . '</title>' . "\n";
    }
    protected static function /* @tpl */ echo_favicon() {
        $favicon = router::web_url('favicon.ico');
        $version = router::get_version();
        $version_str = $version === 0 ? '' : '?' . router::get_version_key() . '=' . $version;
        echo '<link rel="shortcut icon" href="' . $favicon . $version_str . '" type="image/x-icon">' . "\n";
    }
    protected static function /* @tpl */ echo_top_links() {
        if (parent::$skeleton === false) {
            return;
        }
        // link 进来的 css
        foreach (parent::$linked_styles as $style_file) {
            parent::echo_css_link(parent::static_url($style_file, null, false));
        }
        // link 进来的 pss
        $psses = [];
        if (parent::$linked_psses === []) {
            $psses[] = 'link=global';
        } else {
            $linked_psses = parent::$linked_psses;
            if (!array_key_exists('global', $linked_psses)) {
                array_unshift($linked_psses, 'global');
            }
            $psses[] = 'link=' . implode(',', $linked_psses);
        }
        if (parent::$layout_pss !== '') {
            $psses[] = 'layout=' . parent::$layout_pss;
        }
        if (parent::$page_pss !== '') {
            $psses[] = 'page=' . parent::$page_pss;
        }
        if (parent::$block_psses !== []) {
            $psses[] = 'block=' . implode(',', parent::$block_psses);
        }
        parent::echo_css_link(router::pps_url('pss.php?' . implode(';', $psses), false));
        // link 进来的放在顶部的 js
        foreach (parent::$linked_scripts['top'] as $script_file) {
            parent::echo_js_link(parent::static_url($script_file, null, false));
        }
        // link 进来的放在顶部的 pjs
        if (parent::$linked_pjses['top'] !== []) {
            $linked_top_pjses = parent::$linked_pjses['top'];
            if (!array_key_exists('global', $linked_top_pjses)) {
                array_unshift($linked_top_pjses, 'global');
            }
            parent::echo_js_link(router::pps_url('pjs.php?link=' . implode(',', $linked_top_pjses), false));
        }
    }
    protected static function /* @tpl */ echo_bottom_links() {
        if (parent::$skeleton === false) {
            return;
        }
        // link 进来的放在底部的 js
        foreach (parent::$linked_scripts['bottom'] as $script_file) {
            parent::echo_js_link(parent::static_url($script_file, null, false));
        }
        // link 进来的放在底部的 pjs
        $pjses = [];
        if (parent::$linked_pjses['bottom'] === []) {
            if (parent::$linked_pjses['top'] === []) {
                $pjses[] = 'link=global';
            }
        } else {
            $linked_bottom_pjses = parent::$linked_pjses['bottom'];
            if (parent::$linked_pjses['top'] === [] && !array_key_exists('global', $linked_bottom_pjses)) {
                array_unshift($linked_bottom_pjses, 'global');
            }
            $pjses[] = 'link=' . implode(',', $linked_bottom_pjses);
        }
        if (parent::$layout_pjs !== '') {
            $pjses[] = 'layout=' . parent::$layout_pjs;
        }
        if (parent::$page_pjs !== '') {
            $pjses[] = 'page=' . parent::$page_pjs;
        }
        if (parent::$block_pjses !== []) {
            $pjses[] = 'block=' . implode(',', parent::$block_pjses);
        }
        if ($pjses !== []) {
            parent::echo_js_link(router::pps_url('pjs.php?' . implode(';', $pjses), false));
        }
    }
    protected static function /* @kern */ get_target_name_from_page_name($page_name = null) {
        if ($page_name === null || $page_name === '') {
            $target_name = parent::$target->get_target_name();
        } else if (in_string('-', $page_name)) {
            $target_name = $page_name;
        } else if (in_string('/', $page_name)) {
            if (parent::$target->has_module()) {
                $target_name = parent::$target->get_module_name() . '-' . $page_name;
            } else {
                $target_name = $page_name;
            }
        } else {
            $target_name = parent::$target->get_controller_name() . '/' . $page_name;
            if (parent::$target->has_module()) {
                $target_name = parent::$target->get_module_name() . '-' . $target_name;
            }
        }
        return $target_name;
    }
    protected static function /* @kern */ show_with_layout($_layout_name) {
        if ($_layout_name === null) {
            $_controller = get_called_class();
            if (property_exists($_controller, 'layout')) {
                if ($_controller::$layout === null) {
                    throw new developer_error('default layout cannot be null (which itself means use default layout...)');
                }
                $_layout_name = $_controller::$layout;
            } else {
                $_layout_name = false;
            }
        }
        ob_start();
        if ($_layout_name === false) {
            self::content();
        } else {
            parent::$layout_pss = parent::$layout_pjs = $_layout_name;
            // layout tpl 里会调 self::content()
            echo self::render_tpl('layout/' . $_layout_name . '.tpl', context::get_escaped(), false);
        }
        $_html = ob_get_clean();
        if (parent::$skeleton !== false) {
            ob_start();
            // skeleton 对应的 tpl 里会 echo $_html;
            require view_dir . '/skeleton/' . parent::$skeleton . '.tpl';
            self::send(ob_get_clean());
        } else {
            self::send($_html);
        }
    }
    // 可 reset 的属性
    protected static $title = '';
    protected static $keywords = '';
    protected static $description = '';
    protected static $author = '';
    protected static $viewport = '';
    protected static $metas = [];
    protected static $target_block = [];
}
// [实体] block 模板控制器助手
abstract class helper extends tpl_rendor {
    // abstract public static function run(array $context);
    public static function /* @run */ set($key, $value, $to_context = false) {
        if ($to_context) {
            context::set($key, $value);
        } else {
            self::$vars[$key] = html::escape($value);
        }
    }
    public static function /* @run */ render() {
        $args = context::get_escaped(self::$alias);
        foreach (self::$vars as $name => $value) {
            // helper set 到 block 模板的变量都以下标开始
            $args['_' . $name] = $value;
        }
        echo self::render_tpl('block/' . self::$block_name . '.tpl', $args, false);
    }
    public static function /* @kern */ set_block_name($block_name) {
        self::$block_name = $block_name;
    }
    public static function /* @kern */ set_alias($alias) {
        self::$alias = $alias;
    }
    public static function /* @kern */ reset() {
        self::$vars = [];
    }
    // 可 reset 的属性
    protected static $block_name = '';
    protected static $alias = [];
    protected static $vars = [];
}
// [实体] 前置拦截过滤器
abstract class before_filter {
    // abstract public static function run($args);
    protected static function /* @run */ forward_to($target) {
        $forward = new action_forward();
        if (!$target instanceof target) {
            $target = new target($target);
        }
        $forward->set_target($target);
        throw $forward;
    }
    protected static function /* @run */ forward_403($msg = '') {
        throw new visitor_except($msg, 403);
    }
    protected static function /* @run */ forward_404($msg = '') {
        throw new visitor_except($msg, 404);
    }
    protected static function /* @run */ forward_405($msg = '') {
        throw new visitor_except($msg, 405);
    }
    protected static function /* @run */ forward_406($msg = '') {
        throw new visitor_except($msg, 406);
    }
    protected static function /* @run */ json_result($result, $msg = '', $code = 0, $extra = '') {
        self::send_json(['result' => $result, 'msg' => $msg, 'code' => $code, 'extra' => $extra]);
    }
    protected static function /* @run */ send_json($value) {
        self::send(json_encode($value), visitor::is_ie() ? 'text/plain; charset=utf-8' : 'application/json');
    }
    protected static function /* @run */ send($content, $content_type = 'text/html; charset=utf-8') {
        visitor::set_content_type($content_type);
        visitor::set_content($content);
        throw new dispatch_return();
    }
}
// [实体] 预先加载拦截过滤器
class preload_filter extends before_filter {
    public static function run(array $files) {
        // 例如：[kern\utility_dir . '/utility_one.php', kern\utility_dir . '/utility_two.php']
        foreach ($files as $file) {
            loader::load_file($file);
        }
    }
}
// [实体] 后置拦截过滤器
abstract class after_filter {
    // abstract public static function run($args);
}
// [实体] 变量上下文容器
class /* @kern */ context {
    public static function set($key, $value) {
        self::$primary[$key] = $value;
        self::$escaped[$key] = html::escape($value);
    }
    public static function get_primary(array $alias = []) {
        return self::get_property('primary', $alias);
    }
    public static function get_escaped(array $alias = []) {
        return self::get_property('escaped', $alias);
    }
    protected static function get_property($property_name, array $alias) {
        $context = self::${$property_name};
        foreach ($alias as $new_name => $original_name) {
            $context[$new_name] = array_key_exists($original_name, $context) ? $context[$original_name] : null;
        }
        return $context;
    }
    protected static $primary = []; # 原始值
    protected static $escaped = []; # html 转义过的值
}
// [类型] 分派返回标志
class /* @kern */ dispatch_return extends \Exception {}
// [类型] action 返回标志
class /* @kern */ action_return extends \Exception {}
// [类型] action 转移标志
class /* @kern */ action_forward extends \Exception {
    public function set_target(target $target) {
        $this->target = $target;
    }
    public function get_target() {
        return $this->target;
    }
    protected $target = null;
}
