<?php
use kern\visitor;
class site_controller extends kern\controller {
    public static function index_action() {
        self::set_title('欢迎使用 Swap');
        self::show_page();
    }
}
