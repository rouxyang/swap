<?php
use swap\setting;
class demo_service {
    public static function get_message() {
        return setting::get_logic('demo');
    }
}
