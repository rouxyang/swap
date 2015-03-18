<?php
/**
 * Memcached 缓存
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
use Memcache;
if (!extension_loaded('Memcache')) throw new environment_error('cannot use memcached_cache: Memcache extension does not exist');
memcached_cache::__init__();
/**
 * [类型]
 */
class /* @kern */ memcached_cache extends cache {
    public function __construct(array $dsns) {
        $memcache = new Memcache();
        foreach ($dsns as $dsn) {
            $url_parts = parse_url($dsn);
            if (!$memcache->addServer($url_parts['host'], $url_parts['port'])) {
                throw new server_except("cannot connect to dsn: '{$dsn}'");
            }
        }
        $this->memcache = $memcache;
    }
    public function get($key, $default_value = null) {
        $value = $this->memcache->get($key);
        if ($value === false) {
            return $default_value;
        }
        return $value;
    }
    public function get_many(array $keys, $default_value = null) {
        $keyvalues = $this->memcache->get($keys);
        foreach ($keys as $key) {
            if (!array_key_exists($key, $keyvalues)) {
                $keyvalues[$key] = $default_value;
            }
        }
        return $keyvalues;
    }
    public function set($key, $value, $seconds = self::forever) {
        return $this->memcache->set($key, $value, 0, $seconds);
    }
    public function set_many(array $keyvalues, $seconds = self::forever) {
        $result = true;
        foreach ($keyvalues as $key => $value) {
            if (!$this->set($key, $value, $seconds)) {
                $result = false;
            }
        }
        return $result;
    }
    public function has($key) {
        return $this->memcache->get($key) !== false;
    }
    public function del($key) {
        return $this->memcache->delete($key);
    }
    public function del_many(array $keys) {
        $result = true;
        foreach ($keys as $key) {
            if (!$this->del($key)) {
                $result = false;
            }
        }
        return $result;
    }
    public function clear() {
        return $this->memcache->flush();
    }
    protected $memcache = null;
    public static function __init__() {
        ini_set('memcache.hash_strategy', 'consistent');
    }
}
