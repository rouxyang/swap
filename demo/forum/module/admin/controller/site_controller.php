<?php
use kern\visitor;
use kern\lazy_checker;
use kern\check_failed;
class site_controller extends kern\controller {
    public static function index_action() {
        self::role('admin', 'forward_to', 'admin-site/login');
        self::show_page('', 'admin/main');
    }
    public static function login_action() {
        self::show_page('', false);
    }
    public static function do_login_action() {
        self::method('post');
        try {
            $checker = new lazy_checker(p());
            $checker->check('name', array(
                'change_to' => 'string',
                'cannot_be' => array('', '用户名不能为空'),
            ));
            $checker->check('pass', array(
                'change_to' => 'string',
                'cannot_be' => array('', '密码不能为空'),
            ));
            $admin = admin_model::get_one(array('name' => $checker->name));
            if ($admin === null) {
                $checker->failed('name', '用户不存在。');
            } else if (!$admin->is_valid_pass($checker->pass)) {
                $checker->failed('pass', '密码不正确');
            } else {
                visitor::set_role('admin', $admin->id, p_has('remember') ? 30 * 86400 : 0, array('name' => $admin->name));
                self::json_result(true, '', 0, url('admin-site/index'));
            }
        } catch (check_failed $e) {
            self::json_result(false, $e->get_reasons());
        }
    }
    public static function logout_action() {
        self::csrf('admin');
        visitor::del_role('admin');
        self::redirect_to('admin-site/login');
    }
}
