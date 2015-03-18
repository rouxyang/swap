<?php
use kern\clock;
use kern\visitor;
use kern\lazy_checker;
use kern\check_failed;
class member_controller extends kern\controller {
    public static $layout = 'two';
    public static function index_action() {
        // 拦截
        self::role('member');
        // 呈现
        self::set('members', member_model::get_all());
        self::show_page();
    }
    public static function edit_action() {
        // 拦截
        self::role('member');
        $member = member_model::get_by_id(g_int('id'));
        self::forward_404_if($member === null, '用户不存在');
        if (visitor::is_post()) {
            // 拦截
            self::csrf('member');
            try {
                // 校验
                $checker = new lazy_checker(p());
                $checker->check_model_rules('member', 'edit', $member);
                // 执行
                $member->name = $checker->name;
                $pass = $checker->pass;
                if ($pass !== '') {
                    $member->set_pass($pass);
                }
                $member->save();
                visitor::set_role_var('member', 'name', $member->name);
                // 成功
                self::json_result(true, '用户编辑成功。', 0, url('member/index'));
            } catch (check_failed $e) {
                // 失败
                self::json_result(false, $e->get_reasons());
            }
        } else {
            // 呈现
            self::set('member', $member);
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
            $checker->check_model_rules('member', 'new');
            // 执行
            $member = [];
            $member['name'] = $checker->name;
            $salt = random_sha1();
            $member['salt'] = $salt;
            $member['pass'] = member_model::generate_pass($checker->pass, $salt);
            member_model::add($member);
            // 成功
            self::json_result(true, '用户添加成功。', 0, url('member/index'));
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
        # @todo: 级联删除
        # member_model::del_by_id(g_int('id'));
        // 成功
        self::send_json('暂不支持删除用户');
    }
}
