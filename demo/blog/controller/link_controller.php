<?php
use swap\visitor;
use swap\lazy_checker;
use swap\check_failed;
class link_controller extends swap\controller {
    public static $layout = 'two';
    public static function index_action() {
        // 呈现
        self::role('member');
        self::set('links', link_model::get_all());
        self::show_page();
    }
    public static function edit_action() {
        // 拦截
        self::role('member');
        $id = g_int('id');
        $link = link_model::get_by_id($id);
        self::forward_404_if($link === null, '链接不存在');
        if (visitor::is_post()) {
            // 拦截
            self::csrf('member');
            try {
                // 校验
                $checker = new lazy_checker(p());
                $checker->check_model_rules('link');
                // 执行
                $link->add_props($checker->get_all());
                $link->save();
                // 成功
                self::json_result(true, '链接编辑成功。', 0, url('link/index'));
            } catch (check_failed $e) {
                // 失败
                self::json_result(false, $e->get_reasons());
            }
        } else {
            // 呈现
            self::set('link', $link);
            self::show_page();
        }
    }
    public static function new_action() {
        // 拦截
        self::method('post');
        self::role('member');
        self::csrf('member');
        try {
            // 校验
            $checker = new lazy_checker(p());
            $checker->check_model_rules('link');
            // 执行
            link_model::add($checker->get_all());
            // 成功
            self::json_result(true, '链接创建成功。', 0, url('link/index'));
        } catch (check_failed $e) {
            // 失败
            self::json_result(false, $e->get_reasons());
        }
    }
    public static function delete_action() {
        // 拦截
        self::method('delete');
        self::role('member');
        self::csrf('member');
        // 校验
        // 执行
        link_model::del_by_id(g_int('id'));
        // 成功
        self::send_json(true);
    }
}
