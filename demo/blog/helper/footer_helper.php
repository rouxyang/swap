<?php
class footer_helper extends swap\helper {
    public static function run(array $context) {
        self::set('settings', setting_service::get_settings());
        self::render();
    }
}
