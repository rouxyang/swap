<?php
/**
 * 基于 memcached（通过 Memcache 驱动扩展）的会话存储源
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
use Memcache;
if (!extension_loaded('Memcache')) throw new environment_error('cannot use memcached_store: Memcache extension does not exist');
memcached_session_store::__init__();
/**
 * [类型] memcached 会话存储源
 */
class /* @kern */ memcached_session_store extends session_store {
    public function __construct($dsn) {
        $memcache = new Memcache();
        $dsns = is_array($dsn) ? $dsn : array($dsn);
        foreach ($dsns as $dsn) {
            $url_parts = parse_url($dsn);
            if (!$memcache->addServer($url_parts['host'], $url_parts['port'], false)) {
                throw new server_except("cannot connect to dsn: '{$dsn}'");
            }
        }
        $this->memcache = $memcache;
    }
    public function is_role_id_online($role_id) {
        $value = $this->memcache->get($role_id, 0);
        if (framework::is_debug()) {
            debug::save('session', 'memcache get: key -> ' . $role_id . ', value -> ' . var_export($value, true));
        }
        return $value !== false;
    }
    public function online_count() {
        // @todo: implementation
    }
    public function clean() {
        // 啥也不用做，memcached 会自动让记录过期
    }
    public function fetch($sid) {
        $record = $this->memcache->get($sid);
        if (framework::is_debug()) {
            debug::save('session', 'memcache get: key -> ' . $sid . ', value -> ' . var_export($record, true));
        }
        if ($record === false) {
            return null;
        }
        return array(
            'sid' => $sid,
            'expire_time' => $record['expire_time'],
            'last_active' => $record['last_active'],
            'role_id' => $record['role_id'],
            'role_secret' => $record['role_secret'],
            'role_vars' => $record['role_vars'],
        );
    }
    public function modify($sid, $expire_time, $last_active, $role_id, $role_secret, array $role_vars) {
        $this->create($sid, $expire_time, $last_active, $role_id, $role_secret, $role_vars);
    }
    public function create($sid, $expire_time, $last_active, $role_id, $role_secret, array $role_vars) {
        $record = array(
            'expire_time' => $expire_time,
            'last_active' => $last_active,
            'role_id' => $role_id,
            'role_secret' => $role_secret,
            'role_vars' => $role_vars,
        );
        $seconds = $expire_time - $last_active;
        if ($seconds > 2592000) {
            $seconds = 2592000;
        }
        $this->memcache->set($sid, $record, 0, $seconds);
        $this->memcache->add($role_id, 0, 0, $seconds);
        $this->memcache->increment($role_id, 1);
        if (framework::is_debug()) {
            debug::save('session', 'memcache set: key -> ' . $sid . ', value -> ' . var_export($record, true));
            debug::save('session', 'memcache add: key -> ' . $role_id . ', value -> 0');
            debug::save('session', 'memcache increment: key -> ' . $role_id . ', value -> 1');
        }
    }
    public function remove($sid) {
        $record = $this->memcache->get($sid);
        if (framework::is_debug()) {
            debug::save('session', 'memcache get: key -> ' . $sid . ', value -> ' . var_export($record, true));
        }
        if ($record === false) {
            return;
        }
        $role_id = $record['role_id'];
        $this->memcache->delete($sid);
        $role_id_online_count = $this->memcache->decrement($role_id, 1);
        $needs_delete = $role_id_online_count !== false && $role_id_online_count <= 0;
        if ($needs_delete) {
            $this->memcache->delete($role_id);
        }
        if (framework::is_debug()) {
            debug::save('session', 'memcache del: key -> ' . $sid);
            debug::save('session', 'memcache decrement: key -> ' . $role_id);
            if ($needs_delete) {
                debug::save('session', 'memcache del: key -> ' . $role_id);
            }
        }
    }
    protected $memcache = null;
    public static function __init__() {
        ini_set('memcache.hash_strategy', 'consistent');
    }
}
