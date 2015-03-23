<?php

namespace kern;

trait admin_controller {
    public static function index_action() {
        self::puts('hello, i am admin');
    }
}
