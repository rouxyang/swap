<?php
use kern\visitor;
class except_controller extends kern\controller {
    public static function access_denied_action($e) {
        self::show_page('', false);
    }
    public static function target_missing_action($e) {
        self::show_page('', false);
    }
}
