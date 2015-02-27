<?php
use swap\visitor;
use swap\lazy_checker;
use swap\check_failed;
class site_controller extends swap\controller {
    public static function index_action() {
        self::forward_to('post/index');
    }
    public static function about_action() {
        // 呈现
        self::set('about', setting_model::get_about());
        list($pager, $messages) = message_model::pager_all(array(array('id' => 'DESC'), g_int('page', 1), 10));
        $pager['target'] = 'site/about';
        self::set('pager', $pager);
        self::set('messages', $messages);
        self::set('logined', visitor::has_role('member'));
        self::set('captcha_question', setting_model::get_by_id(setting_model::id_captcha_question)->value);
        self::show_page('', 'three');
    }
    public static function search_action() {
        // 呈现
        self::show_page('', 'four');
    }
    public static function login_action() {
        // 呈现
        self::show_page('', 'three');
    }
    public static function do_login_action() {
        // 拦截
        self::method('post');
        try {
            // 校验
            $checker = new lazy_checker(p());
            $checker->check('name', array(
                'change_to' => 'string',
                'cannot_be' => array('', '用户不能为空'),
            ));
            $checker->check('pass', array(
                'change_to' => 'string',
                'cannot_be' => array('', '密码不能为空'),
            ));
            // 执行
            $member = member_model::get_one(array('name' => $checker->name));
            if ($member === null) {
                $checker->failed('name', '用户名不正确');
            }
            if (!$member->is_valid_pass($checker->pass)) {
                $checker->failed('pass', '密码不正确');
            }
            visitor::set_role('member', $member->id, p_has('remember') ? 30 * 86400 : 0, []);
            // 成功
            self::json_result(true, '登录成功', 0, url('site/admin'));
        } catch (check_failed $e) {
            // 失败
            self::json_result(false, $e->get_reasons());
        }
    }
    public static function logout_action() {
        // 拦截
        self::csrf('member');
        // 执行
        visitor::del_role('member');
        self::redirect_to('site/index');
    }
    public static function admin_action() {
        // 拦截
        self::role('member');
        // 呈现
        $member = member_model::get_by_id(visitor::get_role_id('member'));
        self::set('name', $member->name);
        self::show_page('', 'two');
    }
}
