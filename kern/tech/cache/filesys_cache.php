<?php
/**
 * 文件系统缓存
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
use Exception;
filesys_cache::__init__();
// [类型]
class /* @kern */ filesys_cache extends cache {
    const max_expire = 2147483647;
    public function __construct(array $dsns) {
        list(, $this->cache_dir) = explode('://', current($dsns), 2);
    }
    public function get($key, $default_value = null) {
        $cache_file = $this->get_cache_file_from_key($key);
        $data = @file_get_contents($cache_file);
        if (kernel::is_debug()) {
            debug::save('cache', 'filesys get: file -> ' . $cache_file . ', data -> ' . var_export($data, true));
        }
        if ($data === false) {
            return $default_value;
        }
        $expire = $this->fetch_expire($data);
        if ($expire >= clock::get_stamp()) {
            try {
                return $this->fetch_value($data);
            } catch (Exception $e) {}
        } else {
            @unlink($cache_file);
        }
        return $default_value;
    }
    public function get_many(array $keys, $default_value = null) {
    }
    public function set($key, $value, $seconds = self::forever) {
        $cache_file = $this->get_cache_file_from_key($key);
        if ($seconds === self::forever) {
            $expire = self::max_expire;
        } else {
            $expire = clock::get_stamp() + $seconds;
            if ($expire > self::max_expire) {
                $expire = self::max_expire;
            }
        }
        $data = $this->build_data($value, $expire);
        if (kernel::is_debug()) {
            debug::save('cache', 'filesys set: file -> ' . $cache_file . ', data -> ' . var_export($data, true));
        }
        return @file_put_contents($cache_file, $data, LOCK_EX) !== false;
    }
    public function set_many(array $keyvalues, $seconds = self::forever) {
    }
    public function has($key) {
        $cache_file = $this->get_cache_file_from_key($key);
        if (kernel::is_debug()) {
            debug::save('cache', 'filesys has: file -> ' . $cache_file);
        }
        return file_exists($cache_file);
    }
    public function del($key) {
        $cache_file = $this->get_cache_file_from_key($key);
        if (kernel::is_debug()) {
            debug::save('cache', 'filesys del: file -> ' . $cache_file);
        }
        return @unlink($cache_file);
    }
    public function del_many(array $keys) {
    }
    public function clear() {
    }
    protected function fetch_expire($data) {
        return (int)substr($data, 0, 10);
    }
    protected function fetch_value($data) {
        $value = @unserialize(substr($data, 10));
        if ($value === false) {
            throw new Exception();
        }
        return $value;
    }
    protected function build_data($value, $expire) {
        return $expire . serialize($value);
    }
    protected function get_cache_file_from_key($key) {
        return $this->cache_dir . '/' . md5(self::$secret_key . $key) . '.cache';
    }
    protected $cache_dir = '';
    public static function __init__() {
        self::$secret_key = config::get_kern('secret_key', '');
    }
    protected static $secret_key = '';
}
