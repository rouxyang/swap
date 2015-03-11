<?php
use swap\visitor;
class site_controller extends swap\controller {
    public static function index_action() {
        self::send_json('api module');
    }
}
