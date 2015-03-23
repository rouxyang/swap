<?php

namespace kern;

trait auto_controller {
    public static function index_action() {
        self::puts('hello, i am auto');
    }
}
