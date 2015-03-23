<?php
use kern\visitor;
class setting_controller extends kern\controller {
    public static function index_action() {
        self::show_page('', 'admin/main');
    }
}
