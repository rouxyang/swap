<?php
use swap\clock;
use swap\visitor;
use swap\lazy_checker;
use swap\check_failed;
class user_controller extends swap\controller {
    public static $layout = 'main';
    public static function show_action() {
        self::show_page();
    }
    public static function info_action() {
        self::role('user');
        self::show_page();
    }
    public static function setting_action() {
        self::role('user');
        self::show_page();
    }
    public static function do_setting_action() {
        self::method('post');
        self::role('user');
        self::csrf('user');
        try {
            $checker = new lazy_checker(p());
            $checker->check('pass', array(
                'change_to' => 'string',
                'pass_if_be' => '',
                'char_size' => array(6, -1, ''),
            ));
            $checker->check('new_pass', array(
                'change_to' => 'string',
                'pass_if_be' => '',
                'char_size' => array(6, -1, ''),
            ));
            if (p_has('avatar')) {
                $checker->check('avatar', array(
                    'change_to' => 'array',
                ));
            }
            $pass = $checker->pass;
            if ($pass !== '') {
                $user_id = visitor::get_role_id('user');
                $user = user_model::get_by_id($user_id);
                if (!$user->is_valid_pass($checker->pass)) {
                    $checker->failed('pass', '原密码不正确。');
                } else if ($checker->new_pass !== p_str('re_pass')) {
                    $checker->failed('new_pass', '两次输入的密码不相同。');
                } else {
                    $user->change_pass_to($checker->new_pass);
                    $user->save();
                }
            }
            self::json_result(true, '', 0, url('user/setting'));
        } catch (check_failed $e) {
            self::json_result(false, $e->get_reasons());
        }
    }
    public static function register_action() {
        self::show_page();
    }
    public static function do_register_action() {
        self::method('post');
        try {
            $checker = new lazy_checker(p());
            $checker->check('name', array(
                'change_to' => 'string',
                'cannot_be' => array('', '用户名不能为空'),
                'char_size' => array(3, user_model::name_len, '用户名的长度为%d-%d个字符'),
                'callback' => function ($name) {
                    if (user_model::get_one(array('name' => $name)) !== null) {
                        return '该用户已被注册';
                    }
                },
            ));
            $checker->check('pass', array(
                'change_to' => 'string',
                'cannot_be' => array('', '密码不能为空'),
                'char_size' => array(6, -1, '密码长度必须大于等于%d个字符'),
            ));
            if (p_str('re_pass') !== $checker->pass) {
                $checker->failed('pass', '两次输入的密码不一致');
            }
            $salt = random_sha1();
            $pass = user_model::get_crypted_pass($checker->pass, $salt);
            $user = array(
                'name' => $checker->name,
                'pass' => $pass,
                'salt' => $salt,
                'register_time' => clock::get_stamp(),
            );
            $user_id = user_model::add($user);
            visitor::set_role('user', $user_id, 0, array('name' => $user['name']));
            online_service::refresh_online_count();
            self::json_result(true, '', 0, url('site/index'));
        } catch (check_failed $e) {
            self::json_result(false, $e->get_reasons());
        }
    }
}
