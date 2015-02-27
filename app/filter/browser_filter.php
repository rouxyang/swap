<?php
use swap\visitor;
class browser_filter extends swap\before_filter {
    public static function run(array $denied_browsers) {
        foreach ($denied_browsers as $browser) {
            $checker = 'is_' . $browser;
            if (visitor::$checker()) {
                self::forward_406('您的浏览器太古老，请升级您的浏览器再访问本站。');
            }
        }
    }
}
