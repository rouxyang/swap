<?php
use kern\clock;
use kern\visitor;
use kern\lazy_checker;
use kern\check_failed;
class message_controller extends kern\controller {
    public static function edit_action() {
        // 拦截
        self::role('member');
        $message = message_model::get_by_id(g_int('id'));
        self::forward_404_if($message === null, '留言不存在');
        if (visitor::is_post()) {
            // 拦截
            self::csrf('member');
            try {
                // 校验
                $checker = new lazy_checker(p());
                $checker->check_model_rules('message');
                // 执行
                $message->add_props($checker->get_all());
                $message->save();
                // 成功
                self::json_result(true, '留言编辑成功。', 0, url('site/about'));
            } catch (check_failed $e) {
                // 失败
                self::json_result(false, $e->get_reasons());
            }
        } else {
            // 呈现
            self::set('message', $message);
            self::show_page('', 'two');
        }
    }
    public static function new_action() {
        // 拦截
        self::method('post');
        try {
            // 校验
            $checker = new lazy_checker(p());
            $checker->check('captcha', array(
                'should_be' => array(setting_model::get_by_id(setting_model::id_captcha_answer)->value, '验证码不正确'),
            ));
            $checker->del('captcha');
            $checker->check_model_rules('message');
            // 执行
            $message = $checker->get_all();
            $message['pub_time'] = clock::get_stamp();
            message_model::add($message);
            // 成功
            self::json_result(true, '留言成功。', 0, url('site/about'));
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
        message_model::del_by_id(g_int('id'));
        // 成功
        self::send_json(true);
    }
}
