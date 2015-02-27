<?php
use swap\clock;
use swap\visitor;
use swap\lazy_checker;
use swap\check_failed;
class setting_controller extends swap\controller {
    public static $layout = 'two';
    public static function edit_action() {
        // 拦截
        self::role('member');
        if (visitor::is_post()) {
            // 拦截
            self::csrf('member');
            try {
                // 校验
                $checker = new lazy_checker(p());
                $checker->check(setting_model::id_blog_name, array(
                    'change_to' => 'string',
                    'cannot_be' => array('', ''),
                ));
                $checker->check(setting_model::id_blog_description, array(
                    'change_to' => 'string',
                    'cannot_be' => array('', ''),
                ));
                $checker->check(setting_model::id_blog_keywords, array(
                    'change_to' => 'string',
                    'cannot_be' => array('', ''),
                ));
                $checker->check(setting_model::id_copyright, array(
                    'change_to' => 'string',
                    'cannot_be' => array('', ''),
                ));
                $checker->check(setting_model::id_captcha_question, array(
                    'change_to' => 'string',
                    'cannot_be' => array('', ''),
                ));
                $checker->check(setting_model::id_captcha_answer, array(
                    'change_to' => 'string',
                    'cannot_be' => array('', ''),
                ));
                // 执行
                $props = $checker->get_all();
                foreach ($props as $id => $value) {
                    setting_model::set_by_id(array('value' => $value), $id);
                }
                // 成功
                self::json_result(true, '', 0, url('setting/edit'));
            } catch (check_failed $e) {
                // 失败
                self::json_result(false, $e->get_reasons());
            }
        } else {
            // 呈现
            self::set('settings', setting_service::get_settings());
            self::show_page();
        }
    }
    public static function about_action() {
        // 拦截
        self::role('member');
        $setting = setting_model::get_by_id(setting_model::id_about);
        if (visitor::is_post()) {
            // 拦截
            self::csrf('member');
            try {
                // 校验
                $checker = new lazy_checker(p());
                $checker->check('content', array(
                    'change_to' => 'string',
                    'cannot_be' => array('', '关于不能为空'),
                ));
                // 执行
                $setting->value = $checker->content;
                $setting->save();
                // 成功
                self::json_result(true, '关于信息编辑成功。', 0, url('site/about'));
            } catch (check_failed $e) {
                // 失败
                self::json_result(false, $e->get_reasons());
            }
        } else {
            // 呈现
            self::set('content', $setting->value);
            self::show_page();
        }
    }
}
