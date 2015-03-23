<?php
use kern\clock;
use kern\visitor;
use kern\lazy_checker;
use kern\check_failed;
class reply_controller extends kern\controller {
    public static $layout = 'main';
    public static function new_action() {
        self::method('post');
        self::role('user', 'forward_to', 'site/login');
        self::csrf('user');
        $topic_id = g_int('topic_id');
        $topic = topic_model::get_by_id($topic_id);
        self::forward_404_if($topic === null, '主题不存在。');
        try {
            $checker = new lazy_checker(p());
            $checker->check('content', array(
                'change_to' => 'string',
                'char_size' => array(6, reply_model::content_len, '内容长度为%d到%d个字符'),
            ));
            $pub_time = clock::get_stamp();
            $user_id = visitor::get_role_id('user');
            $reply = array(
                'board_id' => $topic->board_id,
                'topic_id' => $topic_id,
                'user_id'  => $user_id,
                'pub_time' => $pub_time,
                'content'  => $checker->content,
            );
            reply_service::new_reply($reply);
            self::json_result(true, '', 0, url('topic/show?id=' . $topic_id));
        } catch (check_failed $e) {
            self::json_result(false, $e->get_reasons());
        }
    }
}
