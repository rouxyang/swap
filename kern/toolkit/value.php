<?php
/**
 * 值类型
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
//  [类型] 值
abstract class value {
    abstract public function is_valid();
    const email_type = 'email';
    const ip_type = 'ip';
    const url_type = 'url';
    const dsn_type = 'dsn';
    const date_type = 'date';
    const time_type = 'time';
    const mobile_type = 'mobile';
    public function __construct($value) {
        $this->value = $value;
    }
    protected $value = null;
}
// [类型] URL 值
class url_value extends value {
    public function is_valid() {
        return self::is_valid_url($this->value);
    }
    public static function is_valid_url($url) {
        return false;
    }
}
// [类型] 时间值
class time_value extends value {
    public function is_valid() {
        return self::is_valid_time($this->value);
    }
    public static function is_valid_time($time) {
        return false;
    }
}
// [类型] 手机号值
class mobile_value extends value {
    public function is_valid() {
        return self::is_valid_mobile($this->value);
    }
    public static function is_valid_mobile($mobile) {
        return preg_match('/^1[\d]{10}$/', $mobile);
    }
}
// [类型] IP 地址值
class ip_value extends value {
    public function is_valid() {
        return self::is_valid_ip($this->value);
    }
    public static function is_valid_ip($ip) {
        $long = ip2long($ip);
        if ($long === false) {
            return false;
        }
        return long2ip($long) === $ip;
    }
}
// [类型] 数据源值
class dsn_value extends value {
    const separator = '://';
    public function is_valid() {
        return self::is_valid_dsn($this->value);
    }
    public function __construct($value) {
        parent::__construct($value);
        if ($this->is_valid()) {
            list($this->scheme, $this->detail) = explode(self::separator, $value, 2);
        }
    }
    public function get_scheme() {
        return $this->scheme;
    }
    public function get_detail() {
        return $this->detail;
    }
    public static function is_valid_dsn($dsn) {
        return is_string($dsn) && strpos($dsn, self::separator) !== false;
    }
    protected $scheme = '';
    protected $detail = '';
}
// [类型] 邮箱地址值
class email_value extends value {
    public function is_valid() {
        return self::is_valid_email($this->value);
    }
    public static function is_valid_email($email) {
        $pattern = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';
        return preg_match($pattern, $email);
    }
}
