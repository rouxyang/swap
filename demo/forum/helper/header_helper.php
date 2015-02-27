<?php
use swap\visitor;
class header_helper extends swap\helper {
    public static function run(array $context) {
        $logined = visitor::has_role('user');
        self::set('logined', $logined);
        if ($logined) {
            self::set('user', user_model::get_by_id(visitor::get_role_id('user')));
        }
        self::render();
    }
}
