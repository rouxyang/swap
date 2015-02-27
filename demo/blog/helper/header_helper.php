<?php
use swap\visitor;
class header_helper extends swap\helper {
    public static function run(array $context) {
        self::set('logined', visitor::has_role('member'));
        self::set('settings', setting_service::get_settings());
        self::render();
    }
}
