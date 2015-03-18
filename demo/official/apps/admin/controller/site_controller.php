<?php
use kern\visitor;
class site_controller extends kern\controller {
    public static function index_action() {
        self::show_page('', false);
    }
}
