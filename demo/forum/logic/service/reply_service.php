<?php
class reply_service {
    public static function new_reply(array $reply) {
        reply_model::add($reply);
        $user = user_model::get_by_id($reply['user_id']);
        topic_model::set_and_inc_by_id(array('last_post_user' => $user->name, 'last_post_time' => $reply['pub_time']), array('reply_count' => 1), $reply['topic_id']);
        board_model::set_and_inc_by_id(array('last_post_user' => $user->name, 'last_post_time' => $reply['pub_time']), array('reply_count' => 1), $reply['board_id']);
        user_model::inc_by_id(array('reply_count' => 1), $reply['user_id']);
    }
}
