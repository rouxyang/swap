<?php
/**
 * PPS 模式
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
// [实体] PPS 模式分派器
class /* @kern */ pps_dispatcher {
    public static function dispatch() {
        $uri = visitor::uri();
        $target = router::parse_pps_uri($uri);
        if (config::get_module('view.default_skeleton', false) !== false) {
            if (config::get_module('view.cache_pps_in_server', false)) {
                $use_cache = false;
                if (defined('kern\data_dir')) {
                    $version_key = config::get_kern('version_key', router::default_version_key);
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
}
// [实体] PSS, PJS 渲染器
class pps_rendor extends rendor {
    public static function /* @kern */ render_for(target $target) {
        visitor::set_target($target);
        return kernel::is_pss_mode() ? self::render_pss_for($target) : self::render_pjs_for($target);
    }
    protected static function /* @kern */ render_pss_for(target $target) {
        parent::use_viewlet('pss');
        ob_start();
        self::do_render_in(view_dir, 'pss', $target);
        $pss = ob_get_clean();
        if (config::get_module('view.minify_pps', false)) {
            $pss = self::minify_pss($pss);
        }
        return $pss;
    }
    protected static function /* @kern */ render_pjs_for(target $target) {
        parent::use_viewlet('pjs');
        ob_start();
        self::do_render_in(view_dir, 'pjs', $target);
        $pjs = ob_get_clean();
        if (config::get_module('view.minify_pps', false)) {
            $pjs = self::minify_pjs($pjs);
        }
        return $pjs;
    }
    protected static function /* @kern */ do_render_in($_view_dir, $_pps_type, target $_target) {
        foreach ($_target->get_param('link', []) as $_linked_name) {
            $_file = $_pps_type . '/' . $_linked_name . '.' . $_pps_type;
            $_pps_file = $_view_dir . '/' . $_file;
            if (is_readable($_pps_file)) {
                if (kernel::is_debug()) {
                    echo "\n/******** {$_file} ********/\n\n";
                }
                require $_pps_file;
            }
        }
        $_layout_name = $_target->get_param('layout', '');
        if ($_layout_name !== '') {
            $_file = 'layout/' . $_layout_name . '.' . $_pps_type;
            $_pps_file = $_view_dir . '/' . $_file;
            if (is_readable($_pps_file)) {
                if (kernel::is_debug()) {
                    echo "\n/******** {$_file} ********/\n\n";
                }
                require $_pps_file;
            }
        }
        foreach ($_target->get_param('block', []) as $_block_name) {
            $_file = 'block/' . $_block_name . '.' . $_pps_type;
            $_pps_file = $_view_dir . '/' . $_file;
            if (is_readable($_pps_file)) {
                if (kernel::is_debug()) {
                    echo "\n/******** {$_file} ********/\n\n";
                }
                require $_pps_file;
            }
        }
        if ($_target->get_target_name() !== '') {
            $_file = 'page/' . $_target->get_target_file('.' . $_pps_type);
            $_pps_file = $_view_dir . '/' . $_file;
            if (is_readable($_pps_file)) {
                if (kernel::is_debug()) {
                    echo "\n/******** {$_file} ********/\n\n";
                }
                require $_pps_file;
            }
        }
    }
    protected static function /* @kern */ minify_pss($pss) {
        // @todo: cssmin
        return $pss;
    }
    protected static function /* @kern */ minify_pjs($pjs) {
        // @todo: jsmin
        return $pjs;
    }
}
