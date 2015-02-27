<?php
class comment_service {
    public static function delete($comment_id) {
        $comment = comment_model::get_by_id($comment_id);
        if ($comment !== null) {
            comment_model::del_by_id($comment_id);
            post_model::dec_by_id(array('comment_count' => 1), $comment->post_id);
        }
    }
}
