<?php
use swap\visitor;
use swap\binder;
class nav_helper extends swap\helper {
    public static function run(array $context) {
        self::set('categories', category_model::get_all());
        self::set('tags', tag_model::get_all(array(array('refer_count' => 'DESC'), 1, 10)));
        $comments = comment_model::get_all(array(array('id' => 'DESC'), 1, 10));
        binder::bind($comments, 'belongs_to', 'post');
        self::set('comments', $comments);
        self::set('links', link_model::get_all());
        self::render();
    }
}
