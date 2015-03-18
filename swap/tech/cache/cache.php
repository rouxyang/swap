<?php
/**
 * 缓存处理
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
/**
 * [类型] 缓存源
 */
abstract class cache {
    const forever = 0;
    abstract public function __construct(array $dsns);
    abstract public function get($key, $default_value = null);
    abstract public function get_many(array $keys, $default_value = null);
    abstract public function set($key, $value, $seconds = self::forever);
    abstract public function set_many(array $keyvalues, $seconds = self::forever);
    abstract public function has($key);
    abstract public function del($key);
    abstract public function del_many(array $keys);
    abstract public function clear();
}
/**
 * [实体] 缓存池
 */
class cache_pool {
    public static function get_cache($cache_name) {
        static $caches = [];
        if (!array_key_exists($cache_name, $caches)) {
            $dsns = setting::get_logic('cache.' . $cache_name);
            list($cache_type, ) = explode('://', current($dsns), 2);
            $cache_class = __NAMESPACE__ . '\\' . $cache_type . '_cache';
            $cache = new $cache_class($dsns);
            $caches[$cache_name] = $cache;
        }
        return $caches[$cache_name];
    }
}
