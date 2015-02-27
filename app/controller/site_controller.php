<?php
use swap\visitor;
class site_controller extends swap\controller {
    public static function index_action() {
        self::show_page();
    }
    public static function demo_action() {
        self::puts(demo_service::get_message());
    }
}
