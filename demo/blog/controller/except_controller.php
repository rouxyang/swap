<?php
use kern\visitor;
class except_controller extends kern\controller {
    public static function access_denied_action($e) {
        visitor::set_status(403, 'Forbidden');
        self::show_page('', false);
    }
    public static function target_missing_action($e) {
        visitor::set_status(404, 'Not Found');
        self::show_page('', false);
    }
    public static function browser_denied_action($e) {
        visitor::set_status(406, 'Browser Not Allowed');
        self::set('msg', $e->getMessage());
        self::show_page('', false);
    }
}
