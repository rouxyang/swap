<?php
/**
 * 基于 Redis（通过 Redis 驱动扩展）的会话存储源
 *
 * @copyright Copyright (c) 2009-2014 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace swap;
use Redis;
if (!extension_loaded('Redis')) throw new environment_error('cannot use redis_store: Redis extension does not exist');
redis_session_store::__init__();
/**
 * [类型] redis 会话存储源
 */
class /* @swap */ redis_session_store extends session_store {
    public function __construct($dsn) {
    }
    public function is_role_id_online($role_id) {
    }
    public function online_count() {
    }
    public function clean() {
    }
    public function fetch($sid) {
    }
    public function modify($sid, $expire_time, $last_active, $role_id, $role_secret, array $role_vars) {
    }
    public function create($sid, $expire_time, $last_active, $role_id, $role_secret, array $role_vars) {
    }
    public function remove($sid) {
    }
    protected $redis = null;
    public static function __init__() {
    }
}
