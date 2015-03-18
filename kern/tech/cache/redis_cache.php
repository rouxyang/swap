<?php
namespace kern;
use Redis;
if (!extension_loaded('Redis')) throw new environment_error('cannot use redis_cache: Redis extension does not exist');
redis_cache::__init__();
/**
 * [类型]
 */
class /* @kern */ redis_cache extends cache {
    public function __construct(array $dsns) {
    }
    public function get($key, $default_value = null) {
    }
    public function get_many(array $keys, $default_value = null) {
    }
    public function set($key, $value, $seconds = self::forever) {
    }
    public function set_many(array $keyvalues, $seconds = self::forever) {
    }
    public function has($key) {
    }
    public function del($key) {
    }
    public function del_many(array $keys) {
    }
    public function clear() {
    }
    protected $redis = null;
    public static function __init__() {
    }
}
