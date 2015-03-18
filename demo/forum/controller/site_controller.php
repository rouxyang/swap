<?php
use kern\visitor;
use kern\lazy_checker;
use kern\check_failed;
class site_controller extends kern\controller {
    public static $layout = 'main';
    public static function index_action() {
        self::forward_to('board/index');
    }
    public static function about_action() {
        self::show_page();
    }
    public static function login_action() {
        self::show_page();
    }
    public static function do_login_action() {
        self::method('post');
        try {
            $checker = new lazy_checker(p());
            $checker->check('name', array(
                'change_to' => 'string',
                'cannot_be' => array('', '用户不能为空'),
            ));
            $checker->check('pass', array(
                'change_to' => 'string',
                'cannot_be' => array('', '密码不能为空'),
            ));
            $user = user_model::get_one(array('name' => $checker->name));
            if ($user === null) {
                $checker->failed('name', '用户不存在');
            } else if (!$user->is_valid_pass($checker->pass)) {
                $checker->failed('pass', '密码不正确');
            } else {
                visitor::set_role('user', $user->id, p_has('remember') ? 30 * 86400 : 0, array('name' => $user->name));
                online_service::refresh_online_count();
                self::json_result(true, '', 0, url('site/index'));
            }
        } catch (check_failed $e) {
            self::json_result(false, $e->get_reasons());
        }
    }
    public static function logout_action() {
        self::csrf('user');
        visitor::del_role('user');
        online_service::refresh_online_count();
        self::redirect_to('site/index');
    }
}
