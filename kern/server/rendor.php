<?php
/**
 * 视图（tpl, pss, pjs）渲染器
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
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
    protected static function /* @kern */ use_viewlet($viewlet_name) {
        if (defined('kern\view_dir')) {
            $viewlet_file = view_dir . '/viewlet/' . $viewlet_name . '.php';
            if (is_readable($viewlet_file)) {
                loader::load_file($viewlet_file);
            }
        }
    }
    protected static function /* @kern */ regularize($target, $for_html) {
        if ($for_html === null) { # 如果是 null，则根据当前运行模式自动判断
            $for_html = !kernel::is_pps_mode();
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
