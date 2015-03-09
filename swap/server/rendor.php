<?php
/**
 * 视图（tpl, pss, pjs）渲染器
 *
 * @copyright Copyright (c) 2009-2014 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
// [类型] 分派返回标志
class /* @swap */ dispatch_return extends \Exception {}
// [实体] 分派器
class /* @swap */ dispatcher {
    public static function dispatch_pps() {
        $uri = visitor::uri();
        $target = router::parse_pps_uri($uri);
        if (setting::get_module('view.use_skeleton', true)) {
            if (setting::get_module('view.cache_pps_in_server', false)) {
                $use_cache = false;
                if (defined('swap\data_dir')) {
                    $version_key = setting::get_swap('version_key', router::default_version_key);
                    $cache_dir = data_dir . '/cache/' . $serve_mode . '/' . $target->get_param($version_key, '0');
                    $cache_file = $cache_dir . '/' . sha1($uri) . '.cache';
                    if (is_readable($cache_file)) {
                        $use_cache = true;
                    }
                }
                if ($use_cache) {
                    $content = file_get_contents($cache_file);
                } else {
                    $content = pps_rendor::render_for($target);
                    if (!is_dir($cache_dir)) {
                        @mkdir($cache_dir, 0777, true);
                    }
                    @file_put_contents($cache_file, $content);
                }
            } else {
                $content = pps_rendor::render_for($target);
            }
        } else {
            $content = '';
        }
        visitor::set_content($content);
    }
    public static function dispatch_php() {
        if (defined('swap\utility_dir')) {
            $global_file = utility_dir . '/global.php';
            if (is_readable($global_file)) {
                loader::load_file($global_file);
            }
        }
        $target = router::parse_php_uri(visitor::uri(), visitor::host());
        $forward_times = 0;
        while (true) {
            if ($forward_times >= 8) {
                throw new developer_error('too many forwards');
            }
            self::$global_filters = setting::get_module('global_filters', null);
            try {
                self::dispatch_to($target);
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
    protected static function dispatch_to(target $target) {
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
            $filter = 'swap\\' . $filter;
            if (!class_exists($filter, true)) {
                throw new developer_error("cannot find filter: {$filter}");
            }
        }
        if (!is_subclass_of($filter, 'swap\\' . $filter_type . '_filter')) {
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
// [实体] 视图渲染器
abstract class rendor {
    protected static function /* @view */ php_url($target, $for_html = null, $echo = true) {
        list($target, $for_html) = self::regularize($target, $for_html);
        return router::build_php_url($target, $echo, $for_html, true);
    }
    protected static function /* @view */ php_uri($target, $for_html = null, $echo = true) {
        list($target, $for_html) = self::regularize($target, $for_html);
        return router::build_php_url($target, $echo, $for_html, false);
    }
    protected static function /* @view */ web_url($path) {
        return router::web_url($path);
    }
    protected static function /* @view */ static_url($static_file, $for_html = null, $echo = true) {
        return router::static_url(ltrim($static_file, '/'), $for_html, $echo);
    }
    protected static function /* @view */ upload_url($upload_file, $echo = true) {
        return router::upload_url($upload_file, $echo);
    }
    protected static function /* @view */ pss_url($pss_name, $echo = true) {
        return router::pps_url('pss.php?link=' . $pss_name, $echo);
    }
    protected static function /* @view */ pjs_url($pjs_name, $echo = true) {
        return router::pps_url('pjs.php?link=' . $pjs_name, $echo);
    }
    protected static function /* @swap */ use_viewlet($viewlet_name) {
        loader::load_file(swap_dir . '/server/view/viewlet/' . $viewlet_name . '.php');
    }
    protected static function /* @swap */ use_app_viewlet($viewlet_name) {
        if (defined('swap\view_dir')) {
            $viewlet_file = view_dir . '/viewlet/' . $viewlet_name . '.php';
            if (is_readable($viewlet_file)) {
                loader::load_file($viewlet_file);
            }
        }
    }
    protected static function /* @swap */ regularize($target, $for_html) {
        if ($for_html === null) { # 如果是 null，则根据当前运行模式自动判断
            $for_html = !framework::is_pps_mode();
        }
        if ($for_html) {
            if ($target instanceof target) {
                $target = $target->as_array();
            }
            $target = html::unescape($target);
        }
        return [$target, $for_html];
    }
}
