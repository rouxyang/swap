<?php
/**
 * 对称加密器
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
/**
 * [实体] 对称加密解密器
 */
class crypt {
    public static function encrypt($data, $key) {
        return self::rc4($data, $key);
    }
    public static function decrypt($data, $key) {
        return self::rc4($data, $key);
    }
    protected static function rc4($data, $key) {
        $s = range(0, 255);
        $j = 0;
        $key_len = strlen($key);
        for ($i = 0; $i < 256; $i++) {
            $j = ($j + $s[$i] + ord($key[$i % $key_len])) % 256;
            $t = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $t;
        }
        $i = $j = 0;
        for ($k = 0, $data_len = strlen($data); $k < $data_len; $k++) {
            $i = ($i + 1) % 256;
            $j = ($j + $s[$i]) % 256;
            $t = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $t;
            $data[$k] = chr(ord($data[$k]) ^ $s[($s[$i] + $s[$j]) % 256]);
        }
        return $data;
    }
}
