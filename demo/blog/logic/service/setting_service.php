<?php
class setting_service {
    public static function get_settings() {
        static $settings = null;
        if ($settings === null) {
            $settings = setting_model::get_all();
        }
        return $settings;
    }
}
