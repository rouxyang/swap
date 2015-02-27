<?php
use swap\clock;
use swap\visitor;
use swap\binder;
use swap\lazy_checker;
use swap\check_failed;
class topic_controller extends swap\controller {
    public static $layout = 'main';
    public static function show_action() {
        $topic_id = g_int('id');
        $topic = topic_model::get_by_id($topic_id);
        binder::bind($topic, 'belongs_to', 'user');
        self::forward_404_if($topic === null, '主题不存在。');
        self::set('topic', $topic);
        $board = board_model::get_by_id($topic->board_id);
        self::set('board', $board);
        $page = g_int('page', 1);
        list($pager, $replies) = reply_model::pager(array('topic_id' => $topic_id), array(array('id' => 'ASC'), $page, 10));
        binder::bind($replies, 'belongs_to', 'user');
        $pager['target'] = 'topic/show?id=' . $topic_id;
        if ($replies === []) {
            $replies = array($topic);
        } else if ($page === 1) {
            array_unshift($replies, $topic);
        }
        self::set('pager', $pager);
        self::set('replies', $replies);
        $logined = visitor::has_role('user');
        self::set('logined', $logined);
        self::show_page();
    }
    public static function new_action() {
        self::role('user', 'forward_to', 'site/login');
        self::set('board_id', g_int('board_id', 1));
        self::show_page();
    }
    public static function do_new_action() {
        self::method('post');
        self::role('user');
        self::csrf('user');
        $board_id = g_int('board_id', 1);
        $board = board_model::get_by_id($board_id);
        try {
            $checker = new lazy_checker(p());
            $checker->check('title', array(
                'change_to' => 'string',
                'char_size' => array(3, topic_model::title_len, '标题长度为%d到%d个字符'),
            ));
            $checker->check('content', array(
                'change_to' => 'string',
                'char_size' => array(6, topic_model::content_len, '内容长度为%d到%d个字符'),
            ));
            $user_id = visitor::get_role_id('user');
            $topic = array(
                'board_id' => $board_id,
                'user_id'  => $user_id,
                'pub_time' => clock::get_stamp(),
                'title'    => $checker->title,
                'content'  => $checker->content,
            );
            $topic_id = topic_service::new_topic($topic);
            self::json_result(true, '', 0, url('topic/show?id=' . $topic_id));
        } catch (check_failed $e) {
            self::json_result(false, $e->get_reasons());
        }
    }
}
