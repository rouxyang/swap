<?php
/**
 * 脚手架
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
// [实体]
trait auto_controller {
    public static function index_action() {
        self::puts('hello, i am auto');
    }
}
