<?php
use kern\clock;
use kern\visitor;
use kern\lazy_checker;
use kern\check_failed;
class comment_controller extends kern\controller {
    public static function edit_action() {
        // 拦截
        self::role('member');
        $comment = comment_model::get_by_id(g_int('id'));
        self::forward_404_if($comment === null, '评论不存在');
        if (visitor::is_post()) {
            // 拦截
            self::csrf('member');
            try {
                // 校验
                $checker = new lazy_checker(p());
                $checker->check_model_rules('comment');
                // 执行
                $comment->add_props($checker->get_all());
                $comment->save();
                // 成功
                self::json_result(true, '评论编辑成功。', 0, url('post/show?id=' . $comment->post_id));
            } catch (check_failed $e) {
                // 失败
                self::json_result(false, $e->get_reasons());
            }
        } else {
            // 呈现
            self::set('comment', $comment);
            self::show_page('', 'two');
        }
    }
    public static function new_action() {
        // 拦截
        self::method('post');
        $post_id = g_int('post_id');
        $post = post_model::get_by_id($post_id);
        self::forward_404_if($post === null, '文章不存在，无法评论');
        try {
            // 校验
            $checker = new lazy_checker(p());
            $checker->check('captcha', array(
                'should_be' => array(setting_model::get_by_id(setting_model::id_captcha_answer)->value, '验证码不正确'),
            ));
            $checker->del('captcha');
            $checker->check_model_rules('comment');
            $comment = $checker->get_all();
            if (!visitor::has_role('member') && member_model::get_one(array('name' => $comment['author'])) !== null) {
                $checker->failed('author', '您不能使用管理员的昵称');
            }
            // 执行
            $comment['post_id'] = $post_id;
            $comment['pub_time'] = clock::get_stamp();
            comment_model::add($comment);
            post_model::inc_by_id(array('comment_count' => 1), $post_id);
            setting_model::inc_by_id(array('value' => 1), setting_model::id_comment_count);
            // 成功
            self::json_result(true, '评论成功', 0, url('post/show?id=' . $post_id));
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
        comment_service::delete(g_int('id'));
        // 成功
        self::send_json(true);
    }
}
