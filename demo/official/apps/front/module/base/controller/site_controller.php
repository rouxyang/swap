<?php
use kern\visitor;
class site_controller extends kern\controller {
    public static $layout = 'default';
    public static function index_action() {
        self::show_page();
    }
    public static function about_action() {
        self::show_page();
    }
    public static function download_action() {
        self::show_page();
    }
    public static function demo_action() {
        self::show_page();
    }
    public static function doc_action() {
        self::show_page();
    }
    public static function support_action() {
        self::show_page();
    }
    public static function license_action() {
        self::show_page();
    }
}
