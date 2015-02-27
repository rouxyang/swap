<?php
use swap\config;
class demo_service {
    public static function get_message() {
        return config::get_logic('demo');
    }
}
