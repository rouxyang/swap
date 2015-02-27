<?php
use swap\visitor;
class setting_controller extends swap\controller {
    public static function index_action() {
        self::show_page('', 'admin/main');
    }
}
