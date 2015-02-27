<?php
use swap\visitor;
class except_controller extends swap\controller {
    public static function access_denied_action($e) {
        visitor::set_status(403, 'Forbidden');
        self::show_page('', false);
    }
    public static function target_missing_action($e) {
        visitor::set_status(404, 'Not Found');
        self::show_page('', false);
    }
    public static function method_denied_action($e) {
        visitor::set_status(405, 'Method Not Allowed');
        self::show_page('', false);
    }
    public static function browser_denied_action($e) {
        visitor::set_status(406, 'Browser Not Allowed');
        self::show_page('', false);
    }
    /*
    public static function server_except_action($e) {
        visitor::set_status(500, 'Internal Server Error');
        self::show_page('', false);
    }
    */
}
