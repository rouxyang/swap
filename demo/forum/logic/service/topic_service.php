<?php
class topic_service {
    public static function new_topic(array $topic) {
        $topic_id = topic_model::add($topic);
        board_model::inc_by_id(array('topic_count' => 1), $topic['board_id']);
        user_model::inc_by_id(array('topic_count' => 1), $topic['user_id']);
        return $topic_id;
    }
}
