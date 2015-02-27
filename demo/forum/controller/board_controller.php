<?php
use swap\visitor;
use swap\session_manager;
use swap\cache_pool;
use swap\binder;
class board_controller extends swap\controller {
    public static $layout = 'main';
    public static function index_action() {
        $boards = board_model::get_all();
        binder::bind($boards, 'many_many', 'user', array('manager', 0));
        self::set('boards', $boards);
        self::set('online_count', online_service::get_online_count());
        self::show_page();
    }
    public static function show_action() {
        $board_id = g_int('id', 0);
        $board = board_model::get_by_id($board_id);
        self::forward_404_if($board === null, '板块不存在。');
        self::set('board', $board);
        $order_limit = array(array('id' => 'DESC'), g_int('page', 1), 10);
        list($pager, $topics) = topic_model::pager_with_count($board->topic_count, array('board_id' => $board_id), $order_limit);
        binder::bind($topics, 'belongs_to', 'user');
        $pager['target'] = 'board/show?id=' . $board_id;
        self::set('pager', $pager);
        self::set('topics', $topics);
        self::show_page();
    }
}
