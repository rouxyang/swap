<?php
/**
 * 收集和渲染动态的 pss 和 pjs 文件
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
// [实体] PSS, PJS 渲染器
class pps_rendor extends rendor {
    public static function /* @swap */ render_for(target $target) {
        visitor::set_target($target);
        return framework::is_pss_mode() ? self::render_pss_for($target) : self::render_pjs_for($target);
    }
    protected static function /* @swap */ render_pss_for(target $target) {
        parent::use_viewlet('pss');
        parent::use_app_viewlet('pss');
        ob_start();
        self::do_render_in(view_dir, 'pss', $target);
        $pss = ob_get_clean();
        if (setting::get_module('view.minify_pps', false)) {
            $pss = self::minify_pss($pss);
        }
        return $pss;
    }
    protected static function /* @swap */ render_pjs_for(target $target) {
        parent::use_viewlet('pjs');
        parent::use_app_viewlet('pjs');
        ob_start();
        self::do_render_in(view_dir, 'pjs', $target);
        $pjs = ob_get_clean();
        if (setting::get_module('view.minify_pps', false)) {
            $pjs = self::minify_pjs($pjs);
        }
        return $pjs;
    }
    protected static function /* @swap */ do_render_in($_view_dir, $_pps_type, target $_target) {
        foreach ($_target->get_param('link', []) as $_linked_name) {
            $_file = $_pps_type . '/' . $_linked_name . '.' . $_pps_type;
            $_pps_file = $_view_dir . '/' . $_file;
            if (is_readable($_pps_file)) {
                if (framework::is_debug()) {
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
                if (framework::is_debug()) {
                    echo "\n/******** {$_file} ********/\n\n";
                }
                require $_pps_file;
            }
        }
        foreach ($_target->get_param('block', []) as $_block_name) {
            $_file = 'block/' . $_block_name . '.' . $_pps_type;
            $_pps_file = $_view_dir . '/' . $_file;
            if (is_readable($_pps_file)) {
                if (framework::is_debug()) {
                    echo "\n/******** {$_file} ********/\n\n";
                }
                require $_pps_file;
            }
        }
        if ($_target->get_target_name() !== '') {
            $_file = 'page/' . $_target->get_target_file('.' . $_pps_type);
            $_pps_file = $_view_dir . '/' . $_file;
            if (is_readable($_pps_file)) {
                if (framework::is_debug()) {
                    echo "\n/******** {$_file} ********/\n\n";
                }
                require $_pps_file;
            }
        }
    }
    protected static function /* @swap */ minify_pss($pss) {
        // @todo: cssmin
        return $pss;
    }
    protected static function /* @swap */ minify_pjs($pjs) {
        // @todo: jsmin
        return $pjs;
    }
}
