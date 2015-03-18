<?php
/**
 * HTML 相关操作
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
/**
 * [类型] 可进行 HTML 转义的
 */
interface html_escapable {
    function html_escape();
    function html_unescape();
}
/**
 * [实体] HTML
 */
class html {
    public static function escape($value) {
        if (is_array($value)) {
            $escaped_value = [];
            foreach ($value as $k => $v) {
                $escaped_value[is_numeric($k) ? $k : htmlentities($k, ENT_QUOTES, 'UTF-8')] = self::escape($v);
            }
        } else if (is_string($value)) {
            $escaped_value = htmlentities($value, ENT_QUOTES, 'UTF-8');
        } else if ($value instanceof html_escapable) {
            $escaped_value = $value->html_escape();
        } else {
            $escaped_value = $value;
        }
        return $escaped_value;
    }
    public static function unescape($value) {
        if (is_array($value)) {
            $unescaped_value = [];
            foreach ($value as $k => $v) {
                $unescaped_value[is_numeric($k) ? $k : html_entity_decode($k, ENT_QUOTES, 'UTF-8')] = self::unescape($v);
            }
        } else if (is_string($value)) {
            $unescaped_value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
        } else if ($value instanceof html_escapable) {
            $unescaped_value = $value->html_unescape();
        } else {
            $unescaped_value = $value;
        }
        return $unescaped_value;
    }
    public static function purify($str) {
        // @todo
    }
}
